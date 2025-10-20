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
        Schema::create('evidencia_limpiezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('limpieza_id');
            $table->enum('tipo', ['Limpieza', 'Daño']);
            $table->timestamps();

            $table->foreign('limpieza_id')->references('id')->on('limpiezas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evidencia_limpiezas');
    }
};
