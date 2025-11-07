<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum values to 'Pendiente' and 'Completado' to match the UI
        DB::statement("ALTER TABLE `pagos` MODIFY `estadoPago` ENUM('Pendiente','Completado') NOT NULL DEFAULT 'Pendiente';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum that had 'Confirmado' and 'Pendiente'
        DB::statement("ALTER TABLE `pagos` MODIFY `estadoPago` ENUM('Confirmado','Pendiente') NOT NULL DEFAULT 'Pendiente';");
    }
};
