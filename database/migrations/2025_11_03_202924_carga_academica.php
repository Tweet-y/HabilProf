<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carga_academica', function (Blueprint $table) {
            $table->integer('rut_alumno')->primary(); 
            
            $table->string('nombre_alumno', 50)->nullable(false);
            $table->string('apellido_alumno', 50)->nullable(false);
            $table->string('asignaturas', 47)->nullable(false); 
        });
        DB::statement('ALTER TABLE carga_academica ADD CONSTRAINT rut_valido CHECK (rut_alumno > 999999 AND rut_alumno <= 99999999)');
        DB::statement("ALTER TABLE carga_academica ADD CONSTRAINT nombre_solo_letras CHECK (nombre_alumno ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement("ALTER TABLE carga_academica ADD CONSTRAINT apellido_solo_letras CHECK (apellido_alumno ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement("ALTER TABLE carga_academica ADD CONSTRAINT maximo_asignaturas CHECK (array_length(string_to_array(asignaturas, ','), 1) <= 6)");
        DB::statement("ALTER TABLE carga_academica ADD CONSTRAINT formato_asignaturas CHECK (asignaturas ~ '^[A-Z0-9]{7}(,[A-Z0-9]{7}){0,5}$')");
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_academica');
    }
};
