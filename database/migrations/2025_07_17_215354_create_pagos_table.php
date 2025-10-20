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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idReserva');
            $table->unsignedBigInteger('idLimpieza')->nullable();
            $table->enum('tipoPago', ['Reserva', 'Limpieza', 'Deposito']);
            $table->decimal('monto', 10, 2);
            $table->dateTime('fechaPago');
            $table->enum('formaPago', ['QR', 'Airtm', 'Efectivo BS', 'Efectivo USD']);
            $table->enum('estadoPago', ['Confirmado', 'Pendiente']);
            $table->binary('comprobante');
            $table->timestamps();

            $table->foreign('idReserva')->references('id')->on('reservas')->onDelete('cascade');
            $table->foreign('idLimpieza')->references('id')->on('limpiezas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
