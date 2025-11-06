<?php

namespace App\Http\Controllers;
use App\Models\Alumno;
use App\Models\Profesor;
use App\Models\Habilitacion;
use App\Models\Proyecto;
use App\Models\PrTut;
use Illuminate\Http\Request;

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

        // Si se busca una habilitación específica, obtenerla
        $habilitacion = null;
        if ($request->has('rut_alumno') && $request->rut_alumno) {
            $habilitacion = Habilitacion::where('rut_alumno', $request->rut_alumno)
                ->with(['proyecto', 'prTut'])
                ->first();
        }

        return view('actualizar_eliminar', compact('alumnos', 'profesores', 'habilitacion'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener alumnos que no tienen habilitación
        $alumnos = Alumno::whereDoesntHave('habilitacion')->get();
        $profesores = Profesor::all();

        // Generar semestres disponibles (solo los 2 próximos)
        $mesActual = date('n');
        $yearActual = date('Y');
        $semestres = [];

        if ($mesActual <= 6) { // Primer semestre
            $semestres[] = $yearActual . '-1';
            $semestres[] = $yearActual . '-2';
        } else { // Segundo semestre
            $semestres[] = $yearActual . '-2';
            $semestres[] = ($yearActual + 1) . '-1';
        }
        // Eliminar las líneas redundantes que causaban 4 opciones

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
                $rules['seleccion_co_guia_rut'] = 'nullable|exists:profesor,rut_profesor|different:seleccion_guia_rut,seleccion_comision_rut';
                $rules['seleccion_comision_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor|different:seleccion_guia_rut,seleccion_co_guia_rut';
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

        // Validar los datos requeridos
        $rules = [
            'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
            'semestre_inicio' => 'required|string',
            'titulo' => 'required|string|max:50|min:6|regex:/^[a-zA-Z0-9\s.,;:\'"&-_()]+$/',
            'descripcion' => 'required|string|max:500|min:30',
        ];

        if ($request->tipo_habilitacion === 'PrIng' || $request->tipo_habilitacion === 'PrInv') {
            $rules['seleccion_guia_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor';
            $rules['seleccion_co_guia_rut'] = 'nullable|exists:profesor,rut_profesor|different:seleccion_guia_rut,seleccion_comision_rut';
            $rules['seleccion_comision_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor|different:seleccion_guia_rut,seleccion_co_guia_rut';
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

        // Validaciones de negocio para actualización
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

        // Verificar si cambió el tipo de habilitación
        $oldType = $habilitacion->proyecto ? 'Proyecto' : 'PrTut';
        $newType = $request->tipo_habilitacion;

        // Actualizar campos comunes
        $habilitacion->update([
            'semestre_inicio' => $request->semestre_inicio,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
        ]);

        if ($oldType === 'Proyecto' && in_array($newType, ['PrIng', 'PrInv'])) {
            // Mismo tipo Proyecto, actualizar
            $habilitacion->proyecto->update([
                'tipo_proyecto' => $request->tipo_habilitacion,
                'rut_profesor_guia' => $request->seleccion_guia_rut,
                'rut_profesor_co_guia' => $request->seleccion_co_guia_rut ?: null,
                'rut_profesor_comision' => $request->seleccion_comision_rut,
            ]);
        } elseif ($oldType === 'PrTut' && $newType === 'PrTut') {
            // Mismo tipo PrTut, actualizar
            $habilitacion->prTut->update([
                'nombre_supervisor' => $request->nombre_supervisor,
                'nombre_empresa' => $request->nombre_empresa,
                'rut_profesor_tutor' => $request->seleccion_tutor_rut,
            ]);
        } else {
            // Cambió el tipo, eliminar el anterior y crear el nuevo
            if ($oldType === 'Proyecto') {
                $habilitacion->proyecto->delete();
            } else {
                $habilitacion->prTut->delete();
            }

            if ($newType === 'PrTut') {
                PrTut::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
                    'nombre_supervisor' => $request->nombre_supervisor,
                    'nombre_empresa' => $request->nombre_empresa,
                    'rut_profesor_tutor' => $request->seleccion_tutor_rut,
                ]);
            } else {
                Proyecto::create([
                    'id_habilitacion' => $habilitacion->id_habilitacion,
                    'tipo_proyecto' => $request->tipo_habilitacion,
                    'rut_profesor_guia' => $request->seleccion_guia_rut,
                    'rut_profesor_co_guia' => $request->seleccion_co_guia_rut ?: null,
                    'rut_profesor_comision' => $request->seleccion_comision_rut,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Los datos fueron modificados correctamente.');
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
}
