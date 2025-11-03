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
    public function index()
    {
        // Obtener alumnos que tienen habilitación
        $alumnos = Alumno::whereHas('habilitacion')->get();
        $profesores = Profesor::all();
        return view('actualizar_eliminar', compact('alumnos', 'profesores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener alumnos que no tienen habilitación
        $alumnos = Alumno::whereDoesntHave('habilitacion')->get();
        $profesores = Profesor::all();
        return view('habilitacion_create', compact('alumnos', 'profesores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos requeridos
            $rules = [
                'selector_alumno_rut' => 'required|exists:alumnos,rut_alumno',
                'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
                'semestre_inicio' => 'required',
                'titulo' => 'required|string|max:80|min:6|regex:/^[a-zA-Z0-9\s.,;:\'"&-_()]+$/',
                'descripcion' => 'required|string|max:500|min:30',
            ];

            if ($request->tipo_habilitacion === 'PrIng' || $request->tipo_habilitacion === 'PrInv') {
                $rules['seleccion_guia_rut'] = 'required|exists:profesors,rut_profesor';
                $rules['seleccion_co_guia_rut'] = 'nullable|exists:profesors,rut_profesor';
                $rules['seleccion_comision_rut'] = 'required|exists:profesors,rut_profesor';
            } elseif ($request->tipo_habilitacion === 'PrTut') {
                $rules['nombre_empresa'] = 'required|string|max:50|regex:/^[a-zA-Z0-9\s]+$/';
                $rules['nombre_supervisor'] = 'required|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/';
                $rules['seleccion_tutor_rut'] = 'required|exists:profesors,rut_profesor';
            }

            $request->validate($rules);

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
                'nota_final' => null,
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
