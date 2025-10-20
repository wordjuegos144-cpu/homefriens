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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idDepartamento');
            $table->unsignedBigInteger('idHuesped');
            $table->unsignedBigInteger('idCanalReserva');
            $table->dateTime('fechaInicio');
            $table->dateTime('fechaFin');
            $table->string('estado', 50);
            $table->decimal('costoPorNoche', 10, 2);
            $table->integer('cantidadHuespedes');
            $table->integer('cantidadNoches');
            $table->decimal('descuentoAplicado', 5, 2)->default(0);
            $table->decimal('comisionCanal', 5, 2);
            $table->decimal('montoReserva', 10, 2);
            $table->decimal('montoLimpieza', 10, 2);
            $table->decimal('montoGarantia', 10, 2);
            $table->decimal('montoEmpresaAdministradora', 10, 2);
            $table->decimal('montoPropietario', 10, 2);
            $table->timestamps();

            $table->foreign('idDepartamento')->references('id')->on('departamentos')->onDelete('cascade');
            $table->foreign('idHuesped')->references('id')->on('huespeds')->onDelete('cascade');
            $table->foreign('idCanalReserva')->references('id')->on('canal_reservas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
