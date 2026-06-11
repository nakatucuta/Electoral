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

        foreach (array_chunk($rows, 500) as $chunk) {
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
