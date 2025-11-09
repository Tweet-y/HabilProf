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
        Schema::create('proyecto', function (Blueprint $table) {
            
            $table->integer('rut_alumno')->nullable(false);
            $table->string('semestre_inicio', 9)->nullable(false);
            $table->primary(['rut_alumno', 'semestre_inicio']);
            
            $table->string('tipo_proyecto', 10)->nullable(false);
            
            $table->integer('rut_profesor_guia')->nullable(false);
            $table->foreign('rut_profesor_guia')->references('rut_profesor')->on('profesor')->onDelete('restrict'); 
            
            $table->integer('rut_profesor_co_guia')->nullable(true);
            $table->foreign('rut_profesor_co_guia')->references('rut_profesor')->on('profesor')->onDelete('set null'); 
            
            $table->integer('rut_profesor_comision')->nullable(false);
            $table->foreign('rut_profesor_comision')->references('rut_profesor')->on('profesor')->onDelete('restrict');
            
        });
        DB::statement('ALTER TABLE proyecto ADD CONSTRAINT fk_proyecto_habilitacion FOREIGN KEY (rut_alumno, semestre_inicio) REFERENCES habilitacion (rut_alumno, semestre_inicio) ON DELETE CASCADE;');
        DB::statement('ALTER TABLE proyecto ADD CONSTRAINT rut_guia_valido CHECK (rut_profesor_guia > 999999 AND rut_profesor_guia <= 99999999)');
        DB::statement('ALTER TABLE proyecto ADD CONSTRAINT rut_co_guia_valido CHECK (rut_profesor_co_guia IS NULL OR (rut_profesor_co_guia > 999999 AND rut_profesor_co_guia <= 99999999))');
        DB::statement('ALTER TABLE proyecto ADD CONSTRAINT rut_comision_valido CHECK (rut_profesor_comision > 999999 AND rut_profesor_comision <= 99999999)');
        DB::statement("ALTER TABLE proyecto ADD CONSTRAINT tipo_proyecto_valido CHECK (tipo_proyecto IN ('PrIng', 'PrInv'))");
        
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto');
    }
};
