<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_en_linea', function (Blueprint $table) {
            $table->integer('rut_alumno')->primary(); 
            $table->float('nota_final')->nullable(true);
            $table->dateTime('fecha_nota')->nullable(true); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_en_linea');
    }
};
