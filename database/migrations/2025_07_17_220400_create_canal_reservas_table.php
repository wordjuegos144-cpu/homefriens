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
        Schema::create('canal_reservas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->decimal('comision', 5, 2)->default(0); // Nuevo campo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canal_reservas');
    }
};
