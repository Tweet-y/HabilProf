<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CargaSeeder extends Seeder
{
    public function run(): void
    {
        // Limpieza de tablas de Mockup
        DB::table('gestion_academica')->delete();
        DB::table('carga_academica')->delete();
        DB::table('notas_en_linea')->delete();
        
        // -----------------------------------------------------------------
        // 1. GESTIÓN ACADÉMICA (PROFESORES) - Fuente para el filtro DINF
        // -----------------------------------------------------------------
        DB::table('gestion_academica')->insert([
            ['rut_profesor' => 10234567, 'nombre_profesor' => 'Ana', 'apellido_profesor' => 'Perez', 'departamento' => 'DINF'],
            ['rut_profesor' => 12345678, 'nombre_profesor' => 'Sofia', 'apellido_profesor' => 'Rojas', 'departamento' => 'DINF'],
            ['rut_profesor' => 15000000, 'nombre_profesor' => 'Roberto', 'apellido_profesor' => 'Mena', 'departamento' => 'DINF'],
            ['rut_profesor' => 16161616, 'nombre_profesor' => 'Elena', 'apellido_profesor' => 'Fuentes', 'departamento' => 'DINF'], 
            
            // Profesores NO DINF 
            ['rut_profesor' => 20000000, 'nombre_profesor' => 'Luis', 'apellido_profesor' => 'Gomez', 'departamento' => 'FIS'],
            ['rut_profesor' => 21212121, 'nombre_profesor' => 'Patricio', 'apellido_profesor' => 'Diaz', 'departamento' => 'MAT'], 
        ]);

        // -----------------------------------------------------------------
        // 2. CARGA ACADÉMICA (ALUMNOS y ASIGNATURAS) - Fuente para el filtro IN2000C
        // -----------------------------------------------------------------
        DB::table('carga_academica')->insert([
        // Caso 1: OK - Cursa IN2000C, nota disponible.
            [
                'rut_alumno' => 18567890, 
                'nombre_alumno' => 'Javier', 
                'apellido_alumno' => 'Soto',
                'asignaturas' => json_encode(['FI001C', 'IN2000C', 'MA1001']) 
            ],
            [
                'rut_alumno' => 19123456, 
                'nombre_alumno' => 'Maria', 
                'apellido_alumno' => 'Gomez',
                'asignaturas' => json_encode(['FI001C', 'CI999A']) 
            ],
            [
                'rut_alumno' => 17098765, 
                'nombre_alumno' => 'Carlos', 
                'apellido_alumno' => 'Vidal',
                'asignaturas' => json_encode(['IN2000C', 'MA1002']) 
            ],
            [
                'rut_alumno' => 14789012, 
                'nombre_alumno' => 'Pamela', 
                'apellido_alumno' => 'Contreras',
                'asignaturas' => json_encode(['IN2000C', 'IN3001']) 
            ],
            [
                'rut_alumno' => 15050505, 
                'nombre_alumno' => 'Diego', 
                'apellido_alumno' => 'Alarcon',
                'asignaturas' => json_encode(['MA1003', 'LE2000']) 
            ],
        ]);
        
        // -----------------------------------------------------------------
        // 3. NOTAS EN LÍNEA - Fuente para Nota Final
        // -----------------------------------------------------------------
        DB::table('notas_en_linea')->insert([
            [
                'rut_alumno' => 18567890, 
                'nota_final' => 6.2, 
                'fecha_nota' => Carbon::now()->subDays(5) // Nota cargada hace 5 días
            ],
            [
                'rut_alumno' => 19123456, 
                'nota_final' => 5.5, 
                'fecha_nota' => Carbon::now()->subDays(2)
            ],
            [
                'rut_alumno' => 17098765, 
                'nota_final' => null, 
                'fecha_nota' => null 
            ],
            [
                'rut_alumno' => 14789012, 
                'nota_final' => 7.0, 
                'fecha_nota' => Carbon::now() 
            ],
        ]);
    }
}