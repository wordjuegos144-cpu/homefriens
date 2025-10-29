<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize existing values: map 'Confirmado' to 'Completado'
        DB::statement("UPDATE pagos SET estadoPago = 'Completado' WHERE estadoPago = 'Confirmado'");

        // Alter enum to the desired set: Pendiente, Completado
        // Use MODIFY for MySQL; this is a raw statement to ensure compatibility.
        DB::statement("ALTER TABLE pagos MODIFY estadoPago ENUM('Pendiente','Completado') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map 'Completado' back to 'Confirmado' to restore previous semantics
        DB::statement("UPDATE pagos SET estadoPago = 'Confirmado' WHERE estadoPago = 'Completado'");

        // Restore enum to original values
        DB::statement("ALTER TABLE pagos MODIFY estadoPago ENUM('Confirmado','Pendiente') NOT NULL");
    }
};
