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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idEmpresaAdministradora');
            $table->string('nombreEdificio', 100);
            $table->string('direccion', 255);
            $table->smallInteger('piso')->nullable();
            $table->integer('numero')->nullable();
            $table->integer('capacidadNormal');
            $table->integer('capacidadExtra');
            $table->string('nombrePropietario', 100);
            $table->string('telefonoPropietario', 100);
            $table->timestamps();

            $table->foreign('idEmpresaAdministradora')
                ->references('id')
                ->on('empresa_administradoras')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
