<?php

namespace App\Http\Controllers;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Habilitacion;
use App\Models\Proyecto;
use App\Models\PrTut;
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
        // Obtener alumnos que tienen habilitación con sus datos completos
        $alumnos = Alumno::whereHas('habilitacion')->with(['habilitacion.proyecto', 'habilitacion.prTut'])->get();
        $profesores = Profesor::all();

        // Obtener semestres que cuentan con habilitaciones
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

        // Si se busca una habilitación específica, obtenerla
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
        // Obtener alumnos que no tienen habilitación
        $alumnos = Alumno::whereDoesntHave('habilitacion')->get();
        $profesores = Profesor::all();

        // Obtener los 2 próximos semestres para crear habilitaciones
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos requeridos
            $rules = [
                'selector_alumno_rut' => 'required|exists:alumno,rut_alumno',
                'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
                'semestre_inicio' => 'required|string',
                'titulo' => 'required|string|max:50|min:6|regex:/^[a-zA-Z0-9\s.,;:\'"&-_()]+$/',
                'descripcion' => 'required|string|max:500|min:30',
            ];

            if ($request->tipo_habilitacion === 'PrIng' || $request->tipo_habilitacion === 'PrInv') {
                $rules['seleccion_guia_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor';
                $rules['seleccion_co_guia_rut'] = 'nullable|exists:profesor,rut_profesor';
                $rules['seleccion_comision_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor';
            } elseif ($request->tipo_habilitacion === 'PrTut') {
                $rules['nombre_empresa'] = 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z0-9\s]+$/u';
                $rules['nombre_supervisor'] = 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u';
                $rules['seleccion_tutor_rut'] = 'required_if:tipo_habilitacion,PrTut|nullable|exists:profesor,rut_profesor';
            }

            // Mensajes personalizados
            $messages = [
                '*.required' => 'El campo es obligatorio.',
                '*.required_if' => 'Este campo es obligatorio para la modalidad seleccionada.',
                '*.exists' => 'El valor seleccionado no es válido.',
 
                // Mensajes específicos para profesores
                'seleccion_guia_rut.required_if' => 'Debe seleccionar un Profesor Guía.',
                'seleccion_comision_rut.required_if' => 'Debe seleccionar un Profesor Comisión.',
                'seleccion_tutor_rut.required_if' => 'Debe seleccionar un Profesor Tutor.',

                // Mensajes para duplicados
                '*.different' => 'Un profesor no puede tener múltiples roles (Guía, Co-Guía, Comisión).',
            ];

            $request->validate($rules, $messages);

            // Validaciones de negocio
            $semestre = $request->semestre_inicio;
            $profesores = [];

            if ($request->tipo_habilitacion === 'PrTut') {
                $profesores = [$request->seleccion_tutor_rut];
            } else {
                $profesores = array_filter([$request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut]);
                // Verificar que no haya profesores con múltiples roles
                if (count($profesores) != count(array_unique($profesores))) {
                    return redirect()->back()->with('error', 'Un profesor no puede tener múltiples roles en la misma habilitación.')->withInput();
                }
            }

            // Verificar límite de 5 habilitaciones por semestre por profesor
            foreach ($profesores as $rut) {
                $count = Habilitacion::where('semestre_inicio', $semestre)
                    ->where(function($q) use ($rut) {
                        $q->whereHas('proyecto', function($subQ) use ($rut) {
                            $subQ->where('rut_profesor_guia', $rut)
                                 ->orWhere('rut_profesor_co_guia', $rut)
                                 ->orWhere('rut_profesor_comision', $rut);
                        })
                        ->orWhereHas('prTut', function($subQ) use ($rut) {
                            $subQ->where('rut_profesor_tutor', $rut);
                        });
                    })
                    ->count();

                if ($count >= 5) {
                    $profesor = Profesor::find($rut);
                    $nombre = $profesor ? $profesor->nombre_profesor . ' ' . $profesor->apellido_profesor : $rut;
                    return redirect()->back()->with('error', "El profesor $nombre ya participa en 5 habilitaciones este semestre.")->withInput();
                }
            }

            // Crear la habilitación
            $habilitacion = Habilitacion::create([
                'rut_alumno' => $request->selector_alumno_rut,
                'semestre_inicio' => $request->semestre_inicio,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                // la columna nota_final en la migración es NOT NULL con default 0.0
                // no enviar null explícitamente porque PostgreSQL lanza violación NOT NULL
                'nota_final' => 0.0,
                'fecha_nota' => null,
            ]);

            // Crear el registro específico según el tipo
            if ($request->tipo_habilitacion === 'PrIng' || $request->tipo_habilitacion === 'PrInv') {
                // Crear Proyecto
                Proyecto::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
                    'tipo_proyecto' => $request->tipo_habilitacion,
                    'rut_profesor_guia' => $request->seleccion_guia_rut,
                    'rut_profesor_co_guia' => $request->seleccion_co_guia_rut ?: null,
                    'rut_profesor_comision' => $request->seleccion_comision_rut,
                ]);
            } elseif ($request->tipo_habilitacion === 'PrTut') {
                // Crear PrTut
                PrTut::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
                    'nombre_supervisor' => $request->nombre_supervisor,
                    'nombre_empresa' => $request->nombre_empresa,
                    'rut_profesor_tutor' => $request->seleccion_tutor_rut,
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
    public function update(Request $request, $alumno)
    {
        $habilitacion = Habilitacion::where('rut_alumno', $alumno)->firstOrFail();

        // REGLAS DE VALIDACIÓN (MEJORADAS)
        $rules = [
            'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
            'semestre_inicio' => 'required|string',
            'titulo' => 'required|string|max:50|min:6|regex:/^[a-zA-Z0-9\s.,;:\'"&-_()]+$/',
            'descripcion' => 'required|string|max:500|min:30',
    
            // PrIng/PrInv Rules
            'seleccion_guia_rut' => 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor',
            'seleccion_comision_rut' => 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor',
            'seleccion_co_guia_rut' => 'nullable|exists:profesor,rut_profesor',
    
            // PrTut Rules
            'nombre_empresa' => 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z0-9\s]+$/u',
            'nombre_supervisor' => 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u',
            'seleccion_tutor_rut' => 'required_if:tipo_habilitacion,PrTut|nullable|exists:profesor,rut_profesor',
        ];

        // MENSAJES PERSONALIZADOS
        $messages = [
            '*.required' => 'Este campo es obligatorio.',
            '*.required_if' => 'Este campo es obligatorio para la modalidad seleccionada.',
            '*.exists' => 'El valor seleccionado no es válido o no existe.',
            
            // Mensajes para duplicados
            'seleccion_comision_rut.different' => 'El Profesor de Comisión no puede ser el mismo que el Guía.',
            'seleccion_co_guia_rut.different' => 'El Co-Guía no puede ser el mismo que el Guía o el de Comisión.',
        ];

        // Validar los datos
        $validatedData = $request->validate($rules, $messages);

        $habilitacion = Habilitacion::where('rut_alumno', $alumno)->firstOrFail();

        // Validaciones de negocio
        $semestre = $validatedData['semestre_inicio'];
        $profesores = [];

        if ($validatedData['tipo_habilitacion'] === 'PrTut') {
            $profesores = [$validatedData['seleccion_tutor_rut']];
        } else {
            $profesores = array_filter([$validatedData['seleccion_guia_rut'], $validatedData['seleccion_co_guia_rut'], $validatedData['seleccion_comision_rut']]);
            // Verificar que no haya profesores con múltiples roles
            if (count($profesores) != count(array_unique($profesores))) {
                return redirect()->back()->with('error', 'Un profesor no puede tener múltiples roles en la misma habilitación.')->withInput();
            }
        }

        // Verificar límite de 5 habilitaciones por semestre por profesor (excluyendo la actual)
        foreach ($profesores as $rut) {
            $count = Habilitacion::where('rut_alumno', '!=', $alumno)
                ->where('semestre_inicio', $semestre)
                ->where(function($q) use ($rut) {
                    $q->whereHas('proyecto', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_guia', $rut)
                             ->orWhere('rut_profesor_co_guia', $rut)
                             ->orWhere('rut_profesor_comision', $rut);
                    })
                    ->orWhereHas('prTut', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_tutor', $rut);
                    });
                })
                ->count();

            if ($count >= 5) {
                $profesor = Profesor::find($rut);
                $nombre = $profesor ? $profesor->nombre_profesor . ' ' . $profesor->apellido_profesor : $rut;
                return redirect()->back()->with('error', "El profesor $nombre ya participa en 5 habilitaciones este semestre.")->withInput();
            }
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
     * Check if professors exceed the limit of 5 habilitations per semester.
     */
    public function checkLimit(Request $request)
    {
        $semestre = $request->semestre_inicio;
        $tipo = $request->tipo_habilitacion;
        $profesores = [];

        if ($tipo === 'PrTut') {
            $profesores = [$request->seleccion_tutor_rut];
        } else {
            $profesores = array_filter([$request->seleccion_guia_rut, $request->seleccion_co_guia_rut, $request->seleccion_comision_rut]);
        }

        $errors = [];

        foreach ($profesores as $rut) {
            $count = Habilitacion::where('semestre_inicio', $semestre)
                ->where(function($q) use ($rut) {
                    $q->whereHas('proyecto', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_guia', $rut)
                             ->orWhere('rut_profesor_co_guia', $rut)
                             ->orWhere('rut_profesor_comision', $rut);
                    })
                    ->orWhereHas('prTut', function($subQ) use ($rut) {
                        $subQ->where('rut_profesor_tutor', $rut);
                    });
                })
                ->count();

            if ($count >= 5) {
                $profesor = Profesor::find($rut);
                $nombre = $profesor ? $profesor->nombre_profesor . ' ' . $profesor->apellido_profesor : $rut;
                $errors[] = "El profesor $nombre ya participa en 5 habilitaciones este semestre.";
            }
        }

        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
