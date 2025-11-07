<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            // Set default to 1 for cantidadHuespedes to avoid insert errors when not provided
            $table->integer('cantidadHuespedes')->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->integer('cantidadHuespedes')->default(null)->change();
        });
    }
};
