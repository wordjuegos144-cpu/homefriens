<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['idReserva']);
            $table->unsignedBigInteger('idReserva')->nullable()->change();
            $table->foreign('idReserva')->references('id')->on('reservas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['idReserva']);
            $table->unsignedBigInteger('idReserva')->nullable(false)->change();
            $table->foreign('idReserva')->references('id')->on('reservas')->onDelete('cascade');
        });
    }
};
