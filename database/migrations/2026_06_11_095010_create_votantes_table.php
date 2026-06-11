<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('votantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('foto_certificado')->nullable();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('tipo_identificacion', 40);
            $table->string('numero_identificacion', 40)->unique();
            $table->string('puesto_votacion');
            $table->string('mesa_votacion', 40);
            $table->enum('relacion', ['familiar', 'amigo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votantes');
    }
};
