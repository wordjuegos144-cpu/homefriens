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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idDepartamento');
            $table->unsignedBigInteger('idEmpresaAdministradora');
            $table->date('fechaInicioContrato');
            $table->date('fechaFinContrato');
            $table->decimal('comisionContrato', 5, 2);
            $table->timestamps();

            $table->foreign('idDepartamento')
                ->references('id')
                ->on('departamentos')
                ->onDelete('cascade');
            $table->foreign('idEmpresaAdministradora')
                ->references('id')
                ->on('empresa_administradoras')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
