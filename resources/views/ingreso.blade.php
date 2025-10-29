<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingreso de Habilitación Profesional (Nueva Paleta)</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #F8F9FA; /* Gris Claro (Fondo) */
            color: #222222;
        }
        .container {
            max-width: 900px;
            margin: 20px auto;
            background-color: #FFFFFF; /* Blanco (Contenedor) */
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #CED4DA; /* Gris (Borde) */
        }
        header {
            padding: 20px 30px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #CED4DA;
            border-radius: 8px 8px 0 0;
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
        form {
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
            color: #333333;
        }
        label.required::after {
            content: ' *';
            color: #E60026; /* Rojo Primario (UCSC) */
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #CED4DA; /* Gris (Borde) */
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
            background-color: #fff;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        input:read-only {
            background-color: #E9ECEF; /* Gris Lectura */
            color: #6C757D;
            border-color: #CED4DA;
            cursor: not-allowed;
        }
        .help-text {
            font-size: 0.85em;
            color: #555555;
            margin-top: 4px;
        }
        .seccion-condicional {
            border-top: 2px dashed #0056A8; /* Azul Secundario (UCSC) */
            margin-top: 20px;
            padding-top: 20px;
        }
        .error-message {
            color: #721C24;
            background-color: #F8D7DA;
            border: 1px solid #F5C6CB;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
        }
        .button-container {
            text-align: right;
            border-top: 1px solid #CED4DA;
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
        button[type="submit"] {
            background-color: #E60026; /* Rojo Primario (UCSC) */
            color: white;
        }
        button[type="submit"]:hover {
            background-color: #C00020; /* Rojo más oscuro */
        }
        button[type="reset"] {
            background-color: #0056A8; /* Azul Secundario (UCSC) */
            color: white;
            margin-right: 10px;
        }
        button[type="reset"]:hover {
            background-color: #004180; /* Azul más oscuro */
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Ingreso de Habilitación Profesional</h1>
            <img src="imagenes/ucsc.png" alt="Logo UCSC">
        </header>

        <form action="#" method="POST" onsubmit="return false;">

            <div id="mensaje-error-general" class="error-message">
                Complete todos los campos destacados(*) o los campos no están bien escritos(*).
            </div>

            <fieldset>
                <legend>Datos Principales</legend>
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label for="tipo_habilitacion" class="required">Tipo de Habilitación</label>
                        <select id="tipo_habilitacion" name="tipo_habilitacion" required>
                            <option value="" disabled selected>Seleccione un tipo...</option>
                            <option value="PrIng">PrIng (Proyecto de Ingeniería)</option>
                            <option value="PrInv">PrInv (Proyecto de Innovación)</option>
                            <option value="PrTut">PrTut (Práctica Tutelada)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="semestre_inicio" class="required">Semestre de Inicio</label>
                        <select id="semestre_inicio" name="semestre_inicio" required>
                            <option value="2025-2">2025-2</option>
                            <option value="2026-1">2026-1</option>
                            <option value="2026-2">2026-2</option>
                            <option value="2027-1">2027-1</option>
                        </select>
                        <small class="help-text">Solo se muestran semestres futuros.</small>
                    </div>

                    <div class="form-group">
                        <label for="nota_final">Nota Final</label>
                        <input type="number" id="nota_final" name="nota_final" 
                               min="1.0" max="7.0" step="0.1" 
                               placeholder="Se actualizará desde R1" readonly>
                        <small class="help-text">Este campo no se puede modificar.</small>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Datos del Alumno</legend>
                
                <div id="alumnos-disponibles-lista">
                    <div class="form-group form-group-full">
                        <label for="selector_alumno" class="required">Seleccionar Alumno Habilitado</label>
                        <select id="selector_alumno" name="selector_alumno_rut" required>
                            <option value="" disabled selected>Buscar y seleccionar un alumno habilitado...</option>
                            <option value="12345678">García López, Ana (12345678)</option>
                            <option value="23456789">Martínez Fernández, Luis (23456789)</option>
                            <option value="34567890">Sánchez Ruiz, Carla (34567890)</option>
                            </select>
                    </div>
                </div>

                </fieldset>

            <fieldset>
                <legend>Descripción del Trabajo</legend>
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="titulo" class="required">Título del Trabajo</label>
                        <input type="text" id="titulo" name="titulo" required 
                               minlength="6" maxlength="80"
                               pattern="[a-zA-Z0-9\s.,;:''&quot;&quot;-_()]+" title="Solo alfanumérico y algunos símbolos.">
                        <small class="help-text">Entre 6 y 80 caracteres. Símbolos permitidos: . , ; : ' " - _ ( )</small>
                    </div>
                    <div class="form-group form-group-full">
                        <label for="descripcion" class="required">Descripción / Resumen</label>
                        <textarea id="descripcion" name="descripcion" required 
                                  minlength="30" maxlength="500"></textarea>
                        <small class="help-text">Entre 30 y 500 caracteres. Símbolos permitidos: . , ; : ' " - _ ( )</small>
                    </div>
                </div>
            </fieldset>

            <div id="seccion-pring-prinv" class="seccion-condicional">
                <fieldset>
                    <legend>Equipo Docente (PrIng / PrInv)</legend>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="seleccion_guia" class="required">Profesor Guía (DINF)</label>
                            <select id="seleccion_guia" name="seleccion_guia_rut">
                                <option value="" disabled selected>Seleccione un guía...</option>
                                <option value="98765432">Profesor DINF Uno (98765432)</option>
                                <option value="87654321">Profesor DINF Dos (87654321)</option>
                                </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="seleccion_co_guia">Profesor Co-Guía (UCSC)</label>
                            <select id="seleccion_co_guia" name="seleccion_co_guia_rut">
                                <option value="" selected>Ninguno (Opcional)</option>
                                <option value="11223344">Profesor UCSC (No-DINF) (11223344)</option>
                                <option value="22334455">Profesor DINF Tres (22334455)</option>
                                </select>
                        </div>

                        <div class="form-group">
                            <label for="seleccion_comision" class="required">Profesor Comisión (DINF)</label>
                            <select id="seleccion_comision" name="seleccion_comision_rut">
                                <option value="" disabled selected>Seleccione comisión...</option>
                                <option value="98765432">Profesor DINF Uno (98765432)</option>
                                <option value="87654321">Profesor DINF Dos (87654321)</option>
                                <option value="76543210">Profesor DINF Cuatro (76543210)</option>
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
                                   maxlength="50" pattern="[a-zA-Z0-9\s]+" required>
                            <small class="help-text">Alfanumérico, máx 50 caracteres.</small>
                        </div>

                        <div class="form-group">
                            <label for="nombre_supervisor" class="required">Nombre Supervisor (Empresa)</label>
                            <input type="text" id="nombre_supervisor" name="nombre_supervisor" 
                                   maxlength="50" pattern="[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+" required>
                            <small class="help-text">Alfabético, máx 50 caracteres.</small>
                        </div>

                        <div class="form-group">
                            <label for="seleccion_tutor" class="required">Profesor Tutor (DINF)</label>
                            <select id="seleccion_tutor" name="seleccion_tutor_rut" required>
                                <option value="" disabled selected>Seleccione un tutor...</option>
                                <option value="98765432">Profesor DINF Uno (98765432)</option>
                                <option value="87654321">Profesor DINF Dos (87654321)</option>
                                </select>
                        </div>
                    </div>
                </fieldset>
            </div>

            <div class="button-container">
                <button type="reset">Cancelar</button>
                <button type="submit">Confirmar Ingreso</button>
            </div>

        </form>
    </div>

</body>
</html>