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
            // Profesor 1: DINF Válido (Será cargado/actualizado en 'profesors')
            ['rut_profesor' => 10234567, 'nombre_profesor' => 'Ana', 'apellido_profesor' => 'Perez', 'departamento' => 'DINF'],
            // Profesor 2: DINF Válido
            ['rut_profesor' => 12345678, 'nombre_profesor' => 'Sofia', 'apellido_profesor' => 'Rojas', 'departamento' => 'DINF'],
            // Profesor 3: NO DINF (Será IGNORADO por el servicio de carga)
            ['rut_profesor' => 20000000, 'nombre_profesor' => 'Luis', 'apellido_profesor' => 'Gomez', 'departamento' => 'FIS'],
        ]);

        // -----------------------------------------------------------------
        // 2. CARGA ACADÉMICA (ALUMNOS y ASIGNATURAS) - Fuente para el filtro IN2000C
        // -----------------------------------------------------------------
        DB::table('carga_academica')->insert([
            // Caso 1: OK - Cursa IN2000C, datos válidos. Debe sincronizarse.
            [
                'rut_alumno' => 18567890, 
                'nombre_alumno' => 'Javier', 
                'apellido_alumno' => 'Soto',
                'asignaturas' => json_encode(['FI001C', 'IN2000C', 'MA1001']) 
            ],
            // Caso 2: RECHAZADO - No cursa IN2000C (Debe ser IGNORADO).
            [
                'rut_alumno' => 19123456, 
                'nombre_alumno' => 'Maria', 
                'apellido_alumno' => 'Gomez',
                'asignaturas' => json_encode(['FI001C', 'CI999A']) 
            ],
            // Caso 3: NOTA NULA - Cursa IN2000C, pero nota es NULL (Nota asignada será 0.0).
            [
                'rut_alumno' => 17098765, 
                'nombre_alumno' => 'Carlos', 
                'apellido_alumno' => 'Vidal',
                'asignaturas' => json_encode(['IN2000C', 'MA1002']) 
            ],
        ]);
        
        // -----------------------------------------------------------------
        // 3. NOTAS EN LÍNEA - Fuente para Nota Final
        // -----------------------------------------------------------------
        DB::table('notas_en_linea')->insert([
            // Caso 1: Nota 6.2 disponible
            [
                'rut_alumno' => 18567890, 
                'nota_final' => 6.2, 
                'fecha_nota' => Carbon::now()
            ],
            // Caso 2: Nota 5.5 disponible (pero el alumno será rechazado por la asignatura)
            [
                'rut_alumno' => 19123456, 
                'nota_final' => 5.5, 
                'fecha_nota' => Carbon::now()
            ],
            // Caso 3: Nota NULA (valor debe ser 0.0)
            [
                'rut_alumno' => 17098765, 
                'nota_final' => null, 
                'fecha_nota' => null 
            ],
        ]);
    }
}