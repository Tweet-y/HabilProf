<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carga_academica', function (Blueprint $table) {
            // PK: rut_alumno
            $table->integer('rut_alumno')->primary(); 
            
            // Atributos
            $table->string('nombre_alumno', 50)->nullable(false);
            $table->string('apellido_alumno', 50)->nullable(false);
            
            // Listado de asignaturas (se asume JSONB para almacenar el array de códigos)
            $table->jsonb('asignaturas')->nullable(false); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_academica');
    }
};
