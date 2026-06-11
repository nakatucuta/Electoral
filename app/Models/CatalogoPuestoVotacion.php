<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoPuestoVotacion extends Model
{
    use HasFactory;

    protected $table = 'catalogo_puestos_votacion';

    protected $fillable = [
        'departamento',
        'municipio',
        'puesto',
        'comuna',
        'direccion',
    ];
}
