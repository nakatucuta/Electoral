<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('votantes', function (Blueprint $table) {
            $table->string('departamento')->nullable()->after('numero_identificacion');
            $table->string('municipio')->nullable()->after('departamento');
            $table->string('comuna')->nullable()->after('puesto_votacion');
            $table->string('direccion')->nullable()->after('comuna');

            $table->index(['departamento', 'municipio']);
            $table->index('comuna');
            $table->index('direccion');
        });
    }

    public function down(): void
    {
        Schema::table('votantes', function (Blueprint $table) {
            $table->dropIndex(['departamento', 'municipio']);
            $table->dropIndex(['comuna']);
            $table->dropIndex(['direccion']);

            $table->dropColumn(['departamento', 'municipio', 'comuna', 'direccion']);
        });
    }
};
