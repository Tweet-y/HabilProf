<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ingreso de Habilitación Profesional
        </h2>
    </x-slot>

    <x-slot name="header_styles">
        <link rel="stylesheet" href="{{ asset('css/form.css') }}">
        <style>
            /* Añade un margen para que el contenedor no choque con la barra de nav */
            .container { margin-top: 20px; }
        </style>
    </x-slot>

    <div class="container">

        <!-- Mensajes de éxito -->
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- Mensajes de error -->
        @if($errors->any())
            <div class="error-message">
                <strong>Complete todos los campos destacados(*) o los campos no están bien escritos(*).</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Mensajes de error de sesión -->
        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        <!-- Div para errores de validación JavaScript -->
        <div id="js-validation-error" class="error-message" style="display: none; margin-bottom: 20px;"></div>

        <form action="{{ route('habilitaciones.store') }}" method="POST" onsubmit="return validarFormulario() && confirm('¿Está seguro de que desea registrar esta habilitación?');">
            @csrf

            <fieldset>
                <legend>Datos Principales</legend>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="selector_alumno_rut" class="required">Seleccionar Alumno Habilitado</label>
                        <select id="selector_alumno_rut" name="selector_alumno_rut" required
                                class="{{ $errors->has('selector_alumno_rut') ? 'field-error' : '' }}"
                                {{ $alumnos->count() == 0 ? 'disabled' : '' }}>
                            @if($alumnos->count() > 0)
                                <option value="" disabled selected>Buscar y seleccionar un alumno habilitado...</option>
                                @foreach($alumnos as $alumno)
                                    <option value="{{ $alumno->rut_alumno }}"
                                        {{ old('selector_alumno_rut') == $alumno->rut_alumno ? 'selected' : '' }}>
                                        {{ $alumno->apellido_alumno }}, {{ $alumno->nombre_alumno }} ({{ $alumno->rut_alumno }})
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled selected>No hay alumnos disponibles</option>
                            @endif
                        </select>
                        @if($errors->has('selector_alumno_rut'))
                            <div class="error-text">{{ $errors->first('selector_alumno_rut') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="tipo_habilitacion" class="required">Tipo de Habilitación</label>
                        <select id="tipo_habilitacion" name="tipo_habilitacion" required
                                class="{{ $errors->has('tipo_habilitacion') ? 'field-error' : '' }}">
                            <option value="" disabled selected>Seleccione un tipo...</option>
                            <option value="PrIng" {{ old('tipo_habilitacion') == 'PrIng' ? 'selected' : '' }}>PrIng (Proyecto de Ingeniería)</option>
                            <option value="PrInv" {{ old('tipo_habilitacion') == 'PrInv' ? 'selected' : '' }}>PrInv (Proyecto de Innovación)</option>
                            <option value="PrTut" {{ old('tipo_habilitacion') == 'PrTut' ? 'selected' : '' }}>PrTut (Práctica Tutelada)</option>
                        </select>
                        @if($errors->has('tipo_habilitacion'))
                            <div class="error-text">{{ $errors->first('tipo_habilitacion') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="semestre_inicio" class="required">Semestre de Inicio</label>
                        <select id="semestre_inicio" name="semestre_inicio" required
                                class="{{ $errors->has('semestre_inicio') ? 'field-error' : '' }}">
                            <option value="" disabled selected>Seleccione semestre...</option>
                            @foreach($semestres as $semestre)
                                <option value="{{ $semestre }}" {{ old('semestre_inicio') == $semestre ? 'selected' : '' }}>{{ $semestre }}</option>
                            @endforeach
                        </select>
                        <small class="help-text">Solo se muestran semestres futuros.</small>
                        @if($errors->has('semestre_inicio'))
                            <div class="error-text">{{ $errors->first('semestre_inicio') }}</div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="nota_final">Nota Final</label>
                        <input type="number" id="nota_final" name="nota_final"
                               min="1.0" max="7.0" step="0.1"
                               placeholder="Se actualizará desde R1" readonly
                               value="{{ old('nota_final') }}">
                        <small class="help-text">Este campo no se puede modificar.</small>
                    </div>

                </div>
            </fieldset>

            <fieldset>
                <legend>Descripción del Trabajo</legend>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="titulo" class="required">Título</label>
                        <input type="text" id="titulo" name="titulo" required 
                               minlength="6" maxlength="80"
                               pattern="[a-zA-Z0-9\s.,;:''&quot;&quot;-_()]+" 
                               title="Solo alfanumérico y algunos símbolos."
                               value="{{ old('titulo') }}"
                               class="{{ $errors->has('titulo') ? 'field-error' : '' }}">
                        <small class="help-text">Entre 6 y 80 caracteres. Símbolos permitidos: . , ; : ' " - _ ( )</small>
                        @if($errors->has('titulo'))
                            <div class="error-text">{{ $errors->first('titulo') }}</div>
                        @endif
                    </div>
                    <div class="form-group form-group-full">
                        <label for="descripcion" class="required">Descripción</label>
                        <textarea id="descripcion" name="descripcion" required 
                                  minlength="30" maxlength="500"
                                  class="{{ $errors->has('descripcion') ? 'field-error' : '' }}">{{ old('descripcion') }}</textarea>
                        <small class="help-text">Entre 30 y 500 caracteres. Símbolos permitidos: . , ; : ' " - _ ( )</small>
                        @if($errors->has('descripcion'))
                            <div class="error-text">{{ $errors->first('descripcion') }}</div>
                        @endif
                    </div>
                </div>
            </fieldset>

            <div id="seccion-pring-prinv" class="seccion-condicional" style="display: none;">
                <fieldset>
                    <legend>Equipo Docente (PrIng / PrInv)</legend>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="seleccion_guia_rut" class="required">Profesor Guía (DINF)</label>
                            <select id="seleccion_guia_rut" name="seleccion_guia_rut"
                                    class="{{ $errors->has('seleccion_guia_rut') ? 'field-error' : '' }}">
                                <option value="" disabled selected>Seleccione un guía...</option>
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->rut_profesor }}"
                                        {{ old('seleccion_guia_rut') == $profesor->rut_profesor ? 'selected' : '' }}>
                                        {{ $profesor->nombre_profesor }} {{ $profesor->apellido_profesor }} ({{ $profesor->rut_profesor }})
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('seleccion_guia_rut'))
                                <div class="error-text">{{ $errors->first('seleccion_guia_rut') }}</div>
                            @endif
                        </div>
                        
                        <div class="form-group">
                            <label for="seleccion_co_guia_rut">Profesor Co-Guía (UCSC)</label>
                            <select id="seleccion_co_guia_rut" name="seleccion_co_guia_rut"
                                    class="{{ $errors->has('seleccion_co_guia_rut') ? 'field-error' : '' }}">
                                <option value="" selected>Ninguno (Opcional)</option>
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->rut_profesor }}"
                                        {{ old('seleccion_co_guia_rut') == $profesor->rut_profesor ? 'selected' : '' }}>
                                        {{ $profesor->nombre_profesor }} {{ $profesor->apellido_profesor }} ({{ $profesor->rut_profesor }})
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('seleccion_co_guia_rut'))
                                <div class="error-text">{{ $errors->first('seleccion_co_guia_rut') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="seleccion_comision_rut" class="required">Profesor Comisión (DINF)</label>
                            <select id="seleccion_comision_rut" name="seleccion_comision_rut"
                                    class="{{ $errors->has('seleccion_comision_rut') ? 'field-error' : '' }}">
                                <option value="" disabled selected>Seleccione comisión...</option>
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->rut_profesor }}"
                                        {{ old('seleccion_comision_rut') == $profesor->rut_profesor ? 'selected' : '' }}>
                                        {{ $profesor->nombre_profesor }} {{ $profesor->apellido_profesor }} ({{ $profesor->rut_profesor }})
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('seleccion_comision_rut'))
                                <div class="error-text">{{ $errors->first('seleccion_comision_rut') }}</div>
                            @endif
                        </div>
                    </div>
                </fieldset>
            </div>

            <div id="seccion-prtut" class="seccion-condicional" style="display: none;">
                <fieldset>
                    <legend>Datos Práctica Tutelada (PrTut)</legend>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre_empresa" class="required">Nombre Empresa</label>
                            <input type="text" id="nombre_empresa" name="nombre_empresa" 
                                   maxlength="50" pattern="[a-zA-Z0-9\s]+" 
                                   value="{{ old('nombre_empresa') }}"
                                   class="{{ $errors->has('nombre_empresa') ? 'field-error' : '' }}">
                            <small class="help-text">Alfanumérico, máx 50 caracteres.</small>
                            @if($errors->has('nombre_empresa'))
                                <div class="error-text">{{ $errors->first('nombre_empresa') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="nombre_supervisor" class="required">Nombre Supervisor (Empresa)</label>
                            <input type="text" id="nombre_supervisor" name="nombre_supervisor" 
                                   maxlength="50" pattern="[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+"
                                   value="{{ old('nombre_supervisor') }}"
                                   class="{{ $errors->has('nombre_supervisor') ? 'field-error' : '' }}">
                            <small class="help-text">Alfabético, máx 50 caracteres.</small>
                            @if($errors->has('nombre_supervisor'))
                                <div class="error-text">{{ $errors->first('nombre_supervisor') }}</div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="seleccion_tutor_rut" class="required">Profesor Tutor (DINF)</label>
                            <select id="seleccion_tutor_rut" name="seleccion_tutor_rut"
                                    class="{{ $errors->has('seleccion_tutor_rut') ? 'field-error' : '' }}">
                                <option value="" disabled selected>Seleccione un tutor...</option>
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->rut_profesor }}"
                                        {{ old('seleccion_tutor_rut') == $profesor->rut_profesor ? 'selected' : '' }}>
                                        {{ $profesor->nombre_profesor }} {{ $profesor->apellido_profesor }} ({{ $profesor->rut_profesor }})
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('seleccion_tutor_rut'))
                                <div class="error-text">{{ $errors->first('seleccion_tutor_rut') }}</div>
                            @endif
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="button-container">
                @if($alumnos->count() == 0)
                    <button type="button" onclick="window.location.href='/dashboard'">Volver al Menú</button>
                @endif
                <div class="right-buttons">
                    <button type="button" onclick="window.location.href='/dashboard'">Cancelar</button>
                    <button type="submit">Confirmar Ingreso</button>
                </div>
            </div>

        </form>
    </div>

    <script src="{{ asset('js/validacion.js') }}"></script>
    <script src="{{ asset('js/formHabilitacion.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoHabilitacion = document.getElementById('tipo_habilitacion');
            const seccionProyecto = document.getElementById('seccion-pring-prinv');
            const seccionPractica = document.getElementById('seccion-prtut');

            function toggleSections() {
                const valor = tipoHabilitacion.value;

                // Ocultar ambas secciones primero
                seccionProyecto.style.display = 'none';
                seccionPractica.style.display = 'none';

                // Quitar requerido de todos los campos condicionales
                document.querySelectorAll('#seccion-pring-prinv [required]').forEach(el => {
                    el.required = false;
                });
                document.querySelectorAll('#seccion-prtut [required]').forEach(el => {
                    el.required = false;
                });

                // Mostrar y hacer requeridos según el tipo
                if (valor === 'PrTut') {
                    seccionPractica.style.display = 'block';
                    document.querySelectorAll('#seccion-prtut [required]').forEach(el => {
                        el.required = true;
                    });
                } else if (valor === 'PrIng' || valor === 'PrInv') {
                    seccionProyecto.style.display = 'block';
                    document.querySelectorAll('#seccion-pring-prinv [required]').forEach(el => {
                        el.required = true;
                    });
                }
            }

            // Ejecutar al cambiar y al cargar la página
            tipoHabilitacion.addEventListener('change', toggleSections);

            // Ejecutar al cargar para mostrar sección según valor antiguo (si hay error)
            const oldValue = "{{ old('tipo_habilitacion') }}";
            if (oldValue) {
                toggleSections();
            }
        });
    </script>

</x-app-layout>
