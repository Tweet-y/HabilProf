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
            
            $table->integer('id_habilitacion')->primary(); 
            $table->foreign('id_habilitacion')->references('id_habilitacion')->on('habilitacion')->onDelete('cascade'); 
            
            $table->string('tipo_proyecto', 10)->nullable(false);
            
            $table->integer('rut_profesor_guia')->nullable(false);
            $table->foreign('rut_profesor_guia')->references('rut_profesor')->on('profesor')->onDelete('restrict'); 
            
            $table->integer('rut_profesor_co_guia')->nullable(true);
            $table->foreign('rut_profesor_co_guia')->references('rut_profesor')->on('profesor')->onDelete('set null'); 
            
            $table->integer('rut_profesor_comision')->nullable(false);
            $table->foreign('rut_profesor_comision')->references('rut_profesor')->on('profesor')->onDelete('restrict');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyecto');
    }
};
