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
        Schema::create('proyecto', function (Blueprint $table) {
            
            // PK y FK a Habilitacion (Subclase Mapping)
            // La clave primaria hereda de Habilitacion y es una FK a la superclase
            $table->integer('id_habilitacion')->primary(); 
            $table->foreign('id_habilitacion')->references('id_habilitacion')->on('habilitacion')->onDelete('cascade'); 
            
            // Atributo del Subtipo (Tipo_Proyecto)
            $table->string('tipo_proyecto', 10)->nullable(false);
            
            // FK de Rol de Profesor GUIA (1:N) - NOT NULL
            $table->integer('rut_profesor_guia')->nullable(false);
            $table->foreign('rut_profesor_guia')->references('rut_profesor')->on('profesor')->onDelete('restrict'); 
            
            // FK de Rol de Profesor CO-GUIA (1:N) - NULL permitido (Opcional)
            $table->integer('rut_profesor_co_guia')->nullable(true);
            $table->foreign('rut_profesor_co_guia')->references('rut_profesor')->on('profesor')->onDelete('set null'); 
            
            // FK de Rol de Profesor COMISION (1:N) - NOT NULL
            $table->integer('rut_profesor_comision')->nullable(false);
            $table->foreign('rut_profesor_comision')->references('rut_profesor')->on('profesor')->onDelete('restrict');
            
            // Nota: Se omiten $table->id() y $table->timestamps() para ser fiel al SQL y al modelo de subclase.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto');
    }
};
