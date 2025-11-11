<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Listado de Habilitaciones
        </h2>
    </x-slot>

    <x-slot name="header_styles">
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #F8F9FA; /* Gris Claro (Fondo) */
                color: #222222;
            }
            .container {
                /* Mantenido en 1100px como en la corrección anterior */
                max-width: 1800px;
                margin: 20px auto;
                background-color: #FFFFFF; /* Blanco (Contenedor) */
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                border: 1px solid #CED4DA; /* Gris (Borde) */
                overflow: hidden;
            }
            header {
                padding: 20px 30px;
                background-color: #FFFFFF;
                border-bottom: 1px solid #CED4DA;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            header h1 {
                margin: 0;
                color: #222222; /* Gris Oscuro (Texto) */
                font-size: 1.8em;
            }
            header img {
                max-height: 50px;
                width: auto;
                margin-left: 20px;
            }
            form, .seccion-tabla {
                padding: 30px;
            }
            fieldset {
                border: 1px solid #CED4DA; /* Gris (Borde) */
                border-radius: 6px;
                padding: 20px;
                margin-bottom: 25px;
                background-color: #F8F9FA; /* Gris Claro (Fondo Fieldset) */
            }
            legend {
                font-size: 1.2em;
                font-weight: 600;
                padding: 0 10px;
                color: #E60026; /* Rojo Primario (UCSC) */
            }
            .form-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                align-items: flex-end;
            }
            .form-group {
                display: flex;
                flex-direction: column;
            }
            label {
                font-weight: 600;
                margin-bottom: 6px;
                font-size: 0.9em;
                color: #333333;
            }
            label.required::after {
                content: ' *';
                color: #E60026; /* Rojo Primario (UCSC) */
                font-weight: bold;
            }
            select {
                width: 100%;
                padding: 10px;
                border: 1px solid #CED4DA;
                border-radius: 4px;
                box-sizing: border-box;
                font-size: 1em;
                background-color: #fff;
            }

            /* --- ESTILOS PARA LA TABLA --- */

            .tabla-container {
                width: 100%;
                /* Allow both horizontal and vertical scrolling when table is larger than container */
                overflow-x: auto;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                /* Limit vertical height but keep horizontal scroll available */
                max-height: 450px;
                border: 1px solid #CED4DA;
                border-radius: 4px;
            }
            .tabla-wrapper {
                position: relative;
            }

            /* Horizontal scrollbar container placed below the table; shows only scrollbar */
            .h-scroll {
                overflow-x: auto;
                overflow-y: hidden;
                height: 18px; /* small height to show only the track */
                margin-top: 6px;
            }
            .h-scroll-inner {
                height: 1px; /* just needs width to create the scroll track */
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.9em;
                table-layout: auto; /* allow natural column widths */
            }
            table th, table td {
                border-bottom: 1px solid #CED4DA; /* Borde solo horizontal */
                padding: 12px 14px;
                text-align: left;
                vertical-align: top;
            }
            table thead {
                background-color: #0056A8; /* Azul Secundario (UCSC) */
                position: sticky; /* Cabecera fija */
                top: 0;
                z-index: 1;
            }
            table thead th {
                color: #FFFFFF; /* Texto Blanco */
                font-weight: 600;
                border-bottom: 0;
            }
            table tbody tr:nth-child(even) {
                background-color: #F8F9FA; /* Gris Claro (Fila Par) */
            }
            table tbody tr:hover {
                background-color: #E9ECEF; /* Gris Hover */
            }

            .button-container {
                text-align: left;
            }
            button {
                padding: 10px 18px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 1em;
                font-weight: 600;
                transition: background-color 0.2s ease;
                background-color: #E60026; /* Rojo Primario (UCSC) */
                color: white;
            }
            button:hover {
                background-color: #C00020;
            }
        </style>
    </x-slot>

    <div class="container">
        <header>
            <h1>Generar Listado de Habilitaciones</h1>
            <img src="{{ asset('imagenes/ucsc.png') }}" alt="Logo UCSC">
        </header>

        <form action="{{ route('listados.generar') }}" method="POST">
            @csrf
            @method('POST')
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <fieldset>
                <legend>Generar Reporte</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tipo_listado" class="required">Tipo de Listado</label>
                        <select id="tipo_listado" name="tipo_listado" required onchange="mostrarFiltroSemestre(this.value)">
                            <option value="" disabled {{ !$tipo_listado ? 'selected' : '' }}>Seleccione un tipo...</option>
                            <option value="Listado Semestral" {{ $tipo_listado == 'Listado Semestral' ? 'selected' : '' }}>Listado Semestral</option>
                            <option value="Listado Histórico" {{ $tipo_listado == 'Listado Histórico' ? 'selected' : '' }}>Listado Histórico</option>
                        </select>
                    </div>

                    <div class="form-group" id="filtro_semestre_container" style="display: none;">
                        <label for="semestre" class="required">Filtrar por semestre</label>
                        <select id="semestre" name="semestre">
                            @foreach($semestres_disponibles as $semestre)
                                <option value="{{ $semestre }}">{{ $semestre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group button-container">
                        <button type="submit">Generar Listado</button>
                    </div>
                </div>
            </fieldset>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inicializar el estado del filtro de semestre si hay un valor guardado
                const tipoListado = document.getElementById('tipo_listado').value;
                if (tipoListado) {
                    mostrarFiltroSemestre(tipoListado);
                }

                // Validar el formulario antes de enviar
                document.querySelector('form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const tipo = document.getElementById('tipo_listado').value;
                    const semestre = document.getElementById('semestre');

                    if (!tipo) {
                        alert('Por favor seleccione un tipo de listado');
                        return false;
                    }

                    if (tipo === 'Listado Semestral' && !semestre.value) {
                        alert('Por favor seleccione un semestre');
                        return false;
                    }

                    this.submit();
                });
            });

            function mostrarFiltroSemestre(tipo) {
                const filtroSemestre = document.getElementById('filtro_semestre_container');
                filtroSemestre.style.display = tipo === 'Listado Semestral' ? 'block' : 'none';
                
                const semestreSelect = document.getElementById('semestre');
                semestreSelect.required = tipo === 'Listado Semestral';
            }
        </script>

        <script>
            // Sincronizar la barra de scroll horizontal independiente con la tabla
            document.addEventListener('DOMContentLoaded', function() {
                const tablaContainer = document.getElementById('tabla-container');
                const tabla = document.getElementById('tabla-resultados');
                const hScroll = document.getElementById('h-scroll');
                const hScrollInner = document.getElementById('h-scroll-inner');

                if (!tablaContainer || !tabla || !hScroll || !hScrollInner) return;

                function refreshHScrollWidth() {
                    // Set inner width to table scroll width so the horizontal scrollbar appears
                    const scrollWidth = tabla.scrollWidth;
                    hScrollInner.style.width = scrollWidth + 'px';
                }

                // Sync scrolling both ways
                tablaContainer.addEventListener('scroll', function() {
                    hScroll.scrollLeft = tablaContainer.scrollLeft;
                });
                hScroll.addEventListener('scroll', function() {
                    tablaContainer.scrollLeft = hScroll.scrollLeft;
                });

                // Initial setup and resize observer to update widths when table changes
                refreshHScrollWidth();

                // Update when window resizes
                window.addEventListener('resize', function() {
                    refreshHScrollWidth();
                });

                // Use MutationObserver to detect table content/size changes
                const mo = new MutationObserver(function() {
                    refreshHScrollWidth();
                });
                mo.observe(tabla, { childList: true, subtree: true, characterData: true });
            });
        </script>

        <div class="seccion-tabla">
            <fieldset>
                <legend>Resultados del Listado</legend>
                
                @if($tipo_listado === null)
                    <div class="alert alert-info">
                        Seleccione un tipo de listado para comenzar
                    </div>
                @elseif($tipo_listado === 'Listado Semestral' && isset($habilitaciones) && $habilitaciones->total() > 0)
                    <div class="tabla-wrapper">
                        <div class="tabla-container" id="tabla-container">
                            <table id="tabla-resultados">
                            <thead>
                                <tr>
                                    <th>Semestre</th>
                                    <th>RUT Alumno</th>
                                    <th>Nombre y Apellido</th>
                                    <th>Tipo</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Nota Final</th>
                                    <th>Fecha Nota</th>
                                    <th>Profesor Principal</th>
                                    <th>RUT Guía</th>
                                    <th>RUT Co-Guía</th>
                                    <th>RUT Comisión</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($habilitaciones as $hab)
                                <tr>
                                    <td>{{ $hab->semestre_inicio }}</td>
                                    <td>{{ $hab->rut_alumno }}</td>
                                    <td>{{ $hab->nombre_alumno }} {{ $hab->apellido_alumno }}</td>
                                    <td>
                                        @php
                                            $tipo = $hab->tipo_habilitacion ?? '';
                                            if ($tipo === 'PrIng') $tipo = 'Pring';
                                            if ($tipo === 'PrInv') $tipo = 'Prinv';
                                        @endphp
                                        {{ $tipo }}
                                    </td>
                                    <td>{{ $hab->titulo }}</td>
                                    <td>{{ Str::limit($hab->descripcion, 100) }}</td>
                                    <td>{{ $hab->nota_final ?? 'Pendiente' }}</td>
                                    <td>{{ $hab->fecha_nota ? date('d/m/Y', strtotime($hab->fecha_nota)) : 'Pendiente' }}</td>
                                    
                                    @if($hab->tipo_registro === 'proyecto')
                                        <td>{{ $hab->nombre_profesor_guia }} {{ $hab->apellido_profesor_guia }}</td>
                                        <td>{{ $hab->rut_profesor_guia ?? 'N/A' }}</td>
                                        <td>{{ $hab->rut_profesor_co_guia ?? 'N/A' }}</td>
                                        <td>{{ $hab->rut_profesor_comision ?? 'N/A' }}</td>
                                        <td>
                                            <strong>Co-Guía:</strong> {{ $hab->nombre_profesor_co_guia ?? 'No asignado' }} {{ $hab->apellido_profesor_co_guia ?? '' }}<br>
                                            <strong>Comisión:</strong> {{ $hab->nombre_profesor_comision ?? 'No asignado' }} {{ $hab->apellido_profesor_comision ?? '' }}
                                        </td>
                                    @else
                                        <td>{{ $hab->nombre_profesor_guia }} {{ $hab->apellido_profesor_guia }}</td>
                                        <td>{{ $hab->rut_profesor_tutor ?? 'N/A' }}</td>
                                        <td colspan="2"></td>
                                        <td>
                                            <strong>Empresa:</strong> {{ $hab->empresa ?? 'No especificada' }}<br>
                                            <strong>Supervisor:</strong> {{ $hab->supervisor ?? 'No especificado' }}
                                        </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>

                        <!-- Barra de scroll horizontal independiente -->
                        <div class="h-scroll" id="h-scroll" aria-hidden="true">
                            <div class="h-scroll-inner" id="h-scroll-inner"></div>
                        </div>
                    </div>
                    
                    <div class="pagination mt-4">
                        {{ $habilitaciones->links() }}
                    </div>

                @elseif($tipo_listado === 'Listado Histórico' && isset($profesores_dinf) && $profesores_dinf->count() > 0)
                    <div class="tabla-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Profesor</th>
                                    <th>RUT Profesor</th>
                                    <th>Semestre</th>
                                    <th>Rol</th>
                                    <th>Alumnos Asistidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profesores_dinf as $profesor)
                                    @if(!empty($profesor['semestres']))
                                        @foreach($profesor['semestres'] as $semestre => $roles)
                                            <tr>
                                                <td>{{ $profesor['nombre_profesor'] }} {{ $profesor['apellido_profesor'] }}</td>
                                                <td>{{ $profesor['rut_profesor'] }}</td>
                                                <td>{{ $semestre }}</td>
                                                <td>
                                                    @foreach($roles as $rol => $alumnos)
                                                        <strong>{{ $rol }}:</strong><br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach($roles as $rol => $alumnos)
                                                        @foreach($alumnos as $alumno)
                                                            {{ $alumno['nombre'] }} {{ $alumno['apellido'] }} ({{ $alumno['rut'] }})<br>
                                                        @endforeach
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>{{ $profesor['nombre_profesor'] }} {{ $profesor['apellido_profesor'] }}</td>
                                            <td>{{ $profesor['rut_profesor'] }}</td>
                                            <td colspan="3">No existen habilitaciones para este profesor</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination mt-4">
                        {{ $profesores_dinf->links() }}
                    </div>

                @elseif($tipo_listado)
                    <div class="alert alert-info">
                        @if($tipo_listado === 'Listado Semestral')
                            No hay Habilitaciones Profesionales para este semestre
                        @elseif($tipo_listado === 'Listado Histórico')
                            No hay datos de profesores del DINF en el sistema
                        @endif
                    </div>
                @endif

                <style>
                    .alert {
                        padding: 15px;
                        margin-bottom: 20px;
                        border: 1px solid transparent;
                        border-radius: 4px;
                    }
                    .alert-info {
                        color: #0c5460;
                        background-color: #d1ecf1;
                        border-color: #bee5eb;
                    }
                </style>

            </fieldset>
        </div>

    </div>

</x-app-layout>
