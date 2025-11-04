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

    /**
     * Simula la extracción desde las tres fuentes de datos separadas.
     * @return array Retorna los tres datasets de origen.
     */
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
        
        // Retornamos todos los datasets para su procesamiento independiente.
        return [
            'alumnos_carga' => $mockAlumnos,
            'profesores_dinf' => $mockProfesores, 
            'notas' => $mockNotas,
        ];
    }

    /**
     * Función que realiza las validaciones y el registro en las tablas finales.
     */
    public function activarCargaPeriodica(): array
    {
        Log::info('RF1 Carga UCSC iniciada a las ' . now()->toDateTimeString());
        $dataSets = $this->obtenerDatosParaProcesar();
        $resultados = [];

        // ************ TAREA 1: CARGAR/ACTUALIZAR TODOS LOS PROFESORES DEL DINF ************
        // Esta tarea es independiente de los alumnos.
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


        // ************ TAREA 2: PROCESAR CADA ALUMNO (Con filtro IN2000C) ************
        
        $mockAlumnos = $dataSets['alumnos_carga'];
        $mockNotas = $dataSets['notas'];

        foreach ($mockAlumnos as $mockA_object) {
    
        // 1. Convertir el objeto stdClass a un array asociativo.
        $mockA = (array) $mockA_object; 
            
        // 2. Usar la sintaxis de array para acceder a las claves
        $rutAlumno = $mockA['rut_alumno'];
            
        // 3. Decodificar asignaturas, usando una clave segura y manejo de nulls
        $asignaturas = json_decode($mockA['asignaturas'] ?? '[]', true) ?: []; 
            
        // 2.1 FILTRO OBLIGATORIO: Verificar Asignatura IN2000C
        if (!in_array(self::CODIGO_HABILITACION, $asignaturas)) {
            // ... (código de rechazo)
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

            // 2.3 VERIFICACIÓN CONDICIONAL DE NOTA FINAL (RF1: Carga/Actualiza Nota)
            
            // Buscar si la Habilitación ya existe en HabilProf (RF2: Ingreso de Datos)
            $habilitacion = Habilitacion::where('rut_alumno', $rutAlumno)->first();

            if ($habilitacion) {
                // Si la Habilitación existe, se aplica la lógica de la nota.
                
                $notaData = $mockNotas[$rutAlumno] ?? null;
                
                // Determinar Nota Final: 0.0 si no disponible (Requisito 49)
                $notaFinal = $notaData ? $notaData->nota_final : 0.0;
                
                // Actualizar Nota y Fecha de Carga
                $habilitacion->nota_final = $notaFinal;
                $habilitacion->fecha_nota = $notaData ? $notaData->fecha_nota : Carbon::now(); // Usar fecha de mockup o actual
                $habilitacion->save(); 

                $resultados[] = ['rut' => $rutAlumno, 'status' => 'COMPLETADO', 'nota_aplicada' => $notaFinal];
            }
        }
        Log::info('RF1 Carga UCSC finalizada con ' . count($dataSets['alumnos_carga']) . ' alumnos procesados.');
        return $resultados;
    }
}