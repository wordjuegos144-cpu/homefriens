<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->integer('camas')->nullable();
            $table->integer('cuartos')->nullable();
            $table->integer('banos')->nullable();
        });
    }

    public function down()
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropColumn(['camas', 'cuartos', 'banos']);
        });
    }
};