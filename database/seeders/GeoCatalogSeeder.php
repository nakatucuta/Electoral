<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $basePath = database_path('seeders/data');
        $timestamp = now();

        $departamentos = array_map(static function (array $row) use ($timestamp): array {
            return $row + [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, json_decode(file_get_contents($basePath.'/geo_departamentos.json'), true, 512, JSON_THROW_ON_ERROR));
        $municipios = json_decode(file_get_contents($basePath.'/geo_municipios.json'), true, 512, JSON_THROW_ON_ERROR);

        DB::table('geo_departamentos')->upsert(
            $departamentos,
            ['codigo'],
            ['nombre', 'latitud', 'longitud', 'updated_at']
        );

        DB::table('geo_municipios')->upsert(
            array_map(static function (array $row): array {
                return $row + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $municipios),
            ['codigo'],
            ['departamento_codigo', 'departamento_nombre', 'nombre', 'tipo', 'longitud', 'latitud', 'updated_at']
        );
    }
}
