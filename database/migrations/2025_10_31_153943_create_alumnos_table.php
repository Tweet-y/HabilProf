<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Necesario para inyectar sentencias SQL crudas si fuera necesario

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alumno', function (Blueprint $table) {
            // PK: rut_alumno (INTEGER)
            // Usamos 'integer' para el tipo de dato y 'primary()' para definir la clave.
            $table->integer('rut_alumno')->primary(); 
            
            // Atributos (VARCHAR(50), NOT NULL)
            $table->string('nombre_alumno', 50)->nullable(false);
            $table->string('apellido_alumno', 50)->nullable(false);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumno');
    }
};
