<?php

use App\Http\Controllers\VotanteController;
use App\Http\Controllers\CatalogoUbicacionController;
use App\Http\Controllers\RoleManagementController;
use App\Models\User;
use App\Models\Votante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

$buildPanelData = function (User $user): array {
    $query = Votante::query()->with('user');
    $confirmedFilter = fn ($query) => $query
        ->whereNotNull('foto_certificado')
        ->where('foto_certificado', '!=', '');
    $pendingFilter = fn ($query) => $query
        ->where(function ($query) {
            $query->whereNull('foto_certificado')
                ->orWhere('foto_certificado', '');
        });

    if (! $user->isAdmin()) {
        $query->where('user_id', $user->id);
    }

    $latestVotantes = (clone $query)->latest()->take(5)->get();
    $confirmedQuery = (clone $query);
    $pendingQuery = (clone $query);
    $employeeNotifications = collect();
    $employeeStats = collect();
    $notificationToasts = collect();
    $pendingNotificationsTotal = 0;
    $topDepartamentos = collect();
    $topMunicipios = collect();
    $dailyTrend = collect();
    $departamentoCount = 0;
    $municipioCount = 0;

    if ($user->isAdmin()) {
        $employeeStats = User::query()
            ->whereHas('votantes')
            ->orderBy('name')
            ->get();

        $countsByEmployee = Votante::query()
            ->select('user_id')
            ->selectRaw("SUM(CASE WHEN foto_certificado IS NOT NULL AND foto_certificado <> '' THEN 1 ELSE 0 END) as votantes_confirmados_count")
            ->selectRaw("SUM(CASE WHEN foto_certificado IS NULL OR foto_certificado = '' THEN 1 ELSE 0 END) as votantes_pendientes_count")
            ->selectRaw('COUNT(*) as votantes_count')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $employeeStats = $employeeStats->map(function ($employee) use ($countsByEmployee) {
            $counts = $countsByEmployee->get($employee->id);
            $employee->votantes_count = (int) ($counts->votantes_count ?? 0);
            $employee->votantes_confirmados_count = (int) ($counts->votantes_confirmados_count ?? 0);
            $employee->votantes_pendientes_count = (int) ($counts->votantes_pendientes_count ?? 0);

            return $employee;
        });

        $employeeNotifications = $employeeStats->where('votantes_pendientes_count', '>', 0)->values();
        $pendingNotificationsTotal = (int) $employeeNotifications->sum('votantes_pendientes_count');
        $notificationToasts = $employeeNotifications->map(function ($employee) {
            return [
                'id' => 'employee-' . $employee->id,
                'title' => $employee->name,
                'message' => $employee->votantes_pendientes_count . ' votantes siguen pendientes de certificado.',
                'subtext' => $employee->sede ?? 'Sin sede',
                'tone' => 'warning',
                'count' => $employee->votantes_pendientes_count,
            ];
        })->values();
    } elseif ($user->votantes()->where(function ($query) {
        $query->whereNull('foto_certificado')->orWhere('foto_certificado', '');
    })->count() > 0) {
        $notificationToasts = collect([
            [
                'id' => 'my-pending',
                'title' => 'Tienes certificados pendientes',
                'message' => 'Aún hay votantes tuyos sin certificado cargado.',
                'subtext' => 'Revisa el panel de pendientes cuando puedas.',
                'tone' => 'warning',
                'count' => $user->votantes()->where(function ($query) {
                    $query->whereNull('foto_certificado')->orWhere('foto_certificado', '');
                })->count(),
            ],
        ]);
    }

    $topDepartamentos = (clone $confirmedQuery)
        ->tap($confirmedFilter)
        ->select('departamento', DB::raw('COUNT(*) as total'))
        ->groupBy('departamento')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->map(function ($item) {
            $item->label = $item->departamento ? trim((string) $item->departamento) : 'Sin dato';
            if ($item->label === '') {
                $item->label = 'Sin dato';
            }

            return $item;
        })
        ->values();

    $topMunicipios = (clone $confirmedQuery)
        ->tap($confirmedFilter)
        ->select('municipio', DB::raw('COUNT(*) as total'))
        ->groupBy('municipio')
        ->orderByDesc('total')
        ->limit(6)
        ->get()
        ->map(function ($item) {
            $item->label = $item->municipio ? trim((string) $item->municipio) : 'Sin dato';
            if ($item->label === '') {
                $item->label = 'Sin dato';
            }

            return $item;
        })
        ->values();

    $dailyTrend = collect(range(6, 0))
        ->map(function ($daysAgo) use ($confirmedQuery) {
            $date = now()->subDays($daysAgo)->startOfDay();

            return [
                'label' => $date->format('d/m'),
                'total' => (clone $confirmedQuery)->tap(fn ($query) => $query
                    ->whereNotNull('foto_certificado')
                    ->where('foto_certificado', '!=', '')
                )->whereDate('created_at', $date->toDateString())->count(),
            ];
        });

    $departamentoCount = (clone $confirmedQuery)
        ->tap($confirmedFilter)
        ->pluck('departamento')
        ->filter()
        ->map(fn ($value) => trim((string) $value))
        ->filter()
        ->unique()
        ->count();

    $municipioCount = (clone $confirmedQuery)
        ->tap($confirmedFilter)
        ->pluck('municipio')
        ->filter()
        ->map(fn ($value) => trim((string) $value))
        ->filter()
        ->unique()
        ->count();

    return [
        'totalVotantes' => (clone $confirmedQuery)->tap($confirmedFilter)->count(),
        'pendingVotantes' => (clone $pendingQuery)->tap($pendingFilter)->count(),
        'pendingNotificationsTotal' => $pendingNotificationsTotal,
        'latestVotantes' => $latestVotantes,
        'myVotantes' => Votante::where('user_id', $user->id)->whereNotNull('foto_certificado')->where('foto_certificado', '!=', '')->count(),
        'myPendingVotantes' => Votante::where('user_id', $user->id)->where(function ($query) {
            $query->whereNull('foto_certificado')->orWhere('foto_certificado', '');
        })->count(),
        'employeeStats' => $employeeStats,
        'employeeNotifications' => $employeeNotifications,
        'notificationToasts' => $notificationToasts,
        'topDepartamentos' => $topDepartamentos,
        'topMunicipios' => $topMunicipios,
        'dailyTrend' => $dailyTrend,
        'departamentoCount' => $departamentoCount,
        'municipioCount' => $municipioCount,
    ];
};

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () use ($buildPanelData) {
    Route::get('/dashboard', function () use ($buildPanelData) {
        return view('dashboard', $buildPanelData(auth()->user()));
    })->name('dashboard');

    Route::get('/estadisticas', function () use ($buildPanelData) {
        $user = auth()->user();

        return view('estadisticas', $buildPanelData($user));
    })->name('estadisticas');

    Route::get('estadisticas/pendientes', function () {
        abort_unless(auth()->user()->isAdmin(), 403);

        $perPage = (int) request()->integer('per_page', 10);
        $perPage = max(5, min($perPage, 25));

        $paginator = Votante::query()
            ->with('user')
            ->where(function ($query) {
                $query->whereNull('foto_certificado')
                    ->orWhere('foto_certificado', '');
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'responsable' => [
                'id' => 0,
                'name' => 'Pendientes globales',
                'email' => '',
                'role' => 'admin',
                'sede' => '',
            ],
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
            'items' => collect($paginator->items())->map(function (Votante $votante) {
                return [
                    'id' => $votante->id,
                    'nombres' => $votante->nombres,
                    'apellidos' => $votante->apellidos,
                    'tipo_identificacion' => $votante->tipo_identificacion,
                    'numero_identificacion' => $votante->numero_identificacion,
                    'telefono' => $votante->telefono,
                    'departamento' => $votante->departamento,
                    'municipio' => $votante->municipio,
                    'puesto_votacion' => $votante->puesto_votacion,
                    'comuna' => $votante->comuna,
                    'direccion' => $votante->direccion,
                    'mesa_votacion' => $votante->mesa_votacion,
                    'relacion' => $votante->relacion,
                    'estado_registro_label' => $votante->estado_registro_label,
                    'created_at' => optional($votante->created_at)->format('d/m/Y H:i'),
                    'responsable_nombre' => $votante->user?->name,
                    'responsable_email' => $votante->user?->email,
                ];
            })->values(),
        ]);
    })->name('estadisticas.pendientes');

    Route::get('responsables/{user}/pendientes', function (User $user) {
        abort_unless(auth()->user()->isAdmin(), 403);

        $perPage = (int) request()->integer('per_page', 10);
        $perPage = max(5, min($perPage, 25));

        $paginator = Votante::query()
            ->where('user_id', $user->id)
            ->where(function ($query) {
                $query->whereNull('foto_certificado')
                    ->orWhere('foto_certificado', '');
            })
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'responsable' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'sede' => $user->sede,
            ],
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
            'items' => collect($paginator->items())->map(function (Votante $votante) {
                return [
                    'id' => $votante->id,
                    'nombres' => $votante->nombres,
                    'apellidos' => $votante->apellidos,
                    'tipo_identificacion' => $votante->tipo_identificacion,
                    'numero_identificacion' => $votante->numero_identificacion,
                    'telefono' => $votante->telefono,
                    'departamento' => $votante->departamento,
                    'municipio' => $votante->municipio,
                    'puesto_votacion' => $votante->puesto_votacion,
                    'comuna' => $votante->comuna,
                    'direccion' => $votante->direccion,
                    'mesa_votacion' => $votante->mesa_votacion,
                    'relacion' => $votante->relacion,
                    'estado_registro_label' => $votante->estado_registro_label,
                    'created_at' => optional($votante->created_at)->format('d/m/Y H:i'),
                ];
            })->values(),
        ]);
    })->name('responsables.pendientes');

    Route::get('votantes/validar-numero-identificacion', [VotanteController::class, 'checkNumeroIdentificacion'])
        ->name('votantes.check-numero');

    Route::get('catalogos/ubicacion/buscar', [CatalogoUbicacionController::class, 'search'])
        ->name('catalogos.ubicacion.search');

    Route::get('usuarios/roles', [RoleManagementController::class, 'index'])
        ->name('users.roles');

    Route::patch('usuarios/{user}/rol', [RoleManagementController::class, 'update'])
        ->name('users.roles.update');

    Route::get('votantes/exportar/excel', [VotanteController::class, 'exportExcel'])
        ->name('votantes.export.excel');

    Route::get('votantes/exportar/pdf', [VotanteController::class, 'exportPdf'])
        ->name('votantes.export.pdf');

    Route::post('votantes/{votante}/certificado', [VotanteController::class, 'uploadCertificado'])
        ->name('votantes.certificado.upload');

    Route::resource('votantes', VotanteController::class);
});
