<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habilitacion', function (Blueprint $table) {
            
            $table->integer('rut_alumno')->primary();
            $table->foreign('rut_alumno')->references('rut_alumno')->on('alumno')->onDelete('cascade');
            
            $table->float('nota_final')->nullable(false)->default(0.0);
            $table->date('fecha_nota')->nullable(true); 
            $table->string('semestre_inicio', 9)->nullable(false); 
            $table->string('descripcion', 500)->nullable(false); 
            $table->string('titulo', 80)->nullable(false);
        });
        DB::statement('ALTER TABLE habilitacion ADD CONSTRAINT nota_rango CHECK (nota_final = 0.0 OR (nota_final >= 1.0 AND nota_final <= 7.0))');
        DB::statement('ALTER TABLE habilitacion ADD CONSTRAINT rut_valido CHECK (rut_alumno > 999999 AND rut_alumno <= 99999999)');
        DB::statement('ALTER TABLE habilitacion ADD CONSTRAINT semestre_inicio_valido CHECK (semestre_inicio ~ \'^[0-9]{4}-[12]$\' AND CAST(SUBSTRING(semestre_inicio FROM 1 FOR 4) AS INTEGER) BETWEEN 2025 AND 2050 AND CAST(SUBSTRING(semestre_inicio FROM 6 FOR 1) AS INTEGER) IN (1, 2))');
        DB::statement('ALTER TABLE habilitacion ADD CONSTRAINT fecha_anio_valido CHECK (fecha_nota IS NULL OR (EXTRACT(YEAR FROM fecha_nota) BETWEEN 2000 AND 2050))');
        // Constraints para descripcion y titulo consistentes con Backend y Frontend
        DB::statement("ALTER TABLE habilitacion ADD CONSTRAINT descripcion_valida CHECK (descripcion ~ E'^[a-zA-Z0-9\\\\s.,;:\\'\\\"&\\\\-_()áéíóúñÁÉÍÓÚ]+$')");
        DB::statement("ALTER TABLE habilitacion ADD CONSTRAINT titulo_valido CHECK (titulo ~ E'^[a-zA-Z0-9\\\\s.,;:\\'\\\"&\\\\-_()áéíóúñÁÉÍÓÚ]+$')");
    }
    public function down(): void
    {
        Schema::dropIfExists('habilitacion');
    }
};