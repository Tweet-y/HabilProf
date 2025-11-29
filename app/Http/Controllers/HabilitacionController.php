<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Habilitacion;
use App\Models\Proyecto;
use App\Models\PrTut;
use App\Http\Requests\StoreHabilitacionRequest;
use App\Http\Requests\UpdateHabilitacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para gestionar habilitaciones académicas.
 * Maneja CRUD de habilitaciones, validaciones de negocio y límites de profesores.
*/
class HabilitacionController extends Controller
{
    /**
     * Muestra el formulario para crear una nueva habilitación.
     * Solo incluye alumnos sin habilitación activa.
    */
    public function create()
    {
        // Obtener alumnos disponibles (sin habilitación)
        $alumnos = Alumno::whereDoesntHave('habilitacion')->get();

        // Obtener profesores DINF para guía, comisión y tutor
        $profesores_dinf = Profesor::where('departamento', 'DINF')
            ->orderBy('apellido_profesor')
            ->orderBy('nombre_profesor')
            ->get();

        // Obtener TODOS los profesores para co-guía (DINF y otros departamentos)
        $profesores_ucsc = Profesor::orderBy('departamento')
            ->orderBy('apellido_profesor')
            ->orderBy('nombre_profesor')
            ->get();

        // Calcular próximos 2 semestres para nuevas habilitaciones
        $mesActual = date('n');
        $yearActual = date('Y');
        if ($mesActual <= 6) { // Primer semestre
            $semestres = [$yearActual . '-1', $yearActual . '-2'];
        } else { // Segundo semestre
            $semestres = [$yearActual . '-2', ($yearActual + 1) . '-1'];
        }

        return view('habilitacion_create', compact('alumnos', 'profesores_dinf', 'profesores_ucsc', 'semestres'));
    }

