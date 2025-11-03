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
        Schema::create('profesors', function (Blueprint $table) {
            // PK: rut_profesor (INTEGER) [cite: 173]
            $table->integer('rut_profesor')->primary(); 
            // Atributos (VARCHAR(50), NOT NULL) [cite: 173]
            $table->string('nombre_profesor', 50)->nullable(false);
            $table->string('apellido_profesor', 50)->nullable(false);
            
            // Nota: Se usa $table->integer en lugar de $table->unsignedInteger 
            // para ser fiel al tipo INTEGER de tu SQL, aunque unsignedInteger 
            // sería mejor para un RUT.
            
            // Nota: Se omiten $table->id() y $table->timestamps() por ser fiel al SQL.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesors');
    }
};
