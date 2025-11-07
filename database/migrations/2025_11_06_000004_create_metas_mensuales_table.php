<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metas_mensuales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idDepartamento')->constrained('departamentos')->cascadeOnDelete();
            $table->integer('mes');
            $table->integer('anio');
            $table->decimal('valor_meta', 10, 2);
            $table->decimal('valor_actual', 10, 2)->default(0);
            $table->decimal('porcentaje_alcanzado', 5, 2)->default(0);
            $table->enum('estado', ['Pendiente', 'En Camino', 'En Progreso', 'Alcanzada'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Asegurar que no haya duplicados para el mismo departamento/mes/año
            $table->unique(['idDepartamento', 'mes', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metas_mensuales');
    }
};