    /**
     * Crea una nueva habilitación en la base de datos.
     * Incluye validaciones de negocio y creación de registros relacionados.
    */
    public function store(StoreHabilitacionRequest $request)
    {
        try {
            $validatedData = $request->validated();
            
            // Preparar validaciones de negocio
            $semestre = $validatedData['semestre_inicio'];
            $profesores = [];
            
            // Determinar profesores según tipo de habilitación
            if ($validatedData['tipo_habilitacion'] === 'PrTut') {
                $profesores = [$validatedData['seleccion_tutor_rut']];
            } else {
                // Para PrIng/PrInv: guía, co-guía (opcional), comisión
                $profesores = array_filter([$request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut]);
            }
            
            // Validar que no haya profesores con múltiples roles
            $error = $this->validarMultiplesRoles($request->tipo_habilitacion, $request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut);
            if ($error) {
                return redirect()->back()->with('error', $error)->withInput();
            }
            
            // Validar límite de 5 habilitaciones por profesor por semestre
            $error = $this->validarLimitesProfesoresBackend($profesores, $semestre);
            if ($error) {
                return redirect()->back()->with('error', $error)->withInput();
            }

            // Crear la habilitación principal
            $habilitacion = Habilitacion::create([
                'rut_alumno' => $validatedData['selector_alumno_rut'],
                'semestre_inicio' => $validatedData['semestre_inicio'],
                'titulo' => $validatedData['titulo'],
                'descripcion' => $validatedData['descripcion'],
                'nota_final' => 0.0,
                'fecha_nota' => null,
            ]);
            
            // Crear registro específico según tipo
            if ($validatedData['tipo_habilitacion'] === 'PrIng' || $validatedData['tipo_habilitacion'] === 'PrInv') {
                // Crear registro de proyecto
                Proyecto::create([
                    'rut_alumno' => $habilitacion->rut_alumno,
                    'tipo_proyecto' => $validatedData['tipo_habilitacion'],
                    'rut_profesor_guia' => $validatedData['seleccion_guia_rut'],
                    'rut_profesor_co_guia' => $validatedData['seleccion_co_guia_rut'] ?: null,
                    'rut_profesor_comision' => $validatedData['seleccion_comision_rut'],
                ]);
            } elseif ($validatedData['tipo_habilitacion'] === 'PrTut') {
                // Crear registro de práctica tutelada
                PrTut::create([
                    'rut_alumno' => $habilitacion->rut_alumno,
                    'nombre_supervisor' => $validatedData['nombre_supervisor'],
                    'nombre_empresa' => $validatedData['nombre_empresa'],
                    'rut_profesor_tutor' => $validatedData['seleccion_tutor_rut'],
                ]);
            }
            
            return redirect()->back()->with('success', 'Habilitación creada correctamente');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear habilitación: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Elimina una habilitación y sus registros relacionados.
     */
    public function destroy($alumno)
    {
        $habilitacion = Habilitacion::where('rut_alumno', $alumno)->firstOrFail();
        
        // Eliminar registros relacionados primero (por integridad referencial)
        if ($habilitacion->proyecto) {
            $habilitacion->proyecto->delete();
        }
        if ($habilitacion->prTut) {
            $habilitacion->prTut->delete();
        }
        
        // Eliminar la habilitación principal
        $habilitacion->delete();
        
        return redirect()->back()->with('success', 'Habilitación eliminada correctamente.');
    }
    
    /**
     * Muestra la vista de actualizar/eliminar habilitaciones.
     * Lista alumnos con habilitaciones disponibles para selección.
     * Si se recibe rut_alumno, busca y muestra la habilitación específica.
     */
    public function index(Request $request)
    {
        // Obtener alumnos con habilitaciones y sus relaciones
        $alumnos = Alumno::whereHas('habilitacion')->with(['habilitacion.proyecto', 'habilitacion.prTut'])->get();

        // Obtener profesores DINF para guía, comisión y tutor
        $profesores_dinf = Profesor::where('departamento', 'DINF')
            ->orderBy('apellido_profesor')
            ->orderBy('nombre_profesor')
            ->get();

        // Obtener TODOS los profesores para co-guía (DINF y otros departamentos)
        $profesores_ucsc = Profesor::orderBy('departamento')
            ->orderBy('apellido_profesor')
            ->orderBy('nombre_profesor')
            ->get();

        // Obtener semestres únicos con habilitaciones existentes
        $semestres = Habilitacion::distinct()
            ->orderBy('semestre_inicio', 'desc')
            ->pluck('semestre_inicio')
            ->toArray();

        // En caso de no haber semestres registrados
        if (empty($semestres)) {
            // Lógica por defecto (no es necesario de implementar ahora)
        }

        // Buscar habilitación si se recibió rut_alumno
        $habilitacion = null;
        if ($request->has('rut_alumno') && !empty($request->rut_alumno)) {
            $habilitacion = Habilitacion::where('rut_alumno', $request->rut_alumno)
                ->with(['proyecto', 'prTut'])
                ->first();

            // Si se encontró habilitación, limitar semestres para edición
            if ($habilitacion) {
                $semestres = $this->calculaSemestresActualizacion($habilitacion->semestre_inicio);
            }
        }

        return view('actualizar_eliminar', compact('alumnos', 'profesores_dinf', 'profesores_ucsc', 'habilitacion', 'semestres'));
    }
    
    /**
     * Muestra el formulario de edición para una habilitación específica.
     * Carga la habilitación seleccionada y prepara datos para edición.
     */
    public function edit($rut_alumno)
    {
        // Obtener alumnos con habilitaciones para el selector
        $alumnos = Alumno::whereHas('habilitacion')->with(['habilitacion.proyecto', 'habilitacion.prTut'])->get();

        // Obtener profesores DINF para guía, comisión y tutor
        $profesores_dinf = Profesor::where('departamento', 'DINF')
            ->orderBy('apellido_profesor')
            ->orderBy('nombre_profesor')
            ->get();

        // Obtener TODOS los profesores para co-guía (DINF y otros departamentos)
        $profesores_ucsc = Profesor::orderBy('departamento')
            ->orderBy('apellido_profesor')
            ->orderBy('nombre_profesor')
            ->get();

        // Buscar la habilitación específica a editar
        $habilitacion = Habilitacion::where('rut_alumno', $rut_alumno)
            ->with(['proyecto', 'prTut'])
            ->firstOrFail();

        // Limitar semestres a anterior, actual y siguiente para edición
        $semestres = $this->calculaSemestresActualizacion($habilitacion->semestre_inicio);

        return view('actualizar_eliminar', compact('alumnos', 'profesores_dinf', 'profesores_ucsc', 'habilitacion', 'semestres'));
    }
 
    /**
     * Actualiza una habilitación existente.
     * Maneja cambios de tipo y validaciones de negocio.
     */
    public function update(UpdateHabilitacionRequest $request, $alumno)
    {
        $habilitacion = Habilitacion::where('rut_alumno', $alumno)->firstOrFail();
        
        $validatedData = $request->validated();
        
        // Preparar validaciones de negocio
        $semestre = $validatedData['semestre_inicio'];
        $profesores = [];

        // Determinar profesores según tipo
        if ($validatedData['tipo_habilitacion'] === 'PrTut') {
            $profesores = [$validatedData['seleccion_tutor_rut']];
        } else {
            $profesores = array_filter([$request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut]);
        }

        // Validar roles únicos
        $error = $this->validarMultiplesRoles($validatedData['tipo_habilitacion'], $request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut);
        if ($error) {
            return redirect()->route('habilitaciones.edit', $alumno)->with('error', $error)->withInput();
        }

        // Validar límites de profesores (excluyendo la habilitación actual)
        $error = $this->validarLimitesProfesoresBackend($profesores, $semestre, $alumno);
        if ($error) {
            return redirect()->route('habilitaciones.edit', $alumno)->with('error', $error)->withInput();
        }

        try {
            // Usar transacción para asegurar consistencia
            DB::transaction(function () use ($habilitacion, $validatedData) {

                // Actualizar datos principales de la habilitación
                $habilitacion->update([
                    'semestre_inicio' => $validatedData['semestre_inicio'],
                    'titulo' => $validatedData['titulo'],
                    'descripcion' => $validatedData['descripcion'],
                ]);

                // Manejar cambio de tipo de habilitación
                if (in_array($validatedData['tipo_habilitacion'], ['PrIng', 'PrInv'])) {
                    // Cambiando a proyecto: eliminar PrTut si existe y crear/actualizar Proyecto
                    if ($habilitacion->prTut) {
                        $habilitacion->prTut->delete();
                    }
                    Proyecto::updateOrCreate(
                        ['rut_alumno' => $habilitacion->rut_alumno],
                        [
                            'tipo_proyecto' => $validatedData['tipo_habilitacion'],
                            'rut_profesor_guia' => $validatedData['seleccion_guia_rut'],
                            'rut_profesor_co_guia' => $validatedData['seleccion_co_guia_rut'] ?? null,
                            'rut_profesor_comision' => $validatedData['seleccion_comision_rut'],
                        ]
                    );
                } elseif ($validatedData['tipo_habilitacion'] === 'PrTut') {
                    // Cambiando a PrTut: eliminar Proyecto si existe y crear/actualizar PrTut
                    if ($habilitacion->proyecto) {
                        $habilitacion->proyecto->delete();
                    }
                    PrTut::updateOrCreate(
                        ['rut_alumno' => $habilitacion->rut_alumno],
                        [
                            'nombre_empresa' => $validatedData['nombre_empresa'],
                            'nombre_supervisor' => $validatedData['nombre_supervisor'],
                            'rut_profesor_tutor' => $validatedData['seleccion_tutor_rut'],
                        ]
                    );
                }
            });

        } catch (\Exception $e) {
            Log::error('Error al actualizar habilitación: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar la habilitación. Por favor, intente nuevamente.');
        }
        
        return redirect()->route('habilitaciones.index')->with('success', 'Habilitación actualizada correctamente.');
    }
   
    /**
     * Calcula semestres disponibles para actualización: anterior, actual y siguiente.
     * Limita opciones para evitar cambios drásticos.
     */
    private function calculaSemestresActualizacion($currentSemestre)
    {
        // Parsear semestre actual (ej: "2025-1" -> año=2025, semestre=1)
        list($year, $semester) = explode('-', $currentSemestre);
        $year = (int)$year;
        $semester = (int)$semester;
    
        $semestres = [];
    
        // Semestre anterior
        if ($semester == 1) {
            // Si actual es 1, anterior es 2 del año previo
            $prevYear = $year - 1;
            if ($prevYear >= 2025) { // No ir antes de 2025
                $semestres[] = $prevYear . '-2';
            }
        } else {
            // Si actual es 2, anterior es 1 del mismo año
            $semestres[] = $year . '-1';
        }
    
        // Semestre actual
        $semestres[] = $currentSemestre;
    
        // Semestre siguiente
        if ($semester == 1) {
            // Si actual es 1, siguiente es 2 del mismo año
            $semestres[] = $year . '-2';
        } else {
            // Si actual es 2, siguiente es 1 del año siguiente
            $nextYear = $year + 1;
            $semestres[] = $nextYear . '-1';
        }
    
        return $semestres;
    }

    /**
     * Muestra una habilitación específica (no implementado).
     */
    public function show(string $id)
    {
        // Método no implementado en esta versión
    }


    /**
     * DIFERENCIAS ENTRE MÉTODOS DE VALIDACIÓN DE LÍMITES DE PROFESORES:
     *
     * 1. validarLimiteProfesorIndividual (UTILITARIO PRIVADO):
     *    - Valida UN SOLO profesor específico
     *    - Retorna string|null (mensaje error o null si válido)
     *    - Es la función base reutilizada por los otros métodos
     *    - Usado internamente por validarLimitesProfesoresBackend y checkLimit
     *
     * 2. validarLimitesProfesoresBackend (VALIDACIÓN BACKEND PRIVADA):
     *    - Valida MÚLTIPLES profesores en una sola llamada
     *    - Retorna string|null (primer error encontrado o null si todos OK)
     *    - Usado en store() y update() para validaciones del lado servidor
     *    - Internamente itera y llama validarLimiteProfesorIndividual por cada profesor
     *
     * 3. checkLimit (ENDPOINT AJAX PÚBLICO):
     *    - Endpoint web accesible desde JavaScript
     *    - Valida múltiples profesores para validación en tiempo real
     *    - Retorna JSON Response (array de errores o mensaje OK)
     *    - Usado por AJAX en formularios para feedback inmediato al usuario
     *    - Internamente usa validarLimiteProfesorIndividual() para cada profesor
     */

    /**
     * Verifica si un profesor supera el límite de 5 habilitaciones por semestre.
     * MÉTODO UTILITARIO: valida UN SOLO profesor, retorna mensaje error o null.
     * Base reutilizable usada por validarLimitesProfesoresBackend y checkLimit.
     *
     * @param string $rut_profesor RUT del profesor
     * @param string $semestre Semestre a verificar
     * @param string|null $excludeRutAlumno Excluir esta habilitación (para updates)
     * @return string|null Mensaje de error o null si válido
     */
    private function validarLimiteProfesorIndividual($rut_profesor, $semestre, $excludeRutAlumno = null)
    {
        // Contar habilitaciones del profesor en el semestre
        $query = Habilitacion::where('semestre_inicio', $semestre)
            ->where(function($q) use ($rut_profesor) {
                // En proyectos: guía, co-guía o comisión
                $q->whereHas('proyecto', function($subQ) use ($rut_profesor) {
                    $subQ->where('rut_profesor_guia', $rut_profesor)
                         ->orWhere('rut_profesor_co_guia', $rut_profesor)
                         ->orWhere('rut_profesor_comision', $rut_profesor);
                })
                // En prácticas tuteladas: tutor
                ->orWhereHas('prTut', function($subQ) use ($rut_profesor) {
                    $subQ->where('rut_profesor_tutor', $rut_profesor);
                });
            });

        // Excluir habilitación actual en caso de update
        if ($excludeRutAlumno) {
            $query->where('rut_alumno', '!=', $excludeRutAlumno);
        }

        $count = $query->count();

        // Verificar límite
        if ($count >= 5) {
            // Buscar profesor en la tabla profesor (contiene DINF y externos)
            $profesor = Profesor::find($rut_profesor);
            $nombre = $profesor ? $profesor->nombre_profesor . ' ' . $profesor->apellido_profesor : $rut_profesor;
            return "$nombre ya participa en 5 habilitaciones este semestre.";
        }

        return null; // Límite no superado
    }

    /**
     * Valida que no haya profesores con múltiples roles en una habilitación.
     * Solo aplica para PrIng/PrInv.
     */
    private function validarMultiplesRoles($tipo, $guia, $co_guia, $comision)
    {
        // PrTut no tiene múltiples roles
        if ($tipo === 'PrTut') {
            return null;
        }

        // Verificar duplicados en roles de proyecto
        $profesores_roles = array_filter([$guia, $co_guia, $comision]);
        if (count($profesores_roles) != count(array_unique($profesores_roles))) {
            return 'Un profesor no puede tener múltiples roles en la misma habilitación.';
        }

        return null;
    }

    /**
     * Verifica que ningún profesor exceda el límite de 5 habilitaciones por semestre.
     */
    private function validarLimitesProfesoresBackend($profesores, $semestre, $excludeRutAlumno = null)
    {
        foreach ($profesores as $rut) {
            // Contar habilitaciones del profesor en el semestre
            $query = Habilitacion::where('semestre_inicio', $semestre)
                ->where(function($q) use ($rut) {
                    // En proyectos: guía, co-guía o comisión
                    $q->whereHas('proyecto', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_guia', $rut)
                             ->orWhere('rut_profesor_co_guia', $rut)
                             ->orWhere('rut_profesor_comision', $rut);
                    })
                    // En prácticas tuteladas: tutor
                    ->orWhereHas('prTut', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_tutor', $rut);
                    });
                });

            // Excluir habilitación actual en updates
            if ($excludeRutAlumno) {
                $query->where('rut_alumno', '!=', $excludeRutAlumno);
            }

            $count = $query->count();

            // Verificar límite
            if ($count >= 5) {
                // Buscar profesor en la tabla profesor (contiene DINF y externos)
                $profesor = Profesor::find($rut);
                $nombre = $profesor ? $profesor->nombre_profesor . ' ' . $profesor->apellido_profesor : $rut;
                return "El profesor $nombre ya participa en 5 habilitaciones este semestre.";
            }
        }

        return null;
    }

    /**
     * Verifica límites de profesores vía AJAX (usado en formularios).
     * Retorna errores JSON para validación en frontend.
     */
    public function checkLimit(Request $request)
    {
        $semestre = $request->semestre_inicio;
        $tipo = $request->tipo_habilitacion;
        $profesores = [];
        $excludeRutAlumno = $request->exclude_rut_alumno; // Para excluir en updates

        // Determinar profesores según tipo
        if ($tipo === 'PrTut') {
            $profesores = [$request->seleccion_tutor_rut];
        } else {
            $profesores = array_filter([$request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut]);
        }

        $errors = [];

        // Verificar cada profesor
        foreach ($profesores as $rut) {
            $error = $this->validarLimiteProfesorIndividual($rut, $semestre, $excludeRutAlumno);
            if ($error) {
                $errors[] = $error;
            }
        }

        // Retornar errores o OK
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
