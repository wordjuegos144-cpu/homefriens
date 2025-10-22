<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ganacia_departamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('cascade');
            $table->string('periodo'); // mensual, trimestral, anual
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->decimal('ingresos_reservas', 10, 2);
            $table->decimal('gastos_limpieza', 10, 2);
            $table->decimal('comisiones_admin', 10, 2);
            $table->decimal('ganancia_neta', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ganacia_departamentos');
    }
};