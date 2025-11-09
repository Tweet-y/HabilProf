<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno', function (Blueprint $table) {
            $table->integer('rut_alumno')->primary(); 
            $table->string('nombre_alumno', 50)->nullable(false);
            $table->string('apellido_alumno', 50)->nullable(false);
            
        });
        DB::statement('ALTER TABLE alumno ADD CONSTRAINT rut_valido CHECK (rut_alumno > 999999 AND rut_alumno <= 99999999)');
        DB::statement("ALTER TABLE alumno ADD CONSTRAINT nombre_solo_letras CHECK (nombre_alumno ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement("ALTER TABLE alumno ADD CONSTRAINT apellido_solo_letras CHECK (apellido_alumno ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno');
    }
};
