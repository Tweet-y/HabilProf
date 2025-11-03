<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar o Eliminar Habilitación</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #F8F9FA; /* <- Color cambiado */
            color: #222222; /* <- Color cambiado */
        }
        .container {
            max-width: 900px; /* <- Se mantiene en 900px (sin modificar) */
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); /* <- Sombra suavizada */
            border: 1px solid #CED4DA; /* <- Color cambiado */
            overflow: hidden; 
        }
        header {
            padding: 20px 30px;
            background-color: #FFFFFF; /* <- Color cambiado */
            border-bottom: 1px solid #CED4DA; /* <- Color cambiado */
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header h1 {
            margin: 0;
            color: #222222; /* <- Color cambiado */
            font-size: 1.8em;
        }
        header img {
            max-height: 50px;
            width: auto;
            margin-left: 20px;
        }
        form, .seccion-accion {
            padding: 30px;
        }
        fieldset {
            border: 1px solid #CED4DA; /* <- Color cambiado */
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 25px;
            background-color: #F8F9FA; /* <- Color cambiado */
        }
        legend {
            font-size: 1.2em;
            font-weight: 600;
            padding: 0 10px;
            color: #E60026; /* <- Color cambiado (Rojo UCSC) */
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group-full {
            grid-column: 1 / -1;
        }
        label {
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 0.9em;
            color: #333333; /* <- Color cambiado */
        }
        label.required::after {
            content: ' *';
            color: #E60026; /* <- Color cambiado (Rojo UCSC) */
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #CED4DA; /* <- Color cambiado */
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
            background-color: #fff;
        }
        input:read-only {
            background-color: #E9ECEF; /* <- Color cambiado */
            color: #6C757D; /* <- Color cambiado */
            border-color: #CED4DA; /* <- Color cambiado */
            cursor: not-allowed;
        }
        .help-text {
            font-size: 0.85em;
            color: #555555; /* <- Color cambiado */
            margin-top: 4px;
        }
        .seccion-condicional {
            border-top: 2px dashed #0056A8; /* <- Color cambiado (Azul UCSC) */
            margin-top: 20px;
            padding-top: 20px;
        }
        
        /* --- ESTILOS PARA MENSAJES Y ACCIONES --- */
        
        .message {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .message.error {
            color: #721C24; /* <- Color cambiado */
            background-color: #F8D7DA; /* <- Color cambiado */
            border: 1px solid #F5C6CB; /* <- Color cambiado */
        }
        .message.success {
            color: #155724; /* <- Color cambiado */
            background-color: #D4EDDA; /* <- Color cambiado */
            border: 1px solid #C3E6CB; /* <- Color cambiado */
        }
        .message.info {
            color: #0C5460; /* <- Color cambiado */
            background-color: #D1ECF1; /* <- Color cambiado */
            border: 1px solid #BEE5EB; /* <- Color cambiado */
        }
        
        /* Botones de acción */
        .button-container {
            text-align: right;
            border-top: 1px solid #CED4DA; /* <- Color cambiado */
            padding-top: 20px;
            margin-top: 20px;
        }
        button {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }
        
        /* --- Paleta de Botones UCSC --- */

        /* Botón Primario (Acción Segura - Modificar, Guardar, Buscar) */
        button.btn-primary {
            background-color: #0056A8; /* <- Color cambiado (Azul UCSC) */
            color: white;
        }
        button.btn-primary:hover {
            background-color: #004180; /* <- Color cambiado */
        }

        /* Botón Secundario (Cancelar) */
        button.btn-secondary {
            background-color: #6C757D; /* <- Color cambiado (Gris) */
            color: white;
            margin-right: 10px;
            border: none; /* <- Modificado */
        }
        button.btn-secondary:hover {
            background-color: #5A6268; /* <- Color cambiado */
        }
        
        /* Botón de Peligro (Eliminar) */
        button.btn-danger {
            background-color: #E60026; /* <- Color cambiado (Rojo UCSC) */
            color: white;
            margin-right: 10px;
        }
        button.btn-danger:hover {
            background-color: #C00020; /* <- Color cambiado */
        }

        /* Estilos para el bloque de elección */
        .seccion-accion {
            text-align: center;
        }
        .seccion-accion h2 {
            color: #222222; /* <- Color cambiado */
            margin-top: 0;
        }
        .seccion-accion p {
            font-size: 1.1em;
            margin-bottom: 25px;
        }
        .seccion-accion .button-container {
            text-align: center;
            border-top: none;
        }

        /* Lógica de display condicional para flujo de actualización/eliminación */
        .hidden {
            display: none;
        }

    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Actualizar o Eliminar Habilitación</h1>
            <img src="imagenes/ucsc.png" alt="Logo UCSC">
        </header>

        <form action="#" method="POST" onsubmit="return false;" class="seccion-accion">
            <fieldset>
                <legend>Buscar Habilitación Existente</legend>
                
                <div class="form-group form-group-full">
                    <label for="buscar_alumno" class="required">Seleccionar Alumno</label>
                    <select id="buscar_alumno" name="buscar_alumno_rut" required>
                        <option value="" disabled selected>Buscar y seleccionar un alumno...</option>
                        @if(isset($alumnos) && count($alumnos) > 0)
                            @foreach($alumnos as $alumno)
                                <option value="{{ $alumno->rut_alumno }}">
                                    {{ $alumno->apellido_alumno }}, {{ $alumno->nombre_alumno }} ({{ $alumno->rut_alumno }})
                                </option>
                            @endforeach
                        @else
                            <option value="" disabled>No hay alumnos disponibles</option>
                        @endif
                    </select>
                </div>
                
                <div class="button-container">
                    <button type="button" class="btn-primary" onclick="buscarHabilitacion()">Buscar Habilitación</button>
                </div>
            </fieldset>
        </form>

        <div class="seccion-accion hidden" id="seleccion-accion">
            <hr style="border: 0; border-top: 1px dashed #CED4DA; margin: 0 30px 30px;">
            <h2>Alumno Seleccionado: <strong id="alumno-seleccionado">Nombre Apellido</strong></h2>
            <p>¿Desea eliminar o modificar los datos de esta habilitación?</p>

            <div class="button-container">
                <button type="button" class="btn-primary" onclick="mostrarModificar()">Modificar Datos</button>
                <button type="button" class="btn-danger" onclick="mostrarEliminar()">Eliminar Habilitación</button>
            </div>
        </div>

        
        <form action="#" method="POST" onsubmit="return false;" class="hidden" id="form-modificar">
            <fieldset>
                <legend>Datos Principales (Editando)</legend>
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label for="tipo_habilitacion" class="required">Tipo de Habilitación</label>
                        <select id="tipo_habilitacion" name="tipo_habilitacion" required>
                            <option value="PrIng" selected>PrIng (Proyecto de Ingeniería)</option>
                            <option value="PrInv">PrInv (Proyecto de Innovación)</option>
                            <option value="PrTut">PrTut (Práctica Tutelada)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="semestre_inicio" class="required">Semestre de Inicio</label>
                        <select id="semestre_inicio" name="semestre_inicio" required>
                            <option value="2025-1" selected>2025-1</option>
                            <option value="2025-2">2025-2</option>
                            <option value="2026-1">2026-1</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nota_final">Nota Final</label>
                        <input type="number" id="nota_final" name="nota_final"
                               min="1.0" max="7.0" step="0.1" value="5.5" readonly>
                        <small class="help-text">La nota final se actualiza automáticamente.</small>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Datos del Alumno (Solo Lectura)</legend>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre_alumno">Nombre Alumno</label>
                        <input type="text" id="nombre_alumno" name="nombre_alumno" readonly value="Ana">
                    </div>
                    <div class="form-group">
                        <label for="apellido_alumno">Apellido Alumno</label>
                        <input type="text" id="apellido_alumno" name="apellido_alumno" readonly value="García López">
                    </div>
                    <div class="form-group">
                        <label for="rut_alumno">RUT Alumno</label>
                        <input type="text" id="rut_alumno" name="rut_alumno" readonly value="12345678">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Descripción del Trabajo (Editando)</legend>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="titulo" class="required">Título del Trabajo</label>
                        <input type="text" id="titulo" name="titulo" required 
                               minlength="6" maxlength="80" 
                               value="Sistema de Gestión para el DINF">
                    </div>
                    <div class="form-group form-group-full">
                        <label for="descripcion" class="required">Descripción / Resumen</label>
                        <textarea id="descripcion" name="descripcion" required 
                                  minlength="30" maxlength="500">Un sistema web para gestionar las habilitaciones profesionales, prácticas y proyectos de los alumnos del departamento.</textarea>
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
                                <option value="" disabled selected>Seleccione un profesor guía...</option>
                                @if(isset($profesores) && count($profesores) > 0)
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}">
                                            {{ $profesor->apellido_profesor }}, {{ $profesor->nombre_profesor }} ({{ $profesor->rut_profesor }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="seleccion_co_guia">Profesor Co-Guía (UCSC) (Opcional)</label>
                            <select id="seleccion_co_guia" name="seleccion_co_guia_rut">
                                <option value="" selected>Ninguno (Opcional)</option>
                                <option value="11223344">Profesor UCSC (No-DINF) (11223344)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="seleccion_comision" class="required">Profesor Comisión (DINF)</label>
                            <select id="seleccion_comision" name="seleccion_comision_rut" required>
                                <option value="" disabled selected>Seleccione un profesor comisión...</option>
                                @if(isset($profesores) && count($profesores) > 0)
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}">
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
                                   value="Empresa Ejemplo S.A.">
                        </div>

                        <div class="form-group">
                            <label for="nombre_supervisor" class="required">Nombre Supervisor (Empresa)</label>
                            <input type="text" id="nombre_supervisor" name="nombre_supervisor" 
                                   maxlength="50" pattern="[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+" 
                                   value="Juan Pérez">
                        </div>

                        <div class="form-group">
                            <label for="seleccion_tutor" class="required">Profesor Tutor (DINF)</label>
                            <select id="seleccion_tutor" name="seleccion_tutor_rut" required>
                                <option value="" disabled selected>Seleccione un tutor...</option>
                                @if(isset($profesores) && count($profesores) > 0)
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->rut_profesor }}">
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
                <button type="submit" class="btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
            </div>
        </form>

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

    <script>
        // Función para buscar habilitación
        function buscarHabilitacion() {
            const select = document.getElementById('buscar_alumno');
            const selectedOption = select.options[select.selectedIndex];
            if (selectedOption.value) {
                const alumnoNombre = selectedOption.text.split(' (')[0];
                document.getElementById('alumno-seleccionado').textContent = alumnoNombre;
                document.getElementById('seleccion-accion').classList.remove('hidden');
            } else {
                alert('Por favor, seleccione un alumno.');
            }
        }

        // Función para mostrar sección de modificar
        function mostrarModificar() {
            document.getElementById('seleccion-accion').classList.add('hidden');
            document.getElementById('form-modificar').classList.remove('hidden');
            document.getElementById('confirmar-eliminacion').classList.add('hidden');
        }

        // Función para mostrar sección de eliminar
        function mostrarEliminar() {
            const alumnoNombre = document.getElementById('alumno-seleccionado').textContent;
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
            if (confirm('¿Está seguro de eliminar los datos de la Habilitación Profesional de ' + document.getElementById('alumno-eliminar').textContent + '?')) {
                alert('Habilitación eliminada correctamente.');
                // Aquí iría la lógica para eliminar la habilitación
            }
        }

        // Lógica para mostrar/ocultar secciones según tipo de habilitación
        document.getElementById('tipo_habilitacion').addEventListener('change', function() {
            const tipo = this.value;
            if (tipo === 'PrTut') {
                document.getElementById('seccion-pring-prinv').classList.add('hidden');
                document.getElementById('seccion-prtut').classList.remove('hidden');
            } else {
                document.getElementById('seccion-pring-prinv').classList.remove('hidden');
                document.getElementById('seccion-prtut').classList.add('hidden');
            }
        });

        // Función para cancelar edición
        function cancelarEdicion() {
            document.getElementById('form-modificar').classList.add('hidden');
            document.getElementById('seleccion-accion').classList.remove('hidden');
        }

        // Función para guardar cambios
        function guardarCambios() {
            if (confirm('¿Desea guardar los cambios realizados?')) {
                alert('Los datos fueron modificados correctamente.');
                // Aquí iría la lógica para guardar los cambios
            }
        }

        // Inicializar secciones ocultas
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('seleccion-accion').classList.add('hidden');
            document.getElementById('form-modificar').classList.add('hidden');
            document.getElementById('confirmar-eliminacion').classList.add('hidden');
        });
    </script>

</body>
</html>
