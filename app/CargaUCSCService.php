<?php

namespace App;
use Illuminate\Support\Facades\Log;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Habilitacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CargaUCSCService
{
    const CODIGO_HABILITACION = "IN2000C";
    private function obtenerDatosParaProcesar(): array
    {
        // 1. EXTRAER TODOS LOS REGISTROS DE LAS TRES FUENTES MOCKUP
        
        // 1.1 Carga_academica: Lista de alumnos a procesar y sus asignaturas
        $mockAlumnos = DB::table('carga_academica')->get();
        
        // 1.2 Gestión_academica: Lista completa de profesores DINF
        $mockProfesores = DB::table('gestion_academica')
                            ->where('departamento', 'DINF') 
                            ->get(); // Retorna la lista completa de profesores DINF
        
        // 1.3 Notas_en_linea: Contiene todas las notas registradas
        $mockNotas = DB::table('notas_en_linea')->get()->keyBy('rut_alumno')->toArray();
    
        return [
            'alumnos_carga' => $mockAlumnos,
            'profesores_dinf' => $mockProfesores, 
            'notas' => $mockNotas,
        ];
    }

    public function activarCargaPeriodica(): array
    {
        Log::info('RF1 Carga UCSC iniciada a las ' . now()->toDateTimeString());
        $dataSets = $this->obtenerDatosParaProcesar();
        $resultados = [];

        $profesoresDINF = $dataSets['profesores_dinf'];
        
        foreach ($profesoresDINF as $profesor) {

            Profesor::updateOrCreate(
                ['rut_profesor' => $profesor->rut_profesor],
                [
                    'nombre_profesor' => $profesor->nombre_profesor,
                    'apellido_profesor' => $profesor->apellido_profesor,
                ]
            );
        }
        $resultados[] = ['status' => 'GENERAL', 'mensaje' => count($profesoresDINF) . ' profesores DINF cargados/actualizados (RF1).'];


        
        $mockAlumnos = $dataSets['alumnos_carga'];
        $mockNotas = $dataSets['notas'];

        foreach ($mockAlumnos as $mockA_object) {
    
        // 1. Convertir el objeto stdClass a un array.
        $mockA = (array) $mockA_object; 
            
        // 2. Usar la sintaxis de array para acceder a las claves
        $rutAlumno = $mockA['rut_alumno'];
            
        // 3. Decodificar asignaturas.
        $asignaturas = json_decode($mockA['asignaturas'] ?? '[]', true) ?: []; 
            
        if (!in_array(self::CODIGO_HABILITACION, $asignaturas)) {
            continue; 
        }
        
        // 2.2 REGISTRO OBLIGATORIO DE ALUMNO 
        Alumno::updateOrCreate(
            ['rut_alumno' => $rutAlumno], 
            [
                'nombre_alumno' => $mockA['nombre_alumno'],
                'apellido_alumno' => $mockA['apellido_alumno'],
            ]
        );

            
            // Buscar si la Habilitación ya existe en HabilProf (RF2: Ingreso de Datos)
            $habilitacion = Habilitacion::where('rut_alumno', $rutAlumno)->first();

            if ($habilitacion) {
                
                $notaData = $mockNotas[$rutAlumno] ?? null;
                
                // Asegurar que nunca se asigne null a nota_final
                $notaFinal = $notaData && $notaData->nota_final !== null ? $notaData->nota_final : 0.0;
                $fechaNota = $notaData && $notaData->fecha_nota !== null ? $notaData->fecha_nota : '2050-12-31';
                
                $habilitacion->nota_final = $notaFinal;
                $habilitacion->fecha_nota = $fechaNota;
                $habilitacion->save(); 

                $resultados[] = ['rut' => $rutAlumno, 'status' => 'COMPLETADO', 'nota_aplicada' => $notaFinal];
            }
        }
        Log::info('RF1 Carga UCSC finalizada con ' . count($dataSets['alumnos_carga']) . ' alumnos procesados.');
        return $resultados;
    }
}