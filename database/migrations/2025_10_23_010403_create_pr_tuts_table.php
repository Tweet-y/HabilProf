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
        Schema::create('pr_tut', function (Blueprint $table) {
            
            $table->integer('rut_alumno')->nullable(false);
            $table->string('semestre_inicio', 9)->nullable(false);
            $table->primary(['rut_alumno', 'semestre_inicio']);
            
            $table->string('nombre_supervisor', 50)->nullable(false);
            $table->string('nombre_empresa', 50)->nullable(false);
            
            $table->integer('rut_profesor_tutor')->nullable(false);
            $table->foreign('rut_profesor_tutor')->references('rut_profesor')->on('profesor')->onDelete('restrict');
            
        });
        DB::statement('ALTER TABLE pr_tut ADD CONSTRAINT fk_prtut_habilitacion FOREIGN KEY (rut_alumno, semestre_inicio) REFERENCES habilitacion (rut_alumno, semestre_inicio) ON DELETE CASCADE;');
        DB::statement("ALTER TABLE pr_tut ADD CONSTRAINT supervisor_solo_letras CHECK (nombre_supervisor ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement("ALTER TABLE pr_tut ADD CONSTRAINT empresa_solo_letras CHECK (nombre_empresa ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement('ALTER TABLE pr_tut ADD CONSTRAINT rut_tutor_valido CHECK (rut_profesor_tutor > 999999 AND rut_profesor_tutor <= 99999999)');
    }

    
    public function down(): void
    {
        Schema::dropIfExists('pr_tut');
    }
};
