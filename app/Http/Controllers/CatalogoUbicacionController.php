<?php

namespace App\Http\Controllers;

use App\Models\CatalogoPuestoVotacion;
use App\Models\GeoDepartamento;
use App\Models\GeoMunicipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogoUbicacionController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $field = (string) $request->string('field');
        $term = trim((string) $request->string('term'));
        $departamento = trim((string) $request->string('departamento'));
        $municipio = trim((string) $request->string('municipio'));

        $items = match ($field) {
            'departamento' => $this->buscarDepartamentos($term),
            'municipio' => $this->buscarMunicipios($term, $departamento),
            'puesto_votacion' => $this->buscarPuestos($term, $departamento, $municipio),
            'comuna' => $this->buscarComunas($term, $departamento, $municipio),
            'direccion' => $this->buscarDirecciones($term, $departamento, $municipio),
            default => collect(),
        };

        return response()->json([
            'items' => $items,
        ]);
    }

    private function buscarDepartamentos(string $term)
    {
        return GeoDepartamento::query()
            ->when($term !== '', fn ($query) => $query->where('nombre', 'like', "%{$term}%"))
            ->orderBy('nombre')
            ->limit(12)
            ->pluck('nombre')
            ->values();
    }

    private function buscarMunicipios(string $term, string $departamento)
    {
        return GeoMunicipio::query()
            ->when($departamento !== '', fn ($query) => $query->where('departamento_nombre', 'like', "%{$departamento}%"))
            ->when($term !== '', fn ($query) => $query->where('nombre', 'like', "%{$term}%"))
            ->orderBy('nombre')
            ->limit(12)
            ->pluck('nombre')
            ->values();
    }

    private function buscarPuestos(string $term, string $departamento, string $municipio)
    {
        return CatalogoPuestoVotacion::query()
            ->when($departamento !== '', fn ($query) => $query->where('departamento', 'like', "%{$departamento}%"))
            ->when($municipio !== '', fn ($query) => $query->where('municipio', 'like', "%{$municipio}%"))
            ->when($term !== '', fn ($query) => $query->where('puesto', 'like', "%{$term}%"))
            ->distinct()
            ->orderBy('puesto')
            ->limit(12)
            ->pluck('puesto')
            ->values();
    }

    private function buscarComunas(string $term, string $departamento, string $municipio)
    {
        return CatalogoPuestoVotacion::query()
            ->when($departamento !== '', fn ($query) => $query->where('departamento', 'like', "%{$departamento}%"))
            ->when($municipio !== '', fn ($query) => $query->where('municipio', 'like', "%{$municipio}%"))
            ->when($term !== '', fn ($query) => $query->where('comuna', 'like', "%{$term}%"))
            ->distinct()
            ->orderBy('comuna')
            ->limit(12)
            ->pluck('comuna')
            ->values();
    }

    private function buscarDirecciones(string $term, string $departamento, string $municipio)
    {
        return CatalogoPuestoVotacion::query()
            ->when($departamento !== '', fn ($query) => $query->where('departamento', 'like', "%{$departamento}%"))
            ->when($municipio !== '', fn ($query) => $query->where('municipio', 'like', "%{$municipio}%"))
            ->when($term !== '', fn ($query) => $query->where('direccion', 'like', "%{$term}%"))
            ->distinct()
            ->orderBy('direccion')
            ->limit(12)
            ->pluck('direccion')
            ->values();
    }
}
