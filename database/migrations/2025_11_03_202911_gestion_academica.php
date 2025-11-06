<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gestion_academica', function (Blueprint $table) {
            $table->integer('rut_profesor')->primary(); 
            
            $table->string('nombre_profesor', 50)->nullable(false);
            $table->string('apellido_profesor', 50)->nullable(false);
            
            $table->string('departamento', 50)->nullable(false); 
        });
        DB::statement('ALTER TABLE gestion_academica ADD CONSTRAINT rut_valido CHECK (rut_profesor > 999999 AND rut_profesor <= 99999999)');
    }

    public function down(): void
    {
        Schema::dropIfExists('gestion_academica');
    }
};