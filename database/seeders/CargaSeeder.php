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
            // Profesores DINF - Titulares
            ['rut_profesor' => 10234567, 'nombre_profesor' => 'Ana', 'apellido_profesor' => 'Perez', 'departamento' => 'DINF'],
            ['rut_profesor' => 12345678, 'nombre_profesor' => 'Sofia', 'apellido_profesor' => 'Rojas', 'departamento' => 'DINF'],
            ['rut_profesor' => 15000000, 'nombre_profesor' => 'Roberto', 'apellido_profesor' => 'Mena', 'departamento' => 'DINF'],
            ['rut_profesor' => 16161616, 'nombre_profesor' => 'Elena', 'apellido_profesor' => 'Fuentes', 'departamento' => 'DINF'],
            ['rut_profesor' => 13131313, 'nombre_profesor' => 'Juan', 'apellido_profesor' => 'Silva', 'departamento' => 'DINF'],
            ['rut_profesor' => 14141414, 'nombre_profesor' => 'Carmen', 'apellido_profesor' => 'Lagos', 'departamento' => 'DINF'],
            ['rut_profesor' => 17171717, 'nombre_profesor' => 'Ricardo', 'apellido_profesor' => 'Muñoz', 'departamento' => 'DINF'],
            ['rut_profesor' => 18181818, 'nombre_profesor' => 'Paula', 'apellido_profesor' => 'Vargas', 'departamento' => 'DINF'],
            
            // Profesores DINF - Adjuntos
            ['rut_profesor' => 26262626, 'nombre_profesor' => 'Ignacio', 'apellido_profesor' => 'Valenzuela', 'departamento' => 'DINF'],
            ['rut_profesor' => 27272727, 'nombre_profesor' => 'Daniela', 'apellido_profesor' => 'Parra', 'departamento' => 'DINF'],
            ['rut_profesor' => 28282828, 'nombre_profesor' => 'Gabriel', 'apellido_profesor' => 'Martínez', 'departamento' => 'DINF'],
            ['rut_profesor' => 29292929, 'nombre_profesor' => 'Isabella', 'apellido_profesor' => 'Vergara', 'departamento' => 'DINF'],
            
            // Profesores DINF - Part-time
            ['rut_profesor' => 30303030, 'nombre_profesor' => 'Sebastián', 'apellido_profesor' => 'Araya', 'departamento' => 'DINF'],
            ['rut_profesor' => 31313131, 'nombre_profesor' => 'Valentina', 'apellido_profesor' => 'Sepúlveda', 'departamento' => 'DINF'],
            ['rut_profesor' => 32323232, 'nombre_profesor' => 'Matías', 'apellido_profesor' => 'Herrera', 'departamento' => 'DINF'],
            
            // Profesores MAT 
            ['rut_profesor' => 21212121, 'nombre_profesor' => 'Patricio', 'apellido_profesor' => 'Diaz', 'departamento' => 'MAT'],
            ['rut_profesor' => 22222222, 'nombre_profesor' => 'Maria', 'apellido_profesor' => 'Torres', 'departamento' => 'MAT'],
            ['rut_profesor' => 25252525, 'nombre_profesor' => 'Alberto', 'apellido_profesor' => 'Pinto', 'departamento' => 'MAT'],
            ['rut_profesor' => 33333333, 'nombre_profesor' => 'Laura', 'apellido_profesor' => 'Soto', 'departamento' => 'MAT'],
            ['rut_profesor' => 34343434, 'nombre_profesor' => 'Felipe', 'apellido_profesor' => 'Núñez', 'departamento' => 'MAT'],
            
            // Profesores FIS
            ['rut_profesor' => 20000000, 'nombre_profesor' => 'Luis', 'apellido_profesor' => 'Gomez', 'departamento' => 'FIS'],
            ['rut_profesor' => 23232323, 'nombre_profesor' => 'Fernando', 'apellido_profesor' => 'Ruiz', 'departamento' => 'FIS'],
            ['rut_profesor' => 35353535, 'nombre_profesor' => 'Catalina', 'apellido_profesor' => 'Mora', 'departamento' => 'FIS'],
            ['rut_profesor' => 36363636, 'nombre_profesor' => 'Diego', 'apellido_profesor' => 'Espinoza', 'departamento' => 'FIS'],
            
            // Profesores QUI
            ['rut_profesor' => 24242424, 'nombre_profesor' => 'Carolina', 'apellido_profesor' => 'Bravo', 'departamento' => 'QUI'],
            ['rut_profesor' => 37373737, 'nombre_profesor' => 'Andrés', 'apellido_profesor' => 'Miranda', 'departamento' => 'QUI'],
            ['rut_profesor' => 38383838, 'nombre_profesor' => 'Javiera', 'apellido_profesor' => 'Rojas', 'departamento' => 'QUI'],
            
            // Profesores BIO
            ['rut_profesor' => 39393939, 'nombre_profesor' => 'Rodrigo', 'apellido_profesor' => 'Cortés', 'departamento' => 'BIO'],
            ['rut_profesor' => 40404040, 'nombre_profesor' => 'Camila', 'apellido_profesor' => 'Vega', 'departamento' => 'BIO'],
            ['rut_profesor' => 41414141, 'nombre_profesor' => 'Vicente', 'apellido_profesor' => 'Molina', 'departamento' => 'BIO'],
        ]);

        // -----------------------------------------------------------------
        // 2. CARGA ACADÉMICA (ALUMNOS y ASIGNATURAS) - Fuente para el filtro IN2000C
        // -----------------------------------------------------------------
        DB::table('carga_academica')->insert([
            // Grupo 1: Alumnos cursando IN2000C con notas disponibles (buenos resultados)
            [
                'rut_alumno' => 18567890, 
                'nombre_alumno' => 'Javier', 
                'apellido_alumno' => 'Soto',
                'asignaturas' => json_encode(['FI001C', 'IN2000C', 'MA1001', 'IN3001']) 
            ],
            [
                'rut_alumno' => 14789012, 
                'nombre_alumno' => 'Pamela', 
                'apellido_alumno' => 'Contreras',
                'asignaturas' => json_encode(['IN2000C', 'IN3001', 'MA1002', 'FI001C']) 
            ],
            [
                'rut_alumno' => 17098765, 
                'nombre_alumno' => 'Carlos', 
                'apellido_alumno' => 'Vidal',
                'asignaturas' => json_encode(['IN2000C', 'MA1002', 'FI002C']) 
            ],
            
            // Grupo 2: Alumnos cursando IN2000C con notas regulares
            [
                'rut_alumno' => 25252525, 
                'nombre_alumno' => 'Lucas', 
                'apellido_alumno' => 'Fernández',
                'asignaturas' => json_encode(['IN2000C', 'MA1001', 'FI001C']) 
            ],
            [
                'rut_alumno' => 26262626, 
                'nombre_alumno' => 'Isabel', 
                'apellido_alumno' => 'Morales',
                'asignaturas' => json_encode(['IN2000C', 'IN3002', 'MA1002', 'LE2000']) 
            ],
            [
                'rut_alumno' => 27272727, 
                'nombre_alumno' => 'Mateo', 
                'apellido_alumno' => 'Silva',
                'asignaturas' => json_encode(['IN2000C', 'FI002C', 'QU1001']) 
            ],
            
            // Grupo 3: Alumnos cursando IN2000C sin notas aún
            [
                'rut_alumno' => 20202020, 
                'nombre_alumno' => 'Andrea', 
                'apellido_alumno' => 'López',
                'asignaturas' => json_encode(['IN2000C', 'FI002C', 'MA1001', 'IN3001']) 
            ],
            [
                'rut_alumno' => 21212121, 
                'nombre_alumno' => 'Felipe', 
                'apellido_alumno' => 'Rivas',
                'asignaturas' => json_encode(['IN2000C', 'IN3002', 'MA1003', 'FI001C']) 
            ],
            [
                'rut_alumno' => 28282828, 
                'nombre_alumno' => 'Antonia', 
                'apellido_alumno' => 'Guzmán',
                'asignaturas' => json_encode(['IN2000C', 'MA1002', 'LE2001']) 
            ],
            
            // Grupo 4: Alumnos que no cursan IN2000C - Primer año
            [
                'rut_alumno' => 29292929, 
                'nombre_alumno' => 'Benjamín', 
                'apellido_alumno' => 'Muñoz',
                'asignaturas' => json_encode(['MA1001', 'FI001C', 'QU1001', 'LE1001']) 
            ],
            [
                'rut_alumno' => 30303030, 
                'nombre_alumno' => 'Sofía', 
                'apellido_alumno' => 'Rivera',
                'asignaturas' => json_encode(['MA1001', 'FI001C', 'LE1001', 'BIO101']) 
            ],
            
            // Grupo 5: Alumnos que no cursan IN2000C - Segundo año
            [
                'rut_alumno' => 31313131, 
                'nombre_alumno' => 'Tomás', 
                'apellido_alumno' => 'Pérez',
                'asignaturas' => json_encode(['MA1003', 'FI002C', 'QU1002', 'IN3001']) 
            ],
            [
                'rut_alumno' => 32323232, 
                'nombre_alumno' => 'Valentina', 
                'apellido_alumno' => 'Castro',
                'asignaturas' => json_encode(['FI002C', 'MA1002', 'LE2001', 'BIO102']) 
            ],
            
            // Grupo 6: Alumnos que no cursan IN2000C - Años superiores
            [
                'rut_alumno' => 33333333, 
                'nombre_alumno' => 'Joaquín', 
                'apellido_alumno' => 'Torres',
                'asignaturas' => json_encode(['IN4001', 'IN4002', 'IN4003']) 
            ],
            [
                'rut_alumno' => 34343434, 
                'nombre_alumno' => 'Catalina', 
                'apellido_alumno' => 'Flores',
                'asignaturas' => json_encode(['IN5001', 'IN5002', 'PR001']) 
            ],
            [
                'rut_alumno' => 35353535, 
                'nombre_alumno' => 'Sebastián', 
                'apellido_alumno' => 'Rojas',
                'asignaturas' => json_encode(['PR002', 'IN6001', 'IN6002']) 
            ],
        ]);
        
        // -----------------------------------------------------------------
        // 3. NOTAS EN LÍNEA - Fuente para Nota Final
        // -----------------------------------------------------------------
        DB::table('notas_en_linea')->insert([
            // Grupo 1: Alumnos cursando IN2000C con notas sobresalientes
            [
                'rut_alumno' => 18567890, 
                'nota_final' => 6.8, 
                'fecha_nota' => Carbon::now()->subDays(5)
            ],
            [
                'rut_alumno' => 14789012, 
                'nota_final' => 7.0, 
                'fecha_nota' => Carbon::now()
            ],
            [
                'rut_alumno' => 17098765, 
                'nota_final' => 6.5, 
                'fecha_nota' => Carbon::now()->subDays(1)
            ],
            
            // Grupo 2: Alumnos cursando IN2000C con notas regulares
            [
                'rut_alumno' => 25252525, 
                'nota_final' => 5.2, 
                'fecha_nota' => Carbon::now()->subDays(3)
            ],
            [
                'rut_alumno' => 26262626, 
                'nota_final' => 4.8, 
                'fecha_nota' => Carbon::now()->subDays(2)
            ],
            [
                'rut_alumno' => 27272727, 
                'nota_final' => 4.5, 
                'fecha_nota' => Carbon::now()->subDays(4)
            ],
            
            // Grupo 3: Alumnos cursando IN2000C sin notas aún
            [
                'rut_alumno' => 20202020, 
                'nota_final' => 0.0, // La nota se inicia en 0.0 hasta que se asigne una nota
                'fecha_nota' => null
            ],
            [
                'rut_alumno' => 21212121, 
                'nota_final' => 0.0, // La nota se inicia en 0.0 hasta que se asigne una nota
                'fecha_nota' => null
            ],
            [
                'rut_alumno' => 28282828, 
                'nota_final' => 0.0, // La nota se inicia en 0.0 hasta que se asigne una nota
                'fecha_nota' => null
            ],
            
            // Grupo 4: Alumnos de primer año (no IN2000C)
            [
                'rut_alumno' => 29292929, 
                'nota_final' => 5.8, 
                'fecha_nota' => Carbon::now()->subDays(10)
            ],
            [
                'rut_alumno' => 30303030, 
                'nota_final' => 6.2, 
                'fecha_nota' => Carbon::now()->subDays(8)
            ],
            
            // Grupo 5: Alumnos de segundo año (no IN2000C)
            [
                'rut_alumno' => 31313131, 
                'nota_final' => 5.5, 
                'fecha_nota' => Carbon::now()->subDays(15)
            ],
            [
                'rut_alumno' => 32323232, 
                'nota_final' => 6.0, 
                'fecha_nota' => Carbon::now()->subDays(12)
            ],
            
            // Grupo 6: Alumnos de años superiores (no IN2000C)
            [
                'rut_alumno' => 33333333, 
                'nota_final' => 6.4, 
                'fecha_nota' => Carbon::now()->subDays(20)
            ],
            [
                'rut_alumno' => 34343434, 
                'nota_final' => 6.7, 
                'fecha_nota' => Carbon::now()->subDays(18)
            ],
            [
                'rut_alumno' => 35353535, 
                'nota_final' => 6.9, 
                'fecha_nota' => Carbon::now()->subDays(25)
            ],
        ]);
    }
}