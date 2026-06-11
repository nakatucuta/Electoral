<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogo_puestos_votacion', function (Blueprint $table) {
            $table->id();
            $table->string('departamento');
            $table->string('municipio');
            $table->string('puesto');
            $table->string('comuna');
            $table->string('direccion');
            $table->timestamps();

            $table->unique(['departamento', 'municipio', 'puesto', 'comuna', 'direccion'], 'catalogo_puestos_unique');
            $table->index(['departamento', 'municipio']);
            $table->index('puesto');
            $table->index('comuna');
            $table->index('direccion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_puestos_votacion');
    }
};
