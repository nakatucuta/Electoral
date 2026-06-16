<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function audits(): HasMany
    {
        return $this->hasMany(VotanteAudit::class)->latest();
    }

    public function getFotoCertificadoUrlAttribute(): ?string
    {
        if (! $this->foto_certificado) {
            return null;
        }

        $baseUrl = request()?->getBaseUrl() ?? '';
        $baseUrl = preg_replace('#/index\.php$#', '', $baseUrl);
        $version = $this->updated_at?->timestamp ?? $this->created_at?->timestamp ?? time();
        $storagePath = 'storage/' . ltrim($this->foto_certificado, '/');

        if ($baseUrl !== '') {
            return rtrim($baseUrl, '/') . '/' . $storagePath . '?v=' . $version;
        }

        return asset($storagePath) . '?v=' . $version;
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
