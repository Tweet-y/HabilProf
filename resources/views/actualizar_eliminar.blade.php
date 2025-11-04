<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar o Eliminar Habilitación</title>
    <link rel="stylesheet" href="{{ asset('css/form.css') }}">
</head>
<body>

    <div class="container">
        <header>
            <h1>Actualizar o Eliminar Habilitación</h1>
            <img src="imagenes/ucsc.png" alt="Logo UCSC">
        </header>

        <form action="{{ route('habilitaciones.index') }}" method="GET" class="seccion-accion">
            <fieldset>
                <legend>Buscar Habilitación Existente</legend>

                <div class="form-group form-group-full">
                    <label for="buscar_alumno" class="required">Seleccionar Alumno</label>
                    <select id="buscar_alumno" name="rut_alumno" required>
                        <option value="" disabled>Buscar y seleccionar un alumno...</option>
                        @if(isset($alumnos) && count($alumnos) > 0)
                            @foreach($alumnos as $alumno)
                                <option value="{{ $alumno->rut_alumno }}" {{ (request('rut_alumno') == $alumno->rut_alumno) ? 'selected' : '' }}>
                                    {{ $alumno->apellido_alumno }}, {{ $alumno->nombre_alumno }} ({{ $alumno->rut_alumno }})
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No hay alumnos disponibles</option>
                        @endif
                    </select>
                </div>

                <div class="button-container">
                    <button type="submit" class="btn-primary">Buscar Habilitación</button>
                </div>
            </fieldset>
        </form>

        @if($habilitacion)
        @php
            $alumnoNombre = $habilitacion->alumno->apellido_alumno . ', ' . $habilitacion->alumno->nombre_alumno;
        @endphp
        <div class="seccion-accion" id="seleccion-accion">
            <hr style="border: 0; border-top: 1px dashed #CED4DA; margin: 0 30px 30px;">
            <h2>Alumno Seleccionado: <strong>{{ $alumnoNombre }}</strong></h2>
            <p>¿Desea eliminar o modificar los datos de esta habilitación?</p>

            <div class="button-container">
                <button type="button" class="btn-primary" onclick="mostrarModificar()">Modificar Datos</button>
                <button type="button" class="btn-danger" onclick="mostrarEliminar('{{ $alumnoNombre }}')">Eliminar Habilitación</button>
            </div>
        </div>
        @endif

        
        <div class="error-message" id="form-error" style="display: none;"></div>

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

        @if($habilitacion)
        <form action="#" method="POST" onsubmit="return false;" id="form-modificar">
            @csrf
            @method('PUT')
            <fieldset>
                <legend>Datos Principales (Editando)</legend>
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label for="tipo_habilitacion" class="required">Tipo de Habilitación</label>
                        <select id="tipo_habilitacion" name="tipo_habilitacion" required>
                            <option value="PrIng" {{ (old('tipo_habilitacion', $habilitacion->proyecto->tipo_proyecto ?? 'PrIng') == 'PrIng') ? 'selected' : '' }}>PrIng (Proyecto de Ingeniería)</option>
                            <option value="PrInv" {{ (old('tipo_habilitacion', $habilitacion->proyecto->tipo_proyecto ?? 'PrIng') == 'PrInv') ? 'selected' : '' }}>PrInv (Proyecto de Innovación)</option>
                            <option value="PrTut" {{ (old('tipo_habilitacion', $habilitacion->proyecto ? '' : 'PrTut') == 'PrTut') ? 'selected' : '' }}>PrTut (Práctica Tutelada)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="semestre_inicio" class="required">Semestre de Inicio</label>
                        <select id="semestre_inicio" name="semestre_inicio" required>
                            <option value="2025-1" {{ (old('semestre_inicio', $habilitacion->semestre_inicio ?? '2025-1') == '2025-1') ? 'selected' : '' }}>2025-1</option>
                            <option value="2025-2" {{ (old('semestre_inicio', $habilitacion->semestre_inicio ?? '2025-1') == '2025-2') ? 'selected' : '' }}>2025-2</option>
                            <option value="2026-1" {{ (old('semestre_inicio', $habilitacion->semestre_inicio ?? '2025-1') == '2026-1') ? 'selected' : '' }}>2026-1</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nota_final">Nota Final</label>
                        <input type="number" id="nota_final" name="nota_final"
                               min="1.0" max="7.0" step="0.1" value="{{ old('nota_final', $habilitacion->nota_final ?? '') }}" readonly>
                        <small class="help-text">La nota final se actualiza automáticamente.</small>
                    </div>
                </div>
            </fieldset>



            <fieldset>
                <legend>Descripción del Trabajo (Editando)</legend>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="titulo" class="required">Título</label>
                        <input type="text" id="titulo" name="titulo"
                               minlength="6" maxlength="80"
                               pattern="[a-zA-Z0-9\s.,;:\'&-_()]+" title="Solo alfanumérico y algunos símbolos."
                               value="{{ old('titulo', $habilitacion->titulo ?? '') }}"
                               class="{{ $errors->has('titulo') ? 'field-error' : '' }}">
                        <small class="help-text">Entre 6 y 80 caracteres. Símbolos permitidos: . , ; : ' " - _ ( )</small>
                        @if($errors->has('titulo'))
                            <div class="error-text">{{ $errors->first('titulo') }}</div>
                        @endif
                    </div>
                    <div class="form-group form-group-full">
                        <label for="descripcion" class="required">Descripción</label>
                        <textarea id="descripcion" name="descripcion"
                                  minlength="30" maxlength="500"
                                  class="{{ $errors->has('descripcion') ? 'field-error' : '' }}">{{ old('descripcion', $habilitacion->descripcion ?? '') }}</textarea>
                        <small class="help-text">Entre 30 y 500 caracteres. Símbolos permitidos: . , ; : ' " - _ ( )</small>
                        @if($errors->has('descripcion'))
                            <div class="error-text">{{ $errors->first('descripcion') }}</div>
                        @endif
                    </div>
                </div>
            </fieldset>

            
            <div id="seccion-pring-prinv" class="seccion-condicional">
                <fieldset>
                    <legend>Equipo Docente (PrIng / PrInv)</legend>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="seleccion_guia" class="required">Profesor Guía (DINF)</label>
                            <select id="seleccion_guia" name="seleccion_guia_rut" required>
                                <option value="" disabled>Seleccione un profesor guía...</option>
                                @if(isset($profesores) && count($profesores) > 0)
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}" {{ (old('seleccion_guia_rut', $habilitacion->proyecto->rut_profesor_guia ?? '') == $profesor->rut_profesor) ? 'selected' : '' }}>
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="seleccion_co_guia">Profesor Co-Guía (UCSC) (Opcional)</label>
                            <select id="seleccion_co_guia" name="seleccion_co_guia_rut">
                                <option value="">Ninguno (Opcional)</option>
                                <option value="11223344" {{ (old('seleccion_co_guia_rut', $habilitacion->proyecto->rut_profesor_co_guia ?? '') == '11223344') ? 'selected' : '' }}>Profesor UCSC (No-DINF) (11223344)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="seleccion_comision" class="required">Profesor Comisión (DINF)</label>
                            <select id="seleccion_comision" name="seleccion_comision_rut" required>
                                <option value="" disabled>Seleccione un profesor comisión...</option>
                                @if(isset($profesores) && count($profesores) > 0)
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}" {{ (old('seleccion_comision_rut', $habilitacion->proyecto->rut_profesor_comision ?? '') == $profesor->rut_profesor) ? 'selected' : '' }}>
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>

            
            <div id="seccion-prtut" class="seccion-condicional">
                <fieldset>
                    <legend>Datos Práctica Tutelada (PrTut)</legend>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre_empresa" class="required">Nombre Empresa</label>
                            <input type="text" id="nombre_empresa" name="nombre_empresa"
                                   maxlength="50" pattern="[a-zA-Z0-9\s]+"
                                   value="{{ old('nombre_empresa', $habilitacion->prTut->nombre_empresa ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="nombre_supervisor" class="required">Nombre Supervisor (Empresa)</label>
                            <input type="text" id="nombre_supervisor" name="nombre_supervisor"
                                   maxlength="50" pattern="[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+"
                                   value="{{ old('nombre_supervisor', $habilitacion->prTut->nombre_supervisor ?? '') }}">
                        </div>

                        <div class="form-group">
                            <label for="seleccion_tutor" class="required">Profesor Tutor (DINF)</label>
                            <select id="seleccion_tutor" name="seleccion_tutor_rut" required>
                                <option value="" disabled>Seleccione un tutor...</option>
                                @if(isset($profesores) && count($profesores) > 0)
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}" {{ (old('seleccion_tutor_rut', $habilitacion->prTut->rut_profesor_tutor ?? '') == $profesor->rut_profesor) ? 'selected' : '' }}>
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="button-container">
                <button type="reset" class="btn-secondary" onclick="cancelarEdicion()">Cancelar Edición</button>
                <button type="button" class="btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='/dashboard'">Volver al Menú</button>
            </div>
        </form>
        @endif

        <div class="seccion-accion hidden" id="confirmar-eliminacion">
            <hr style="border: 0; border-top: 1px dashed #CED4DA; margin: 0 30px 30px;">
            <h2>Confirmar Eliminación</h2>
            <p>¿Desea eliminar la habilitación del Alumno <strong id="alumno-eliminar">Nombre Apellido</strong>?</p>
            <p class="help-text">Esta acción no se puede deshacer.</p>

            <div class="button-container">
                <button type="button" class="btn-secondary" onclick="cancelarEliminar()">Cancelar</button>
                <button type="button" class="btn-danger" onclick="confirmarEliminar()">Sí, eliminar</button>
            </div>
        </div>

    </div>

    <script src="{{ asset('js/validacion.js') }}"></script>
    <script src="{{ asset('js/formHabilitacion.js') }}"></script>
    <script>
        var hasHabilitacion = <?php echo $habilitacion ? 'true' : 'false'; ?>;



        // Función para mostrar sección de modificar
        function mostrarModificar() {
            document.getElementById('seleccion-accion').classList.add('hidden');
            document.getElementById('form-modificar').classList.remove('hidden');
            document.getElementById('confirmar-eliminacion').classList.add('hidden');
            // Trigger change event for tipo_habilitacion to show/hide sections
            const tipoHabilitacion = document.getElementById('tipo_habilitacion');
            if (tipoHabilitacion) {
                tipoHabilitacion.dispatchEvent(new Event('change'));
            }
        }

        // Función para mostrar sección de eliminar
        function mostrarEliminar(alumnoNombre) {
            document.getElementById('alumno-eliminar').textContent = alumnoNombre;
            document.getElementById('seleccion-accion').classList.add('hidden');
            document.getElementById('form-modificar').classList.add('hidden');
            document.getElementById('confirmar-eliminacion').classList.remove('hidden');
        }

        // Función para cancelar eliminación
        function cancelarEliminar() {
            document.getElementById('confirmar-eliminacion').classList.add('hidden');
            document.getElementById('seleccion-accion').classList.remove('hidden');
        }

        // Función para confirmar eliminación
        function confirmarEliminar() {
            // Crear formulario para eliminación
            const form = document.createElement('form');
            form.method = 'POST';
            const selectedRut = document.getElementById('buscar_alumno').value;
            form.action = '{{ route("habilitaciones.destroy", ":rut") }}'.replace(':rut', selectedRut);
            form.innerHTML = '@csrf @method("DELETE")';
            document.body.appendChild(form);
            form.submit();
        }

        // Función para cancelar edición
        function cancelarEdicion() {
            document.getElementById('form-modificar').classList.add('hidden');
            document.getElementById('seleccion-accion').classList.remove('hidden');
        }

        // Función para guardar cambios
        function guardarCambios() {
            if (validarFormulario() && confirm('¿Desea guardar los cambios realizados?')) {
                const form = document.getElementById('form-modificar');
                const selectedRut = document.getElementById('buscar_alumno').value;
                form.action = '{{ route("habilitaciones.update", ":rut") }}'.replace(':rut', selectedRut);
                form.submit();
            }
        }

        // Inicializar secciones ocultas
        document.addEventListener('DOMContentLoaded', function() {
            if (!hasHabilitacion) {
                // Hide sections when no habilitacion is found
                var elementsToHide = ['seleccion-accion', 'form-modificar', 'confirmar-eliminacion'];
                for (var i = 0; i < elementsToHide.length; i++) {
                    var element = document.getElementById(elementsToHide[i]);
                    if (element) {
                        element.classList.add('hidden');
                    }
                }
            }
        });
    </script>

</body>
</html>
