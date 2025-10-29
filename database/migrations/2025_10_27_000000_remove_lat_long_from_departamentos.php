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
        // Drop latitud/longitud if they exist
        if (Schema::hasTable('departamentos')) {
            $toDrop = [];
            if (Schema::hasColumn('departamentos', 'latitud')) {
                $toDrop[] = 'latitud';
            }
            if (Schema::hasColumn('departamentos', 'longitud')) {
                $toDrop[] = 'longitud';
            }

            if (!empty($toDrop)) {
                Schema::table('departamentos', function (Blueprint $table) use ($toDrop) {
                    $table->dropColumn($toDrop);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('departamentos')) {
            Schema::table('departamentos', function (Blueprint $table) {
                if (!Schema::hasColumn('departamentos', 'latitud')) {
                    $table->decimal('latitud', 10, 8)->nullable();
                }
                if (!Schema::hasColumn('departamentos', 'longitud')) {
                    $table->decimal('longitud', 11, 8)->nullable();
                }
            });
        }
    }
};
