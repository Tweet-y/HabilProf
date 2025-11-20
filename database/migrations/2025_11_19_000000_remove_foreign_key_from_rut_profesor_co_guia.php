<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Elimina la restricción de clave foránea del campo rut_profesor_co_guia
     * para permitir que los co-guías puedan ser de cualquier departamento,
     * no solo del DINF (tabla profesor).
     */
    public function up(): void
    {
        Schema::table('proyecto', function (Blueprint $table) {
            // Eliminar la foreign key constraint del co-guía
            $table->dropForeign(['rut_profesor_co_guia']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyecto', function (Blueprint $table) {
            // Restaurar la foreign key constraint si se hace rollback
            $table->foreign('rut_profesor_co_guia')
                  ->references('rut_profesor')
                  ->on('profesor')
                  ->onDelete('set null');
        });
    }
};
