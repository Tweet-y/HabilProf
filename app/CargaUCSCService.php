<?php

namespace App;
use Illuminate\Support\Facades\Log;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Habilitacion;
use Illuminate\Support\Facades\DB;

class CargaUCSCService
{
    const CODIGO_HABILITACION = "IN2000C";
    private function obtenerDatosParaProcesar(): array
    {
        // 1. EXTRAER TODOS LOS REGISTROS DE LAS TRES FUENTES 
        $datosAlumnos = DB::table('carga_academica')->get();
        $datosProfesores = DB::table('gestion_academica')->where('departamento', 'DINF')->get();
        $datosNotas = DB::table('notas_en_linea')->get()->keyBy('rut_alumno')->toArray();
    
        return [
            'alumnos_carga' => $datosAlumnos,
            'profesores' => $datosProfesores,  
            'notas' => $datosNotas,
        ];
    }

    public function activarCargaPeriodica(): array
    {
        Log::info('RF1 Carga UCSC iniciada a las ' . now()->toDateTimeString());
        $dataSets = $this->obtenerDatosParaProcesar();
        $resultados = [];

        // Cargar TODOS los profesores (DINF y externos) a la tabla profesor
        $profesores = $dataSets['profesores'];
        
        foreach ($profesores as $profesor) {
            Profesor::updateOrCreate(
                ['rut_profesor' => $profesor->rut_profesor],
                [
                    'nombre_profesor' => $profesor->nombre_profesor,
                    'apellido_profesor' => $profesor->apellido_profesor,
                    'departamento' => $profesor->departamento,
                ]
            );
        }
        $resultados[] = ['status' => 'GENERAL', 'mensaje' => count($profesores) . ' profesores cargados/actualizados (RF1).'];


        
        $datosAlumnos = $dataSets['alumnos_carga'];
        $datosNotas = $dataSets['notas'];

        foreach ($datosAlumnos as $mockA_object) {
    
        // 1. Convertir el objeto stdClass a un array asociativo.
        $datosA = (array) $mockA_object; 
            
        // 2. Usar la sintaxis de array para acceder a las claves
        $rutAlumno = $datosA['rut_alumno'];
            
        // 3. Convertir string separado por comas en array
        // Obtenemos el string 
        $strAsignaturas = $datosA['asignaturas'] ?? '';
        
        // Separar por la coma
        $asignaturas = explode(',', $strAsignaturas);
        
        if (!in_array(self::CODIGO_HABILITACION, $asignaturas)) {
            continue; 
        }
    
        // 2.2 REGISTRO OBLIGATORIO DE ALUMNO 
        Alumno::updateOrCreate(
            ['rut_alumno' => $rutAlumno], 
            [
                'nombre_alumno' => $datosA['nombre_alumno'],
                'apellido_alumno' => $datosA['apellido_alumno'],
            ]
        );

            
            // Buscar si la Habilitación ya existe en HabilProf (RF2: Ingreso de Datos)
            $habilitacion = Habilitacion::where('rut_alumno', $rutAlumno)->first();

            if ($habilitacion) {
                
                $notaData = $datosNotas[$rutAlumno] ?? null;
                
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