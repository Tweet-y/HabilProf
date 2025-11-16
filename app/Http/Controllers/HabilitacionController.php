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

class HabilitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Rescatar alumnos que tienen habilitación con sus datos completos
        $alumnos = Alumno::whereHas('habilitacion')->with(['habilitacion.proyecto', 'habilitacion.prTut'])->get();
        $profesores = Profesor::all();

        // Rescatar semestres que cuentan con habilitaciones
        $semestres = Habilitacion::distinct()
            ->orderBy('semestre_inicio', 'desc')
            ->pluck('semestre_inicio')
            ->toArray();

        // Si no hay semestres con habilitaciones, agregar los 2 próximos
        if (empty($semestres)) {
            $mesActual = date('n');
            $yearActual = date('Y');
            if ($mesActual <= 6) { // Primer semestre
                $semestres[] = $yearActual . '-1';
                $semestres[] = $yearActual . '-2';
            } else { // Segundo semestre
                $semestres[] = $yearActual . '-2';
                $semestres[] = ($yearActual + 1) . '-1';
            }
        }

        // Si se busca una habilitación específica, Rescatarla
        $habilitacion = null;
        if ($request->has('rut_alumno') && $request->rut_alumno) {
            $habilitacion = Habilitacion::where('rut_alumno', $request->rut_alumno)
                ->with(['proyecto', 'prTut'])
                ->first();
        }

        return view('actualizar_eliminar', compact('alumnos', 'profesores', 'habilitacion', 'semestres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Rescatar alumnos que no tienen habilitación
        $alumnos = Alumno::whereDoesntHave('habilitacion')->get();
        $profesores = Profesor::all();

        // Rescatar los 2 próximos semestres para crear habilitaciones
        $mesActual = date('n');
        $yearActual = date('Y');
        if ($mesActual <= 6) { // Primer semestre
            $semestres = [$yearActual . '-1', $yearActual . '-2'];
        } else { // Segundo semestre
            $semestres = [$yearActual . '-2', ($yearActual + 1) . '-1'];
        }

        return view('habilitacion_create', compact('alumnos', 'profesores', 'semestres'));
    }

    /**
     * Verifica el límite de habilitaciones (5) para un profesor en un semestre.
     * Retorna un mensaje de error si se supera el límite, o null si es válido.
     */
    private function verificarLimiteProfesor($rut_profesor, $semestre, $excludeRutAlumno = null)
    {
        $query = Habilitacion::where('semestre_inicio', $semestre)
            ->where(function($q) use ($rut_profesor) {
                $q->whereHas('proyecto', function($subQ) use ($rut_profesor) {
                    $subQ->where('rut_profesor_guia', $rut_profesor)
                         ->orWhere('rut_profesor_co_guia', $rut_profesor)
                         ->orWhere('rut_profesor_comision', $rut_profesor);
                })
                ->orWhereHas('prTut', function($subQ) use ($rut_profesor) {
                    $subQ->where('rut_profesor_tutor', $rut_profesor);
                });
            });

        if ($excludeRutAlumno) {
            $query->where('rut_alumno', '!=', $excludeRutAlumno);
        }

        $count = $query->count();

        if ($count >= 5) {
            $profesor = Profesor::find($rut_profesor);
            $nombre = $profesor ? $profesor->nombre_profesor . ' ' . $profesor->apellido_profesor : $rut_profesor;
            return "$nombre ya participa en 5 habilitaciones este semestre.";
        }

        return null; // Límite no superado
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHabilitacionRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Validaciones de negocio
            $semestre = $validatedData['semestre_inicio'];
            $profesores = [];

            if ($validatedData['tipo_habilitacion'] === 'PrTut') {
                $profesores = [$validatedData['seleccion_tutor_rut']];
            } else {
                $profesores = array_filter([$request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut]);
            }

            // Verificar que no haya profesores con múltiples roles
            $error = $this->validateMultipleRoles($request->tipo_habilitacion, $request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut);
            if ($error) {
                return redirect()->back()->with('error', $error)->withInput();
            }

            // Verificar límite de 5 habilitaciones por semestre por profesor
            $error = $this->checkProfessorLimits($profesores, $semestre);
            if ($error) {
                return redirect()->back()->with('error', $error)->withInput();
            }

            // Crear la habilitación
            $habilitacion = Habilitacion::create([
                'rut_alumno' => $validatedData['selector_alumno_rut'],
                'semestre_inicio' => $validatedData['semestre_inicio'],
                'titulo' => $validatedData['titulo'],
                'descripcion' => $validatedData['descripcion'],
                // la columna nota_final en la migración es NOT NULL con default 0.0
                // no enviar null explícitamente porque PostgreSQL lanza violación NOT NULL
                'nota_final' => 0.0,
                'fecha_nota' => null,
            ]);

            // Crear el registro específico según el tipo
            if ($validatedData['tipo_habilitacion'] === 'PrIng' || $validatedData['tipo_habilitacion'] === 'PrInv') {
                // Crear Proyecto
                Proyecto::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
                    'tipo_proyecto' => $validatedData['tipo_habilitacion'],
                    'rut_profesor_guia' => $validatedData['seleccion_guia_rut'],
                    'rut_profesor_co_guia' => $validatedData['seleccion_co_guia_rut'] ?: null,
                    'rut_profesor_comision' => $validatedData['seleccion_comision_rut'],
                ]);
            } elseif ($validatedData['tipo_habilitacion'] === 'PrTut') {
                // Crear PrTut
                PrTut::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHabilitacionRequest $request, $alumno)
    {
        $habilitacion = Habilitacion::where('rut_alumno', $alumno)->firstOrFail();

        $validatedData = $request->validated();

        // Validaciones de negocio
        $semestre = $validatedData['semestre_inicio'];
        $profesores = [];

        if ($validatedData['tipo_habilitacion'] === 'PrTut') {
            $profesores = [$validatedData['seleccion_tutor_rut']];
        } else {
            $profesores = array_filter([$validatedData['seleccion_guia_rut'], $validatedData['seleccion_co_guia_rut'], $validatedData['seleccion_comision_rut']]);
        }

        // Verificar que no haya profesores con múltiples roles
        $error = $this->validateMultipleRoles($validatedData['tipo_habilitacion'], $validatedData['seleccion_guia_rut'], $validatedData['seleccion_co_guia_rut'], $validatedData['seleccion_comision_rut']);
        if ($error) {
            return redirect()->back()->with('error', $error)->withInput();
        }

        // Verificar límite de 5 habilitaciones por semestre por profesor (excluyendo la actual)
        $error = $this->checkProfessorLimits($profesores, $semestre, $alumno);
        if ($error) {
            return redirect()->back()->with('error', $error)->withInput();
        }

        try {
            DB::transaction(function () use ($habilitacion, $validatedData) {

                // Actualizar la Habilitacion principal
                $habilitacion->update([
                    'semestre_inicio' => $validatedData['semestre_inicio'],
                    'titulo' => $validatedData['titulo'],
                    'descripcion' => $validatedData['descripcion'],
                ]);

                // Eliminar el registro de la tabla opuesta si el tipo cambió
                if (in_array($validatedData['tipo_habilitacion'], ['PrIng', 'PrInv'])) {
                    // Si cambia a PrIng/PrInv, eliminar PrTut si existe
                    if ($habilitacion->prTut) {
                        $habilitacion->prTut->delete();
                    }
                    Proyecto::updateOrCreate(
                        ['id_habilitacion' => $habilitacion->id_habilitacion],
                        [
                            'tipo_proyecto' => $validatedData['tipo_habilitacion'],
                            'rut_profesor_guia' => $validatedData['seleccion_guia_rut'],
                            'rut_profesor_co_guia' => $validatedData['seleccion_co_guia_rut'] ?? null,
                            'rut_profesor_comision' => $validatedData['seleccion_comision_rut'],
                        ]
                    );
                } elseif ($validatedData['tipo_habilitacion'] === 'PrTut') {
                    // Si cambia a PrTut, eliminar Proyecto si existe
                    if ($habilitacion->proyecto) {
                        $habilitacion->proyecto->delete();
                    }
                    PrTut::updateOrCreate(
                        ['id_habilitacion' => $habilitacion->id_habilitacion],
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
     * Remove the specified resource from storage.
     */
    public function destroy($alumno)
    {
        $habilitacion = Habilitacion::where('rut_alumno', $alumno)->firstOrFail();

        // Eliminar registros relacionados
        if ($habilitacion->proyecto) {
            $habilitacion->proyecto->delete();
        }
        if ($habilitacion->prTut) {
            $habilitacion->prTut->delete();
        }

        $habilitacion->delete();

        return redirect()->back()->with('success', 'Habilitación eliminada correctamente.');
    }

    /**
     * Validate that professors do not have multiple roles.
     */
    private function validateMultipleRoles($tipo, $guia, $co_guia, $comision)
    {
        if ($tipo === 'PrTut') {
            return null;
        }

        $profesores_roles = array_filter([$guia, $co_guia, $comision]);
        if (count($profesores_roles) != count(array_unique($profesores_roles))) {
            return 'Un profesor no puede tener múltiples roles en la misma habilitación.';
        }

        return null;
    }

    /**
     * Check if professors exceed the limit of 5 habilitations per semester.
     */
    private function checkProfessorLimits($profesores, $semestre, $excludeRutAlumno = null)
    {
        foreach ($profesores as $rut) {
            $query = Habilitacion::where('semestre_inicio', $semestre)
                ->where(function($q) use ($rut) {
                    $q->whereHas('proyecto', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_guia', $rut)
                             ->orWhere('rut_profesor_co_guia', $rut)
                             ->orWhere('rut_profesor_comision', $rut);
                    })
                    ->orWhereHas('prTut', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_tutor', $rut);
                    });
                });

            if ($excludeRutAlumno) {
                $query->where('rut_alumno', '!=', $excludeRutAlumno);
            }

            $count = $query->count();

            if ($count >= 5) {
                $profesor = Profesor::find($rut);
                $nombre = $profesor ? $profesor->nombre_profesor . ' ' . $profesor->apellido_profesor : $rut;
                return "El profesor $nombre ya participa en 5 habilitaciones este semestre.";
            }
        }

        return null;
    }

    /**
     * Check if professors exceed the limit of 5 habilitations per semester.
     */
    public function checkLimit(Request $request)
    {
        $semestre = $request->semestre_inicio;
        $tipo = $request->tipo_habilitacion;
        $profesores = [];
        $excludeRutAlumno = $request->exclude_rut_alumno; // Para excluir en updates

        if ($tipo === 'PrTut') {
            $profesores = [$request->seleccion_tutor_rut];
        } else {
            $profesores = array_filter([$request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut]);
        }

        $errors = [];

        foreach ($profesores as $rut) {
            $error = $this->verificarLimiteProfesor($rut, $semestre, $excludeRutAlumno);
            if ($error) {
                $errors[] = $error;
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
