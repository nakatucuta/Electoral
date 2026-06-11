<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('geo_municipios', function (Blueprint $table) {
            $table->id();
            $table->string('departamento_codigo', 10)->index();
            $table->string('departamento_nombre');
            $table->string('codigo', 10)->unique();
            $table->string('nombre');
            $table->string('tipo', 50)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['departamento_nombre', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geo_municipios');
    }
};
