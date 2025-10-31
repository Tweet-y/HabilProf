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
        Schema::create('habilitacion', function (Blueprint $table) {
            
            // PK: id_habilitacion (SERIAL/Autoincremento)
            // Usamos 'id' y luego lo renombramos a id_habilitacion para ser más fiel a la columna.
            $table->id('id_habilitacion'); 
            
            // FK a Alumno (Relación 1:1)
            // La columna debe ser INTEGER (como rut_alumno) y UNIQUE para forzar el 1:1.
            $table->integer('rut_alumno')->unique()->nullable(false); 
            $table->foreign('rut_alumno')->references('rut_alumno')->on('alumno')->onDelete('cascade'); 
            
            // Atributos Comunes
            $table->float('nota_final')->nullable(true); // NULL permitido
            $table->date('fecha_nota')->nullable(true); // NULL permitido
            $table->string('semestre_inicio', 9)->nullable(false); // NOT NULL
            $table->string('descripcion', 500)->nullable(false); // NOT NULL
            $table->string('titulo', 50)->nullable(false); // NOT NULL
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('habilitacion');
    }
};
