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
        Schema::create('profesor', function (Blueprint $table) {
            // PK: rut_profesor (INTEGER) [cite: 173]
            $table->integer('rut_profesor')->primary(); 
            // Atributos (VARCHAR(50), NOT NULL) [cite: 173]
            $table->string('nombre_profesor', 50)->nullable(false);
            $table->string('apellido_profesor', 50)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profesor');
    }
};