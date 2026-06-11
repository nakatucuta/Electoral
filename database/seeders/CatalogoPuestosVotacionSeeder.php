<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoPuestosVotacionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = json_decode(file_get_contents(database_path('seeders/data/catalogo_puestos_votacion.json')), true, 512, JSON_THROW_ON_ERROR);
        $timestamp = now();
        $normalized = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $departamento = trim((string) ($row['departamento'] ?? ''));
            $municipio = trim((string) ($row['municipio'] ?? ''));
            $puesto = trim((string) ($row['puesto'] ?? ''));
            $comuna = trim((string) ($row['comuna'] ?? ''));
            $direccion = trim((string) ($row['direccion'] ?? ''));
            $hayEncabezado = collect([$departamento, $municipio, $puesto, $comuna, $direccion])
                ->map(fn (string $value) => mb_strtolower($value))
                ->intersect(['departamento', 'municipio', 'puesto', 'comuna', 'dirección', 'direccion'])
                ->isNotEmpty();

            if ($departamento === '' || $municipio === '' || $puesto === '' || $hayEncabezado) {
                continue;
            }

            $key = implode('|', [$departamento, $municipio, $puesto, $comuna, $direccion]);

            $normalized[$key] = [
                'departamento' => $departamento,
                'municipio' => $municipio,
                'puesto' => $puesto,
                'comuna' => $comuna,
                'direccion' => $direccion,
            ];
        }

        foreach (array_chunk(array_values($normalized), 500) as $chunk) {
            $payload = array_map(static function (array $row) use ($timestamp): array {
                return $row + [
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }, $chunk);

            DB::table('catalogo_puestos_votacion')->upsert(
                $payload,
                ['departamento', 'municipio', 'puesto', 'comuna', 'direccion'],
                ['departamento', 'municipio', 'puesto', 'comuna', 'direccion', 'updated_at']
            );
        }
    }
}
