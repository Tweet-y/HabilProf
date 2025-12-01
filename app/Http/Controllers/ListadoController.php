<?php
// Listado ordenado por semestre (Vicente Alarcón)
namespace App\Http\Controllers;

use App\Models\Habilitacion;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ListadoController extends Controller
{
    public function index()
    {
        $semestres_disponibles = Habilitacion::distinct()
            ->orderBy('semestre_inicio', 'desc')
            ->pluck('semestre_inicio');

        return view('listados', [
            'semestres_disponibles' => $semestres_disponibles,
            'tipo_listado' => null,
            'habilitaciones' => collect([]),
            'profesores_dinf' => collect([])
        ]);
    }

    public function generar(Request $request)
    {
        \Log::info('Método de la solicitud: ' . $request->method());
        \Log::info('Datos de la solicitud:', $request->all());

        try {
            // Si es POST, validar y guardar en sesión
            if ($request->isMethod('post')) {
                $validated = $request->validate([
                    'tipo_listado' => ['required', 'in:Listado Semestral,Listado Histórico'],
                    // semestre en formato AAAA-S, año entre 2025 y 2050, S en [1,2]
                    'semestre' => ['required_if:tipo_listado,Listado Semestral', 'regex:/^(202[5-9]|20[3-4][0-9]|2050)-(1|2)$/']
                ], [
                    'semestre.regex' => 'El semestre debe tener el formato AAAA-S con año entre 2025 y 2050 y semestre 1 o 2.'
                ]);

                session([
                    'tipo_listado' => $validated['tipo_listado'],
                    'semestre' => $request->semestre
                ]);
            }

            // Obtener datos de la sesión o del request
            $tipo_listado = session('tipo_listado', $request->tipo_listado);
            $semestre = session('semestre', $request->semestre);

            // Si no hay tipo de listado, volver al inicio
            if (!$tipo_listado) {
                return redirect()->route('listados');
            }

            \Log::info('Tipo de listado: ' . $tipo_listado);
            \Log::info('Semestre: ' . ($semestre ?? 'no especificado'));

            // Crear request con los datos combinados
            $newRequest = new Request();
            $newRequest->merge([
                'tipo_listado' => $tipo_listado,
                'semestre' => $semestre,
                'page' => $request->page
            ]);

            return $tipo_listado === 'Listado Semestral' 
                ? $this->generarListadoSemestral($newRequest)
                : $this->generarListadoHistorico($newRequest);
        } catch (\Exception $e) {
            \Log::error('Error al generar listado: ' . $e->getMessage());
            return back()->with('error', 'Error al generar el listado: ' . $e->getMessage());
        }
    }
    // 4.1 tipo_listado: Texto alfabético que puede tomar dos valores, “Listado Semestral” y “Listado Histórico.
    private function generarListadoSemestral(Request $request)
    {
        try {
            $semestre = $request->semestre;
            \Log::info('Generando listado semestral para el semestre: ' . $semestre);

            $query_params = $request->only(['tipo_listado', 'semestre']);
            // 4.4.1.3 El sistema tiene que recuperar los campos comunes para todos los tipo_habilitación con los siguientes datos.
            // Campos comunes para ambas consultas
            $commonFields = [
                'h.id_habilitacion',
                'h.rut_alumno',
                'h.nota_final',
                'h.fecha_nota',
                'h.semestre_inicio',
                'h.descripcion',
                'h.titulo',
                'a.nombre_alumno',
                'a.apellido_alumno',
                // Campo tipo_habilitacion se define en cada consulta
                // Campos de profesor principal (guía o tutor)
                'nombre_profesor_guia',
                'apellido_profesor_guia',
                'nombre_profesor_co_guia',
                'apellido_profesor_co_guia',
                'nombre_profesor_comision',
                'apellido_profesor_comision',
                // Campos de empresa y supervisor
                'empresa',
                'supervisor',
                'tipo_registro'
            ];

            // Subconsulta para proyectos
            $proyectos = DB::table('habilitacion as h')
                ->select([
                    'h.id_habilitacion',
                    'h.rut_alumno',
                    'h.nota_final',
                    'h.fecha_nota',
                    'h.semestre_inicio',
                    'h.descripcion',
                    'h.titulo',
                    'a.nombre_alumno',
                    'a.apellido_alumno',
                    'p.tipo_proyecto as tipo_habilitacion',
                    'p.rut_profesor_guia as rut_profesor_guia',
                    'p.rut_profesor_co_guia as rut_profesor_co_guia',
                    'p.rut_profesor_comision as rut_profesor_comision',
                    'pg.nombre_profesor as nombre_profesor_guia',
                    'pg.apellido_profesor as apellido_profesor_guia',
                    'pcg.nombre_profesor as nombre_profesor_co_guia',
                    'pcg.apellido_profesor as apellido_profesor_co_guia',
                    'pc.nombre_profesor as nombre_profesor_comision',
                    'pc.apellido_profesor as apellido_profesor_comision',
                    DB::raw("NULL as empresa"),
                    DB::raw("NULL as supervisor"),
                    DB::raw("'proyecto' as tipo_registro")
                ])
                ->join('proyecto as p', 'h.id_habilitacion', '=', 'p.id_habilitacion')
                ->join('alumno as a', 'h.rut_alumno', '=', 'a.rut_alumno')
                ->leftJoin('profesor as pg', 'p.rut_profesor_guia', '=', 'pg.rut_profesor')
                ->leftJoin('profesor as pcg', 'p.rut_profesor_co_guia', '=', 'pcg.rut_profesor')
                ->leftJoin('profesor as pc', 'p.rut_profesor_comision', '=', 'pc.rut_profesor')
                ->where('h.semestre_inicio', $semestre);

            // Subconsulta para prácticas
            $practicas = DB::table('habilitacion as h')
                ->select([
                    'h.id_habilitacion',
                    'h.rut_alumno',
                    'h.nota_final',
                    'h.fecha_nota',
                    'h.semestre_inicio',
                    'h.descripcion',
                    'h.titulo',
                    'a.nombre_alumno',
                    'a.apellido_alumno',
                    DB::raw("'PrTut' as tipo_habilitacion"),
                    'prt.rut_profesor_tutor as rut_profesor_tutor',
                    'ptut.nombre_profesor as nombre_profesor_guia',
                    'ptut.apellido_profesor as apellido_profesor_guia',
                    DB::raw("NULL as nombre_profesor_co_guia"),
                    DB::raw("NULL as apellido_profesor_co_guia"),
                    DB::raw("NULL as nombre_profesor_comision"),
                    DB::raw("NULL as apellido_profesor_comision"),
                    'prt.nombre_empresa as empresa',
                    'prt.nombre_supervisor as supervisor',
                    DB::raw("'practica' as tipo_registro")
                ])
                ->join('pr_tut as prt', 'h.id_habilitacion', '=', 'prt.id_habilitacion')
                ->join('alumno as a', 'h.rut_alumno', '=', 'a.rut_alumno')
                ->leftJoin('profesor as ptut', 'prt.rut_profesor_tutor', '=', 'ptut.rut_profesor')
                ->where('h.semestre_inicio', $semestre);

            // Ejecutar ambas consultas y combinarlas en colección para ordenar y paginar
            $proyectosCollection = $proyectos->get();
            $practicasCollection = $practicas->get();

            $merged = $proyectosCollection->merge($practicasCollection);

            // 4.4.1.6 Ordenar por tipo_habilitacion (para agrupar) y por apellido_alumno asc
            $sorted = $merged->sortBy(function ($item) {
                $tipo = $item->tipo_habilitacion ?? '';
                $apellido = $item->apellido_alumno ?? '';
                return $tipo . '|' . strtolower($apellido);
            })->values();

            // Paginación manual
            $page = (int) ($request->get('page', 1));
            $perPage = 5;
            $total = $sorted->count();
            $currentItems = $sorted->forPage($page, $perPage)->values();

            $paginator = new LengthAwarePaginator($currentItems, $total, $perPage, $page, [
                'path' => request()->url()
            ]);

            // Agregar los parámetros del query a la paginación
            $paginator->appends($query_params);

            \Log::info('Cantidad de habilitaciones encontradas: ' . $paginator->total());

            if ($paginator->isEmpty()) {
                return back()->with('error', 'No hay Habilitaciones Profesionales para este semestre');
            }

            return view('listados', [
                'habilitaciones' => $paginator,
                'tipo_listado' => 'Listado Semestral',
                'semestres_disponibles' => Habilitacion::distinct()
                    ->orderBy('semestre_inicio', 'desc')
                    ->pluck('semestre_inicio')
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el listado semestral: ' . $e->getMessage());
        }
    }
    // 4.4.2 Si tipo_listado toma el valor de “Listado Histórico”, el sistema debe generar el listado histórico de todos los profesores DINF.
    private function generarListadoHistorico(Request $request)
    {
        try {
            \Log::info('Generando listado histórico');
            
            $query_params = $request->only(['tipo_listado']);
            
            $profesores_base = DB::table('profesor')
                ->where('departamento', 'DINF')
                ->orderBy('apellido_profesor')
                ->get();

            \Log::info('Cantidad de profesores DINF encontrados: ' . $profesores_base->count());

            $profesores_dinf = collect();

            foreach ($profesores_base as $profesor) {
                $semestres = [];

                // Obtener habilitaciones donde el profesor participa en proyectos
                $proyectos = DB::table('habilitacion')
                    ->join('proyecto', 'habilitacion.id_habilitacion', '=', 'proyecto.id_habilitacion')
                    ->join('alumno', 'habilitacion.rut_alumno', '=', 'alumno.rut_alumno')
                    ->where(function ($query) use ($profesor) {
                        $query->where('proyecto.rut_profesor_guia', $profesor->rut_profesor)
                            ->orWhere('proyecto.rut_profesor_co_guia', $profesor->rut_profesor)
                            ->orWhere('proyecto.rut_profesor_comision', $profesor->rut_profesor);
                    })
                    ->select('habilitacion.*', 'alumno.*', 'proyecto.*')
                    ->orderBy('habilitacion.semestre_inicio', 'desc')
                    ->get();

                // Obtener habilitaciones donde el profesor es tutor
                $practicas = DB::table('habilitacion')
                    ->join('pr_tut', 'habilitacion.id_habilitacion', '=', 'pr_tut.id_habilitacion')
                    ->join('alumno', 'habilitacion.rut_alumno', '=', 'alumno.rut_alumno')
                    ->where('pr_tut.rut_profesor_tutor', $profesor->rut_profesor)
                    ->select('habilitacion.*', 'alumno.*', 'pr_tut.*')
                    ->orderBy('habilitacion.semestre_inicio', 'desc')
                    ->get();

                // Procesar proyectos
                foreach ($proyectos as $proyecto) {
                    $semestre = $proyecto->semestre_inicio;
                    
                    if (!isset($semestres[$semestre])) {
                        $semestres[$semestre] = [];
                    }

                    $alumno_info = [
                        'nombre' => $proyecto->nombre_alumno,
                        'apellido' => $proyecto->apellido_alumno,
                        'rut' => $proyecto->rut_alumno
                    ];

                    if ($proyecto->rut_profesor_guia === $profesor->rut_profesor) {
                        if (!isset($semestres[$semestre]['Guía'])) {
                            $semestres[$semestre]['Guía'] = [];
                        }
                        $semestres[$semestre]['Guía'][] = $alumno_info;
                    }
                    if ($proyecto->rut_profesor_co_guia === $profesor->rut_profesor) {
                        if (!isset($semestres[$semestre]['Co-Guía'])) {
                            $semestres[$semestre]['Co-Guía'] = [];
                        }
                        $semestres[$semestre]['Co-Guía'][] = $alumno_info;
                    }
                    if ($proyecto->rut_profesor_comision === $profesor->rut_profesor) {
                        if (!isset($semestres[$semestre]['Comisión'])) {
                            $semestres[$semestre]['Comisión'] = [];
                        }
                        $semestres[$semestre]['Comisión'][] = $alumno_info;
                    }
                }

                // Procesar prácticas
                foreach ($practicas as $practica) {
                    $semestre = $practica->semestre_inicio;
                    
                    if (!isset($semestres[$semestre])) {
                        $semestres[$semestre] = [];
                    }

                    $alumno_info = [
                        'nombre' => $practica->nombre_alumno,
                        'apellido' => $practica->apellido_alumno,
                        'rut' => $practica->rut_alumno
                    ];

                    if (!isset($semestres[$semestre]['Tutor'])) {
                        $semestres[$semestre]['Tutor'] = [];
                    }
                    $semestres[$semestre]['Tutor'][] = $alumno_info;
                }

                // Ordenar semestres en orden descendente (AAAA-S) antes de agregar al resultado
                if (!empty($semestres)) {
                    // Ordenar por clave (semestre) descendente
                    krsort($semestres);
                }

                $profesor_info = [
                    'nombre_profesor' => $profesor->nombre_profesor,
                    'apellido_profesor' => $profesor->apellido_profesor,
                    'rut_profesor' => $profesor->rut_profesor,
                    'semestres' => $semestres
                ];

                $profesores_dinf->push($profesor_info);
            }
            //4.4.2.1.1  Si no existen profesores, el sistema deberá desplegar un mensaje “No hay datos de profesores del DINF en el sistema”.
            if ($profesores_dinf->isEmpty()) {
                return back()->with('error', 'No hay datos de profesores del DINF en el sistema');
            }

            $profesores_paginados = new LengthAwarePaginator(
                $profesores_dinf->forPage(request()->get('page', 1), 5),
                $profesores_dinf->count(),
                5,
                request()->get('page', 1),
                ['path' => request()->url()]
            );

            // Agregar los parámetros del query a la paginación
            $profesores_paginados->appends($query_params);

            return view('listados', [
                'profesores_dinf' => $profesores_paginados,
                'tipo_listado' => 'Listado Histórico',
                'semestres_disponibles' => Habilitacion::distinct()
                    ->orderBy('semestre_inicio', 'desc')
                    ->pluck('semestre_inicio')
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el listado histórico: ' . $e->getMessage());
        }
    }
}
