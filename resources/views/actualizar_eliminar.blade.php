<x-app-layout>

    <!-- 1. Título para la barra de cabecera -->
    <x-slot name="header">
        <h2 class="relative inline-block text-2xl md:text-3xl font-semibold tracking-tight text-gray-900">
            <span class="bg-gradient-to-r from-[#E60026] to-[#0056A8] bg-clip-text text-transparent">Actualizar o Eliminar Habilitación</span>
            <span class="absolute left-0 -bottom-1 h-[3px] w-full rounded-full bg-gradient-to-r from-[#E60026] to-[#0056A8]"></span>
        </h2>
    </x-slot>

    <!-- 2. CSS específico para esta página -->
    <x-slot name="header_styles">
        <!-- Enlace a CSS general de formularios -->
        <link rel="stylesheet" href="{{ asset('css/form.css') }}">
        <style>
            /* Contenedor principal */
            .container { margin-top: 20px; }

            /* Estilos de botones para acciones principales */
            button.btn-primary, button.btn-secondary, button.btn-danger {
                display: inline-block;
                padding: 12px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 1em;
                font-weight: 600;
                transition: background-color 0.2s ease;
                text-decoration: none;
                text-align: center;
                user-select: none;
            }
            /* Colores de botones */
            button.btn-primary { background-color: #0056A8; color: white; }
            button.btn-primary:hover { background-color: #004180; }
            button.btn-secondary { background-color: #6C757D; color: white; }
            button.btn-secondary:hover { background-color: #5A6268; }
            button.btn-danger { background-color: #E60026; color: white; }
            button.btn-danger:hover { background-color: #C00020; }
        </style>
    </x-slot>

    <!-- 3. Contenido de tu página -->
    <div class="container">

        <!-- Formulario de Búsqueda de Habilitaciones -->
        <form action="{{ route('habilitaciones.index') }}" method="GET" class="seccion-accion">
            <fieldset>
                <legend>Buscar Habilitación Existente</legend>
                <div class="form-group form-group-full">
                    <label for="buscar_alumno" class="required">Seleccionar Alumno</label>
                    <select id="buscar_alumno" name="rut_alumno" required>
                        <option value="" disabled {{ !request('rut_alumno') ? 'selected' : '' }}>Buscar y seleccionar un alumno...</option>
                        @if(isset($alumnos) && count($alumnos) > 0)
                            @foreach($alumnos as $alumno)
                                <!-- Mostrar apellido, nombre y RUT para fácil identificación -->
                                <option value="{{ $alumno->rut_alumno }}" {{ (request('rut_alumno') == $alumno->rut_alumno) ? 'selected' : '' }}>
                                    {{ $alumno->apellido_alumno }}, {{ $alumno->nombre_alumno }} ({{ $alumno->rut_alumno }})
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No hay alumnos con habilitaciones disponibles</option>
                        @endif
                    </select>
                </div>
                <div class="button-container">
                    <button type="submit" class="btn-primary">Buscar Habilitación</button>
                </div>
            </fieldset>
        </form>

        @if(session('success'))
            <div class="message success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="error-message" role="alert">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="error-message" role="alert">
                <strong>Error de validación:</strong>
                <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="js-validation-error" class="error-message" style="display: none; margin-bottom: 20px;"></div>

        <!-- Acciones disponibles para la habilitación encontrada -->
        @if($habilitacion)
            @php
                // Preparar nombre completo del alumno para mostrar
                $alumnoNombre = $habilitacion->alumno->apellido_alumno . ', ' . $habilitacion->alumno->nombre_alumno;
            @endphp
            <div class="seccion-accion" id="seleccion-accion">
                <hr style="border: 0; border-top: 1px dashed #CED4DA; margin: 0 30px 30px;">
                <h2>Alumno Seleccionado: <strong>{{ $alumnoNombre }}</strong></h2>
                <p>¿Desea eliminar o modificar los datos de esta habilitación?</p>
                <div class="button-container">
                    <!-- Botón para mostrar formulario de modificación -->
                    <button type="button" class="btn-primary" onclick="mostrarModificar()">Modificar Datos</button>
                    <!-- Botón para mostrar confirmación de eliminación -->
                    <button type="button" class="btn-danger" onclick="mostrarEliminar('{{ $alumnoNombre }}')">Eliminar Habilitación</button>
                </div>
            </div>

            <form action="#" method="POST" onsubmit="return false;" id="form-modificar" style="display: none;">
                @csrf
                @method('PUT')
                
                <fieldset>
                    <legend>Datos Principales (Editando)</legend>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="tipo_habilitacion" class="required">Tipo de Habilitación</label>
                            <select id="tipo_habilitacion" name="tipo_habilitacion" required>
                                <option value="PrIng" {{ (old('tipo_habilitacion', $habilitacion->proyecto ? $habilitacion->proyecto->tipo_proyecto : ($habilitacion->prTut ? 'PrTut' : '')) == 'PrIng') ? 'selected' : '' }}>PrIng (Proyecto de Ingeniería)</option>
                                <option value="PrInv" {{ (old('tipo_habilitacion', $habilitacion->proyecto ? $habilitacion->proyecto->tipo_proyecto : ($habilitacion->prTut ? 'PrTut' : '')) == 'PrInv') ? 'selected' : '' }}>PrInv (Proyecto de Innovación)</option>
                                <option value="PrTut" {{ (old('tipo_habilitacion', $habilitacion->proyecto ? $habilitacion->proyecto->tipo_proyecto : ($habilitacion->prTut ? 'PrTut' : '')) == 'PrTut') ? 'selected' : '' }}>PrTut (Práctica Tutelada)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="semestre_inicio" class="required">Semestre de Inicio</label>
                            <select id="semestre_inicio" name="semestre_inicio" required>
                                <option value="" disabled>Seleccione semestre...</option>
                                @foreach($semestres as $semestre)
                                    <option value="{{ $semestre }}" {{ (old('semestre_inicio', $habilitacion->semestre_inicio) == $semestre) ? 'selected' : '' }}>{{ $semestre }}</option>
                                @endforeach
                            </select>
                            <small class="help-text">Semestre anterior, actual y siguiente.</small>
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
                            <input type="text" id="titulo" name="titulo" required
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
                            <textarea id="descripcion" name="descripcion" required
                                      minlength="30" maxlength="500"
                                      class="{{ $errors->has('descripcion') ? 'field-error' : '' }}">{{ old('descripcion', $habilitacion->descripcion ?? '') }}</textarea>
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
                                <select id="seleccion_guia_rut" name="seleccion_guia_rut">
                                    <option value="" disabled {{ !(old('seleccion_guia_rut', $habilitacion->proyecto->rut_profesor_guia ?? '')) ? 'selected' : '' }}>Seleccione un profesor guía...</option>
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}" {{ (old('seleccion_guia_rut', $habilitacion->proyecto->rut_profesor_guia ?? '') == $profesor->rut_profesor) ? 'selected' : '' }}>
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="seleccion_co_guia_rut">Profesor Co-Guía (UCSC) (Opcional)</label>
                                <select id="seleccion_co_guia_rut" name="seleccion_co_guia_rut">
                                    <option value="">Ninguno (Opcional)</option>
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}" {{ (old('seleccion_co_guia_rut', $habilitacion->proyecto->rut_profesor_co_guia ?? '') == $profesor->rut_profesor) ? 'selected' : '' }}>
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="seleccion_comision_rut" class="required">Profesor Comisión (DINF)</label>
                                <select id="seleccion_comision_rut" name="seleccion_comision_rut">
                                    <option value="" disabled {{ !(old('seleccion_comision_rut', $habilitacion->proyecto->rut_profesor_comision ?? '')) ? 'selected' : '' }}>Seleccione un profesor comisión...</option>
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}" {{ (old('seleccion_comision_rut', $habilitacion->proyecto->rut_profesor_comision ?? '') == $profesor->rut_profesor) ? 'selected' : '' }}>
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                </select>
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
                                       value="{{ old('nombre_empresa', $habilitacion->prTut->nombre_empresa ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="nombre_supervisor" class="required">Nombre Supervisor (Empresa)</label>
                                <input type="text" id="nombre_supervisor" name="nombre_supervisor"
                                       maxlength="50" pattern="[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+"
                                       value="{{ old('nombre_supervisor', $habilitacion->prTut->nombre_supervisor ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label for="seleccion_tutor_rut" class="required">Profesor Tutor (DINF)</label>
                                <select id="seleccion_tutor_rut" name="seleccion_tutor_rut">
                                    <option value="" disabled {{ !(old('seleccion_tutor_rut', $habilitacion->prTut->rut_profesor_tutor ?? '')) ? 'selected' : '' }}>Seleccione un tutor...</option>
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}" {{ (old('seleccion_tutor_rut', $habilitacion->prTut->rut_profesor_tutor ?? '') == $profesor->rut_profesor) ? 'selected' : '' }}>
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="button-container">
                    <button type="button" onclick="cancelarEdicion()">Cancelar Edición</button>
                    <button type="button" onclick="guardarCambios()">Guardar Cambios</button>
                </div>
            </form>
        @endif

        <!-- Diálogo de Confirmación de Eliminación (Oculto por defecto) -->
        <div class="seccion-accion" id="confirmar-eliminacion" style="display: none;">
            <hr style="border: 0; border-top: 1px dashed #CED4DA; margin: 0 30px 30px;">
            <h2>Confirmar Eliminación</h2>
            <!-- Nombre del alumno se inserta dinámicamente por JavaScript -->
            <p>¿Desea eliminar la habilitación del Alumno <strong id="alumno-eliminar">Nombre Apellido</strong>?</p>
            <p class="help-text">Esta acción no se puede deshacer y eliminará todos los datos relacionados.</p>
            <div class="button-container">
                <!-- Botón para cancelar y volver -->
                <button type="button" class="btn-secondary" onclick="cancelarEliminar()">Cancelar</button>
                <!-- Botón para confirmar eliminación -->
                <button type="button" class="btn-danger" onclick="confirmarEliminar()">Sí, eliminar</button>
            </div>
        </div>

        <!-- Modal de Confirmación para Actualizar -->
        <x-modal name="confirm-update" :show="false" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Confirmar
                </h2>

                <p class="mt-4 text-sm text-gray-600">
                    ¿Desea guardar los cambios realizados?
                </p>

                <div class="flex items-center justify-between mt-6">
                    <x-secondary-button onclick="closeModal('confirm-update')">
                        Cancelar Edición
                    </x-secondary-button>

                    <x-primary-button onclick="confirmarGuardarCambios()">
                        Guardar Cambios
                    </x-primary-button>
                </div>
            </div>
        </x-modal>
    </div>

    <!-- 4. Scripts al final de la página -->
    <!-- Script de validación general -->
    <script src="{{ asset('js/validacion.js') }}"></script>
    <!-- Script específico para formularios de habilitación -->
    <script src="{{ asset('js/formHabilitacion.js') }}"></script>
    <script>
        // Variable para saber si hay habilitación cargada
        var hasHabilitacion = @json($habilitacion ? true : false);

        // Función para mostrar el formulario de modificación
        function mostrarModificar() {
            // Ocultar otras secciones
            document.getElementById('seleccion-accion').style.display = 'none';
            document.getElementById('confirmar-eliminacion').style.display = 'none';
            // Mostrar formulario de modificación
            document.getElementById('form-modificar').style.display = 'block';
            // Disparar cambio para mostrar campos condicionales correctos
            document.getElementById('tipo_habilitacion').dispatchEvent(new Event('change'));
        }

        // Función para mostrar confirmación de eliminación
        function mostrarEliminar(alumnoNombre) {
            // Insertar nombre del alumno en el mensaje
            document.getElementById('alumno-eliminar').textContent = alumnoNombre;
            // Ocultar otras secciones
            document.getElementById('seleccion-accion').style.display = 'none';
            document.getElementById('form-modificar').style.display = 'none';
            // Mostrar confirmación de eliminación
            document.getElementById('confirmar-eliminacion').style.display = 'block';
        }

        // Función para cancelar eliminación y volver a opciones
        function cancelarEliminar() {
            // Ocultar confirmación
            document.getElementById('confirmar-eliminacion').style.display = 'none';
            // Mostrar opciones principales
            document.getElementById('seleccion-accion').style.display = 'block';
        }

        // Función para confirmar y ejecutar eliminación
        function confirmarEliminar() {
            // Crear formulario dinámico para envío POST con DELETE
            const form = document.createElement('form');
            form.method = 'POST';
            // Obtener RUT del alumno seleccionado
            const selectedRut = document.getElementById('buscar_alumno').value;
            // Construir URL con el RUT del alumno
            form.action = '{{ route("habilitaciones.destroy", ["alumno" => ":rut"]) }}'.replace(':rut', selectedRut);
            // Incluir token CSRF y método DELETE
            form.innerHTML = '@csrf @method("DELETE")';
            // Agregar al DOM y enviar
            document.body.appendChild(form);
            form.submit();
        }

        // Función para cancelar modificación y volver a opciones
        function cancelarEdicion() {
            // Ocultar formulario de modificación
            document.getElementById('form-modificar').style.display = 'none';
            // Mostrar opciones principales
            document.getElementById('seleccion-accion').style.display = 'block';
        }

        // Función para guardar cambios con validación previa
        async function guardarCambios() {
            // Ejecutar validación JavaScript antes de mostrar modal
            if (await validarFormulario()) {
                // Mostrar modal de confirmación
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-update' }));
            }
        }

        // Función para confirmar y ejecutar guardado de cambios
        function confirmarGuardarCambios() {
            // Obtener formulario de modificación
            const form = document.getElementById('form-modificar');
            // Obtener RUT del alumno seleccionado
            const selectedRut = document.getElementById('buscar_alumno').value;
            // Establecer acción del formulario con RUT del alumno
            form.action = '{{ route("habilitaciones.update", ["alumno" => ":rut"]) }}'.replace(':rut', selectedRut);
            // Enviar formulario
            form.submit();
        }

        // Función para cerrar modal de confirmación
        function closeModal(modalName) {
            // Disparar evento para cerrar modal específico
            window.dispatchEvent(new CustomEvent('close-modal', { detail: modalName }));
        }

        // Función para mostrar/ocultar secciones condicionales según tipo de habilitación
        function toggleSections() {
            // Obtener elementos del DOM
            const tipoHabilitacion = document.getElementById('tipo_habilitacion');
            const seccionProyecto = document.getElementById('seccion-pring-prinv');
            const seccionPractica = document.getElementById('seccion-prtut');

            // Verificar que existan los elementos
            if (!tipoHabilitacion || !seccionProyecto || !seccionPractica) return;

            // Obtener valor seleccionado
            const valor = tipoHabilitacion.value;

            // Ocultar ambas secciones inicialmente
            seccionProyecto.style.display = 'none';
            seccionPractica.style.display = 'none';

            // Remover atributo 'required' de campos condicionales
            document.querySelectorAll('#seccion-pring-prinv [name]').forEach(el => {
                if(el.name !== 'seleccion_co_guia_rut') el.required = false;
            });
            document.querySelectorAll('#seccion-prtut [name]').forEach(el => el.required = false);

            // Mostrar sección correspondiente y hacer campos requeridos
            if (valor === 'PrTut') {
                // Mostrar sección de práctica tutelada
                seccionPractica.style.display = 'block';
                // Hacer requeridos todos los campos de PrTut
                document.querySelectorAll('#seccion-prtut input[name], #seccion-prtut select[name]').forEach(el => el.required = true);
            } else if (valor === 'PrIng' || valor === 'PrInv') {
                // Mostrar sección de proyecto
                seccionProyecto.style.display = 'block';
                // Hacer requeridos los campos obligatorios de proyecto (excepto co-guía)
                document.querySelectorAll('#seccion-pring-prinv select[name]').forEach(el => {
                    if(el.name !== 'seleccion_co_guia_rut') el.required = true;
                });
            }
        }

        // Inicializar funcionalidades cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar secciones si no hay habilitación cargada
            if (!hasHabilitacion) {
                const elementsToHide = ['seleccion-accion', 'form-modificar', 'confirmar-eliminacion'];
                elementsToHide.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.style.display = 'none';
                });
            }

            // Configurar listener para cambios en tipo de habilitación
            const tipoHabilitacion = document.getElementById('tipo_habilitacion');
            if (tipoHabilitacion) {
                tipoHabilitacion.addEventListener('change', toggleSections);
                // Ejecutar toggle inicial si hay habilitación
                if (hasHabilitacion) {
                    toggleSections();
                }
            }

            // Scroll automático a mensajes del servidor (errores o éxito)
            const serverMessage = document.querySelector('.error-message, .success-message');
            if (serverMessage) {
                serverMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</x-app-layout>