<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeoDepartamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'latitud',
        'longitud',
    ];

    public function municipios(): HasMany
    {
        return $this->hasMany(GeoMunicipio::class, 'departamento_codigo', 'codigo');
    }
}
