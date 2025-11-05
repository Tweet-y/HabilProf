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
        DB::statement('ALTER TABLE alumno ADD CONSTRAINT rut_valido_rango CHECK (rut_profesor > 999999 AND rut_profesor <= 99999999)');
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno');
    }
};
