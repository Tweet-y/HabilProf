<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pr_tut', function (Blueprint $table) {
            
            $table->integer('rut_alumno')->primary();
            $table->foreign('rut_alumno')->references('rut_alumno')->on('habilitacion')->onDelete('cascade');
            
            $table->string('nombre_supervisor', 50)->nullable(false);
            $table->string('nombre_empresa', 50)->nullable(false);
            
            $table->integer('rut_profesor_tutor')->nullable(false);
            $table->foreign('rut_profesor_tutor')->references('rut_profesor')->on('profesor')->onDelete('restrict');
            
        });
        DB::statement("ALTER TABLE pr_tut ADD CONSTRAINT supervisor_solo_letras CHECK (nombre_supervisor ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement("ALTER TABLE pr_tut ADD CONSTRAINT empresa_valido CHECK (nombre_empresa ~ '^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement('ALTER TABLE pr_tut ADD CONSTRAINT rut_tutor_valido CHECK (rut_profesor_tutor > 999999 AND rut_profesor_tutor <= 99999999)');
    }

    
    public function down(): void
    {
        Schema::dropIfExists('pr_tut');
    }
};
