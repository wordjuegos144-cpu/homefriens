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
        Schema::create('huespeds', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('Whatsapp', 150);
            $table->string('numeroDocumento', 100)->nullable();
            $table->boolean('enListaNegra')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huespeds');
    }
};
