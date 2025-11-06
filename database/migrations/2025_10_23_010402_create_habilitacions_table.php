<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habilitacion', function (Blueprint $table) {
            
            $table->id('id_habilitacion'); 
            $table->integer('rut_alumno')->unique()->nullable(false); 
            $table->foreign('rut_alumno')->references('rut_alumno')->on('alumno')->onDelete('cascade'); 
            
            $table->float('nota_final')->nullable(false)->default(0.0);
            $table->date('fecha_nota')->nullable(true); 
            $table->string('semestre_inicio', 9)->nullable(false); 
            $table->string('descripcion', 500)->nullable(false); 
            $table->string('titulo', 50)->nullable(false); 
        });
        DB::statement('ALTER TABLE habilitacion ADD CONSTRAINT nota_rango CHECK (nota_final = 0.0 OR (nota_final >= 1.0 AND nota_final <= 7.0))');
    }
    public function down(): void
    {
        Schema::dropIfExists('habilitacion');
    }
};