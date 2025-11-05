<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_en_linea', function (Blueprint $table) {
            // PK: rut_alumno (o puede ser una clave compuesta con asignatura/periodo, 
            // pero para la simulación, usamos rut_alumno como PK simple o clave única).
            $table->integer('rut_alumno')->primary(); 
            
            // Atributos
            $table->float('nota_final')->nullable(true); // Puede ser NULL si no está disponible (valor 0 en el servicio)
            $table->dateTime('fecha_nota')->nullable(true); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_en_linea');
    }
};
