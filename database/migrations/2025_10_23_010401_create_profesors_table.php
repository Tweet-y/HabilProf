<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesor', function (Blueprint $table) {
            $table->integer('rut_profesor')->primary(); 
            $table->string('nombre_profesor', 50)->nullable(false);
            $table->string('apellido_profesor', 50)->nullable(false);
            $table->string('departamento', 50)->nullable(false); 
            
        });
        DB::statement('ALTER TABLE profesor ADD CONSTRAINT rut_valido CHECK (rut_profesor > 999999 AND rut_profesor <= 99999999)');
        DB::statement("ALTER TABLE profesor ADD CONSTRAINT nombre_solo_letras CHECK (nombre_profesor ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement("ALTER TABLE profesor ADD CONSTRAINT apellido_solo_letras CHECK (apellido_profesor ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
        DB::statement("ALTER TABLE profesor ADD CONSTRAINT departamento_solo_letras CHECK (departamento ~ '^[a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$')");
    }
    public function down(): void
    {
        Schema::dropIfExists('profesor');
    }
};
