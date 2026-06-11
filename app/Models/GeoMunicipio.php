<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeoMunicipio extends Model
{
    use HasFactory;

    protected $fillable = [
        'departamento_codigo',
        'departamento_nombre',
        'codigo',
        'nombre',
        'tipo',
        'longitud',
        'latitud',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(GeoDepartamento::class, 'departamento_codigo', 'codigo');
    }
}
