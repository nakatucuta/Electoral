<?php

namespace App\Http\Requests;

use App\Models\Votante;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVotanteRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tipo_identificacion' => $this->normalizarTipoIdentificacion((string) $this->input('tipo_identificacion')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Votante $votante */
        $votante = $this->route('votante');

        return [
            'foto_certificado' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/webp,image/bmp,image/heic,image/heif', 'max:10240'],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'tipo_identificacion' => ['required', Rule::in(['cc', 'cd', 'ce', 'pasaporte'])],
            'numero_identificacion' => [
                'required',
                'string',
                'max:40',
                Rule::unique('votantes', 'numero_identificacion')->ignore($votante?->id),
            ],
            'telefono' => ['required', 'string', 'max:20'],
            'departamento' => ['required', 'string', 'max:255'],
            'municipio' => ['required', 'string', 'max:255'],
            'puesto_votacion' => ['required', 'string', 'max:255'],
            'comuna' => ['nullable', 'string', 'max:255'],
            'direccion' => ['required', 'string', 'max:255'],
            'mesa_votacion' => ['required', 'string', 'max:40'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto_certificado.mimetypes' => 'El certificado debe ser una imagen en formato JPG, PNG, GIF, WEBP, BMP, HEIC o HEIF. Si es una foto de cámara, vuelve a guardarla como imagen.',
            'foto_certificado.max' => 'La imagen supera el límite permitido de 10 MB. Prueba bajar la resolución, guardarla en JPG o WEBP, o recortarla antes de subirla.',
        ];
    }

    private function normalizarTipoIdentificacion(string $tipo): string
    {
        return match (trim(mb_strtolower($tipo))) {
            'cédula de ciudadanía', 'cedula de ciudadanía', 'cedula de ciudadania', 'cc' => 'cc',
            'cédula digital', 'cedula digital', 'cd' => 'cd',
            'cédula de extranjería', 'cedula de extranjería', 'cedula de extranjeria', 'ce' => 'ce',
            'pasaporte', 'pasaporte colombiano' => 'pasaporte',
            default => trim($tipo),
        };
    }
}
