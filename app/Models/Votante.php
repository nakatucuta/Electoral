<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Votante extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'foto_certificado',
        'nombres',
        'apellidos',
        'tipo_identificacion',
        'numero_identificacion',
        'telefono',
        'departamento',
        'municipio',
        'puesto_votacion',
        'comuna',
        'direccion',
        'mesa_votacion',
        'relacion',
    ];

    protected $appends = [
        'foto_certificado_url',
        'estado_registro',
        'estado_registro_label',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFotoCertificadoUrlAttribute(): ?string
    {
        if (! $this->foto_certificado) {
            return null;
        }

        $baseUrl = request()?->getBaseUrl();

        if ($baseUrl !== null && $baseUrl !== '') {
            return rtrim($baseUrl, '/') . '/storage/' . ltrim($this->foto_certificado, '/');
        }

        return Storage::disk('public')->url($this->foto_certificado);
    }

    public function getEstadoRegistroAttribute(): string
    {
        return $this->foto_certificado ? 'confirmado' : 'pendiente';
    }

    public function getEstadoRegistroLabelAttribute(): string
    {
        return $this->foto_certificado ? 'Confirmado' : 'Pendiente';
    }
}
