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
        Schema::create('pr_tut', function (Blueprint $table) {
            
            // PK y FK a Habilitacion (Subclase Mapping)
            // Usamos unsignedInteger porque la PK de Habilitacion es SERIAL/auto-incremento (INTEGER)
            $table->integer('id_habilitacion')->primary(); 
            $table->foreign('id_habilitacion')->references('id_habilitacion')->on('habilitacion')->onDelete('cascade'); 
            
            // Atributos Exclusivos (NOT NULL)
            $table->string('nombre_supervisor', 50)->nullable(false);
            $table->string('nombre_empresa', 50)->nullable(false);
            
            // FK de Rol de Profesor (Tutor) - NOT NULL
            $table->integer('rut_profesor_tutor')->nullable(false);
            $table->foreign('rut_profesor_tutor')->references('rut_profesor')->on('profesor')->onDelete('restrict');
            
            // Nota: Se omiten $table->id() y $table->timestamps() para ser fiel al SQL y al modelo de subclase.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_tut');
    }
};
