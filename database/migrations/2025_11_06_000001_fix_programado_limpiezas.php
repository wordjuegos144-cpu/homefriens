<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize legacy estado values from 'Programado' to 'Programada'
        DB::table('limpiezas')->where('estado', 'Programado')->update(['estado' => 'Programada']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: don't revert data normalization automatically
    }
};
