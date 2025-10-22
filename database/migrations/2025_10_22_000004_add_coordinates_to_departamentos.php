<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->decimal('latitud', 10, 8)->nullable()->after('direccion');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
        });
    }

    public function down()
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};