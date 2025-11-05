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
            
            $table->integer('id_habilitacion')->primary(); 
            $table->foreign('id_habilitacion')->references('id_habilitacion')->on('habilitacion')->onDelete('cascade'); 
            
            $table->string('nombre_supervisor', 50)->nullable(false);
            $table->string('nombre_empresa', 50)->nullable(false);
            
            $table->integer('rut_profesor_tutor')->nullable(false);
            $table->foreign('rut_profesor_tutor')->references('rut_profesor')->on('profesor')->onDelete('restrict');
            
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('pr_tut');
    }
};
