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
        Schema::table('empresa_administradoras', function (Blueprint $table) {
            $table->decimal('comision', 5, 2)->default(10.00)->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa_administradoras', function (Blueprint $table) {
            $table->dropColumn('comision');
        });
    }
};