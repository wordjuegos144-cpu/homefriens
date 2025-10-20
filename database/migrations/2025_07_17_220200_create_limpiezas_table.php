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
        Schema::create('limpiezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reserva_id')->nullable();
            $table->date('fecha_programada')->nullable();
            $table->time('hora_programada')->nullable();
            $table->decimal('monto', 6, 2)->nullable();
            $table->string('estado')->default('Programada'); // Nuevo campo estado
            $table->timestamps();

            $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('limpiezas');
    }
};
