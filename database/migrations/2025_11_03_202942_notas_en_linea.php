<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_en_linea', function (Blueprint $table) {
            $table->integer('rut_alumno')->primary(); 
            $table->float('nota_final')->nullable(true);
            $table->dateTime('fecha_nota')->nullable(true); 
        });
        DB::statement('ALTER TABLE notas_en_linea ADD CONSTRAINT rut_valido CHECK (rut_alumno > 999999 AND rut_alumno <= 99999999)');
        DB::statement('ALTER TABLE notas_en_linea ADD CONSTRAINT nota_rango_valido CHECK (nota_final IS NULL OR (nota_final >= 1.0 AND nota_final <= 7.0))');
        DB::statement('ALTER TABLE notas_en_linea ADD CONSTRAINT fecha_anio_valido CHECK (fecha_nota IS NULL OR (EXTRACT(YEAR FROM fecha_nota) BETWEEN 2000 AND 2050))');
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_en_linea');
    }
};
