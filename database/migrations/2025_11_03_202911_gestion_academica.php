<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestion_academica', function (Blueprint $table) {
            // PK: rut_profesor
            $table->integer('rut_profesor')->primary(); 
            
            // Atributos
            $table->string('nombre_profesor', 50)->nullable(false);
            $table->string('apellido_profesor', 50)->nullable(false);
            
            // Campo clave para la filtración DINF
            $table->string('departamento', 50)->nullable(false); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gestion_academica');
    }
};