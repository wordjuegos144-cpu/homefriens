<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            // Attempt to drop foreign key if exists, then make column nullable
            try {
                $table->dropForeign(['idCanalReserva']);
            } catch (\Throwable $_) {
                // ignore if FK does not exist
            }

            $table->unsignedBigInteger('idCanalReserva')->nullable()->change();

            try {
                $table->foreign('idCanalReserva')->references('id')->on('canal_reservas')->onDelete('set null');
            } catch (\Throwable $_) {
                // ignore if cannot create FK
            }
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            try {
                $table->dropForeign(['idCanalReserva']);
            } catch (\Throwable $_) {
            }

            $table->unsignedBigInteger('idCanalReserva')->nullable(false)->change();
            $table->foreign('idCanalReserva')->references('id')->on('canal_reservas')->onDelete('cascade');
        });
    }
};