<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVotanteRequest;
use App\Http\Requests\UpdateVotanteRequest;
use App\Models\VotanteAudit;
use App\Models\Votante;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VotanteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Votante::class, 'votante');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        [$filters, $query] = $this->buildFilteredQuery($request);
        $summaryQuery = clone $query;
        $votantes = $query->latest()->paginate(10)->withQueryString();
        $confirmedCount = (clone $summaryQuery)->whereNotNull('foto_certificado')->where('foto_certificado', '!=', '')->count();
        $pendingCount = (clone $summaryQuery)->where(function ($inner) {
            $inner->whereNull('foto_certificado')->orWhere('foto_certificado', '');
        })->count();

        return view('votantes.index', [
            'votantes' => $votantes,
            'filters' => $filters,
            'totales' => [
                'visibles' => (clone $summaryQuery)->count(),
                'confirmados' => $confirmedCount,
                'pendientes' => $pendingCount,
            ],
            'notificationToasts' => $this->notificationToastsForUser($user),
            'responsables' => $this->responsablesOptions($user),
            'departamentos' => $this->distinctOptions('departamento', $user),
            'municipios' => $this->distinctOptions('municipio', $user),
            'puestos' => $this->distinctOptions('puesto_votacion', $user),
            'mesas' => $this->distinctOptions('mesa_votacion', $user),
            'relaciones' => $this->distinctOptions('relacion', $user),
        ]);
    }

    public function exportExcel(Request $request)
    {
        [, $query] = $this->buildFilteredQuery($request);
        $rows = $this->exportRows($query->with('user')->latest()->get());

        $filename = 'votantes_' . now()->format('Ymd_His') . '.xlsx';
        $path = tempnam(sys_get_temp_dir(), 'votantes_');

        if ($path === false) {
            abort(500, 'No se pudo preparar el archivo de exportacion.');
        }

        $xlsxPath = $path . '.xlsx';
        @unlink($path);

        $this->buildXlsxFile($rows, $xlsxPath);

        return response()->download($xlsxPath, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf(Request $request)
    {
        [$filters, $query] = $this->buildFilteredQuery($request);
        $votantes = $query->with('user')->latest()->get();

        $pdf = Pdf::loadView('votantes.pdf', [
            'votantes' => $votantes,
            'filters' => $filters,
            'generatedAt' => now(),
            'total' => $votantes->count(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('votantes_' . now()->format('Ymd_His') . '.pdf');
    }

    public function create(): View
    {
        return view('votantes.create');
    }

    public function checkNumeroIdentificacion(Request $request): JsonResponse
    {
        $numero = trim((string) $request->string('numero'));
        $ignore = $request->integer('ignore');

        $query = Votante::query()->where('numero_identificacion', $numero);

        if ($ignore > 0) {
            $query->whereKeyNot($ignore);
        }

        $votante = $query->with('user')->first();

        return response()->json([
            'exists' => (bool) $votante,
            'message' => $votante
                ? 'Este número de identificación ya está registrado en la plataforma.'
                : 'Número disponible.',
            'owner' => $votante?->user?->name,
        ]);
    }

    public function store(StoreVotanteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_certificado')) {
            $data['foto_certificado'] = $request->file('foto_certificado')->store('votantes/certificados', 'public');
        }

        $votante = $request->user()->votantes()->create($data);
        $this->recordAudit($votante, $request->user(), 'created', 'Votante registrado', [
            'estado' => $votante->estado_registro_label,
            'numero_identificacion' => $votante->numero_identificacion,
        ]);

        return redirect()
            ->route('votantes.index')
            ->with('flash.banner', 'Votante registrado correctamente.');
    }

    public function show(Votante $votante): View
    {
        $votante->loadMissing('user', 'audits.user');

        return view('votantes.show', compact('votante'));
    }

    public function edit(Votante $votante): View
    {
        $votante->loadMissing('user');

        return view('votantes.edit', compact('votante'));
    }

    public function update(UpdateVotanteRequest $request, Votante $votante): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto_certificado')) {
            if ($votante->foto_certificado) {
                Storage::disk('public')->delete($votante->foto_certificado);
            }

            $data['foto_certificado'] = $request->file('foto_certificado')->store('votantes/certificados', 'public');
        }

        $votante->update($data);
        $this->recordAudit($votante, $request->user(), 'updated', 'Datos del votante actualizados', [
            'estado' => $votante->estado_registro_label,
            'numero_identificacion' => $votante->numero_identificacion,
        ]);

        return redirect()
            ->route('votantes.index')
            ->with('flash.banner', 'Votante actualizado correctamente.');
    }

    public function uploadCertificado(Request $request, Votante $votante): RedirectResponse
    {
        $this->authorize('update', $votante);

        $data = $request->validate([
            'foto_certificado' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/heic,image/heif', 'max:12288'],
        ], [
            'foto_certificado.required' => 'Debes seleccionar una imagen del certificado.',
            'foto_certificado.file' => 'El archivo del certificado no es válido.',
            'foto_certificado.mimetypes' => 'El certificado debe ser una imagen en formato JPG, PNG, GIF, WEBP, BMP, HEIC o HEIF.',
            'foto_certificado.max' => 'La imagen supera el límite permitido de 12 MB.',
        ]);

        if ($votante->foto_certificado) {
            Storage::disk('public')->delete($votante->foto_certificado);
        }

        $path = $data['foto_certificado']->store('votantes/certificados', 'public');

        $votante->update([
            'foto_certificado' => $path,
        ]);

        $this->recordAudit($votante, $request->user(), 'certificate_uploaded', 'Certificado cargado o reemplazado', [
            'path' => $path,
            'estado' => $votante->estado_registro_label,
        ]);

        return back()->with('flash.banner', 'Certificado de votacion cargado correctamente.');
    }

    public function destroy(Votante $votante): RedirectResponse
    {
        if ($votante->foto_certificado) {
            Storage::disk('public')->delete($votante->foto_certificado);
        }

        $this->recordAudit($votante, auth()->user(), 'deleted', 'Votante eliminado', [
            'numero_identificacion' => $votante->numero_identificacion,
        ]);

        $votante->delete();

        return redirect()
            ->route('votantes.index')
            ->with('flash.banner', 'Votante eliminado correctamente.');
    }

    /**
     * @return array{0: array<string, mixed>, 1: Builder}
     */
    private function buildFilteredQuery(Request $request): array
    {
        $user = $request->user();

        $filters = [
            'search' => trim((string) $request->string('search')),
            'responsable' => (string) $request->string('responsable'),
            'relacion' => trim((string) $request->string('relacion')),
            'departamento' => trim((string) $request->string('departamento')),
            'municipio' => trim((string) $request->string('municipio')),
            'puesto_votacion' => trim((string) $request->string('puesto_votacion')),
            'mesa_votacion' => trim((string) $request->string('mesa_votacion')),
            'estado' => (string) $request->string('estado'),
            'fecha_desde' => (string) $request->string('fecha_desde'),
            'fecha_hasta' => (string) $request->string('fecha_hasta'),
        ];

        $query = Votante::query()->with('user');

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        } elseif ($filters['responsable'] !== '') {
            $query->where('user_id', $filters['responsable']);
        }

        $query->when($filters['search'] !== '', function ($query) use ($filters) {
            $query->where(function ($inner) use ($filters) {
                $inner->where('nombres', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('apellidos', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('numero_identificacion', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('telefono', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('departamento', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('municipio', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('puesto_votacion', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('comuna', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('direccion', 'like', '%' . $filters['search'] . '%');
            });
        });

        foreach (['departamento', 'municipio', 'puesto_votacion', 'mesa_votacion'] as $field) {
            if ($filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }

        if ($filters['relacion'] !== '') {
            $query->where('relacion', $filters['relacion']);
        }

        if ($filters['estado'] === 'confirmado') {
            $query->whereNotNull('foto_certificado')->where('foto_certificado', '!=', '');
        } elseif ($filters['estado'] === 'pendiente') {
            $query->where(function ($inner) {
                $inner->whereNull('foto_certificado')->orWhere('foto_certificado', '');
            });
        }

        if ($filters['fecha_desde'] !== '') {
            $query->whereDate('created_at', '>=', $filters['fecha_desde']);
        }

        if ($filters['fecha_hasta'] !== '') {
            $query->whereDate('created_at', '<=', $filters['fecha_hasta']);
        }

        return [$filters, $query];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function exportRows($votantes): array
    {
        return $votantes->map(function (Votante $votante) {
            return [
                'Responsable' => $votante->user?->name ?? 'Sin dato',
                'Sede' => $votante->user?->sede ?? 'Sin sede',
                'Estado' => $votante->estado_registro_label,
                'Nombres' => $votante->nombres,
                'Apellidos' => $votante->apellidos,
                'Tipo de identificación' => $votante->tipo_identificacion,
                'Número de identificación' => $votante->numero_identificacion,
                'Teléfono' => $votante->telefono ?? 'Sin dato',
                'Departamento' => $votante->departamento ?? 'Sin dato',
                'Municipio' => $votante->municipio ?? 'Sin dato',
                'Puesto de votación' => $votante->puesto_votacion ?? 'Sin dato',
                'Comuna' => $votante->comuna ?? 'Sin dato',
                'Dirección' => $votante->direccion ?? 'Sin dato',
                'Mesa de votación' => $votante->mesa_votacion ?? 'Sin dato',
                'Relación' => $votante->relacion ?? 'Sin dato',
                'Registrado' => optional($votante->created_at)->format('d/m/Y H:i'),
            ];
        })->all();
    }

    /**
     * @param array<int, array<string, string>> $rows
     */
    private function buildXlsxFile(array $rows, string $path): void
    {
        $headers = array_keys($rows[0] ?? []);
        $dataRows = array_map('array_values', $rows);

        $zip = new \ZipArchive();
        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'No se pudo crear el archivo Excel.');
        }

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addFromString('docProps/core.xml', $this->xlsxCoreXml());
        $zip->addFromString('docProps/app.xml', $this->xlsxAppXml());
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addFromString('xl/styles.xml', $this->xlsxStylesXml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml($headers, $dataRows));
        $zip->close();
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, array<int, mixed>> $rows
     */
    private function buildSheetXml(array $headers, array $rows): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml[] = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';
        $xml[] = '<sheetViews><sheetView workbookViewId="0"/></sheetViews>';
        $xml[] = '<sheetFormatPr defaultRowHeight="15"/>';
        $xml[] = '<sheetData>';
        $xml[] = $this->buildRowXml(1, $headers, true);

        foreach ($rows as $index => $row) {
            $xml[] = $this->buildRowXml($index + 2, $row, false);
        }

        $xml[] = '</sheetData>';
        $xml[] = '<pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>';
        $xml[] = '</worksheet>';

        return implode('', $xml);
    }

    /**
     * @param array<int, mixed> $cells
     */
    private function buildRowXml(int $rowNumber, array $cells, bool $header = false): string
    {
        $xml = ['<row r="' . $rowNumber . '">'];

        foreach (array_values($cells) as $colIndex => $value) {
            $cellRef = $this->columnLetter($colIndex + 1) . $rowNumber;
            $style = $header ? ' s="1"' : '';
            $xml[] = '<c r="' . $cellRef . '" t="inlineStr"' . $style . '>';
            $xml[] = '<is><t>' . htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</t></is>';
            $xml[] = '</c>';
        }

        $xml[] = '</row>';

        return implode('', $xml);
    }

    private function columnLetter(int $index): string
    {
        $letter = '';

        while ($index > 0) {
            $index--;
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26);
        }

        return $letter;
    }

    private function xlsxContentTypesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>
XML;
    }

    private function xlsxRootRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>
XML;
    }

    private function xlsxWorkbookXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Votantes" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>
XML;
    }

    private function xlsxWorkbookRelsXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML;
    }

    private function xlsxStylesXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font>
      <sz val="11"/>
      <color theme="1"/>
      <name val="Calibri"/>
      <family val="2"/>
    </font>
    <font>
      <b/>
      <sz val="11"/>
      <color theme="1"/>
      <name val="Calibri"/>
      <family val="2"/>
    </font>
  </fonts>
  <fills count="2">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
  </fills>
  <borders count="1">
    <border>
      <left/><right/><top/><bottom/><diagonal/>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>
  </cellXfs>
  <cellStyles count="1">
    <cellStyle name="Normal" xfId="0" builtinId="0"/>
  </cellStyles>
</styleSheet>
XML;
    }

    private function xlsxCoreXml(): string
    {
        $now = gmdate('Y-m-d\TH:i:s\Z');

        return <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:creator>Electoral</dc:creator>
  <cp:lastModifiedBy>Electoral</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">$now</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">$now</dcterms:modified>
</cp:coreProperties>
XML;
    }

    private function xlsxAppXml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Electoral</Application>
</Properties>
XML;
    }

    /**
     * @return array<int, array{id:int,name:string}>
     */
    private function responsablesOptions($user): array
    {
        if (! $user->isAdmin()) {
            return [];
        }

        return \App\Models\User::query()
            ->whereHas('votantes')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function distinctOptions(string $field, $user): array
    {
        $query = Votante::query();

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->whereNotNull($field)
            ->where($field, '!=', '')
            ->distinct()
            ->orderBy($field)
            ->pluck($field)
            ->values()
            ->all();
    }

    private function notificationToastsForUser($user)
    {
        if ($user->isAdmin()) {
            $employees = \App\Models\User::query()
                ->whereHas('votantes')
                ->orderBy('name')
                ->get();

            $countsByEmployee = Votante::query()
                ->select('user_id')
                ->selectRaw("SUM(CASE WHEN foto_certificado IS NULL OR foto_certificado = '' THEN 1 ELSE 0 END) as votantes_pendientes_count")
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            return $employees
                ->map(function ($employee) use ($countsByEmployee) {
                    $counts = $countsByEmployee->get($employee->id);
                    $pending = (int) ($counts->votantes_pendientes_count ?? 0);

                    if ($pending <= 0) {
                        return null;
                    }

                    return [
                        'id' => 'employee-' . $employee->id,
                        'title' => $employee->name,
                        'message' => $pending . ' votantes siguen pendientes de certificado.',
                        'subtext' => $employee->sede ?? 'Sin sede',
                        'tone' => 'warning',
                        'count' => $pending,
                    ];
                })
                ->filter()
                ->values();
        }

        $pending = Votante::where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNull('foto_certificado')->orWhere('foto_certificado', '');
            })
            ->count();

        if ($pending <= 0) {
            return collect();
        }

        return collect([
            [
                'id' => 'my-pending',
                'title' => 'Tienes certificados pendientes',
                'message' => 'Aún hay votantes tuyos sin certificado cargado.',
                'subtext' => 'Revisa el listado cuando puedas.',
                'tone' => 'warning',
                'count' => $pending,
            ],
        ]);
    }

    private function recordAudit(Votante $votante, $user, string $action, string $title, array $details = []): void
    {
        VotanteAudit::create([
            'votante_id' => $votante->id,
            'user_id' => $user?->id,
            'action' => $action,
            'title' => $title,
            'details' => $details,
        ]);
    }
}
