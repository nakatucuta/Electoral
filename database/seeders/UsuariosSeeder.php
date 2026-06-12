<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuariosSeeder extends Seeder
{
    private const ADMIN_DOCUMENTO = '1118851434';

    private const ADMIN_EMAIL = 'juancamilosuarezcantero@gmail.com';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empleados = json_decode(
            file_get_contents(database_path('seeders/data/empleados_mayo.json')),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        foreach ($empleados as $empleado) {
            if (! is_array($empleado)) {
                continue;
            }

            $documento = preg_replace('/\D+/', '', (string) ($empleado['cedula'] ?? ''));
            $nombre = trim((string) ($empleado['nombre'] ?? ''));
            $sede = trim((string) ($empleado['sede'] ?? ''));

            if ($documento === '' || $nombre === '') {
                continue;
            }

            $esAdmin = $documento === self::ADMIN_DOCUMENTO;

            User::updateOrCreate(
                ['documento_identificacion' => $documento],
                [
                    'name' => $esAdmin ? 'Juan Camilo Suarez Cantero' : $this->formatName($nombre),
                    'email' => $esAdmin ? self::ADMIN_EMAIL : $this->buildEmail($nombre, $documento),
                    'documento_identificacion' => $documento,
                    'sede' => $sede !== '' ? $sede : null,
                    'role' => $esAdmin ? 'admin' : 'employee',
                    'password' => Hash::make($documento),
                    'email_verified_at' => now(),
                ]
            );
        }

        User::updateOrCreate(
            ['documento_identificacion' => self::ADMIN_DOCUMENTO],
            [
                'name' => 'Juan Camilo Suarez Cantero',
                'email' => self::ADMIN_EMAIL,
                'documento_identificacion' => self::ADMIN_DOCUMENTO,
                'sede' => 'MAICAO',
                'role' => 'admin',
                'password' => Hash::make(self::ADMIN_DOCUMENTO),
                'email_verified_at' => now(),
            ]
        );
    }

    private function formatName(string $value): string
    {
        $value = preg_replace('/\s+/', ' ', trim($value)) ?? trim($value);

        return Str::title(mb_strtolower($value, 'UTF-8'));
    }

    private function buildEmail(string $name, string $documento): string
    {
        $slug = Str::slug($name, '.');

        if ($slug === '') {
            $slug = 'empleado';
        }

        return Str::lower($slug . '.' . $documento . '@electoral.local');
    }
}
