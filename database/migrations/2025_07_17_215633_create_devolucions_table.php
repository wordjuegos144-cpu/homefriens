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
        Schema::create('devolucions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idReserva');
            $table->decimal('monto', 10, 2);
            $table->date('fechaDevolucion');
            $table->date('fechaProcesada')->nullable();
            $table->string('estadoPago', 50);
            $table->binary('comprobante')->nullable();
            $table->timestamps();

            $table->foreign('idReserva')->references('id')->on('reservas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolucions');
    }
};
