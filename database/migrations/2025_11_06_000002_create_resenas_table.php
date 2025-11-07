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
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idHuesped')->constrained('huespeds')->onDelete('cascade');
            $table->foreignId('idPropietario')->constrained('propietarios')->onDelete('cascade');
            $table->tinyInteger('valor')->unsigned();
            $table->text('argumento')->nullable();
            $table->timestamp('fecha')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
