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
                max-width: 1100px;
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
                overflow-x: auto;
                /* Scroll vertical para tablas largas */
                max-height: 450px;
                overflow-y: auto;
                border: 1px solid #CED4DA;
                border-radius: 4px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.9em;
            }
            table th, table td {
                border-bottom: 1px solid #CED4DA; /* Borde solo horizontal */
                padding: 12px 14px;
                text-align: left;
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
            <h1>Listado de Habilitaciones</h1>
            <img src="{{ asset('imagenes/ucsc.png') }}" alt="Logo UCSC">
        </header>

        <form action="#" method="POST" onsubmit="return false;">
            <fieldset>
                <legend>Generar Reporte</legend>
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label for="tipo_listado" class="required">Tipo de Listado</label>
                        <select id="tipo_listado" name="tipo_listado" required>
                            <option value="" disabled selected>Seleccione un tipo...</option>
                            <option value="semestral">Listado Semestral (Actual)</option>
                            <option value="historico">Listado Histórico (Completo)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro_modalidad">Filtrar por Modalidad</label>
                        <select id="filtro_modalidad" name="filtro_modalidad">
                            <option value="todas">Todas las modalidades</option>
                            <option value="PrIng">PrIng (Proyecto de Ingeniería)</option>
                            <option value="PrInv">PrInv (Proyecto de Innovación)</option>
                            <option value="PrTut">PrTut (Práctica Tutelada)</option>
                        </select>
                    </div>

                    <div class="form-group button-container">
                        <button type="submit">Generar Listado</button>
                    </div>
                </div>
            </fieldset>
        </form>

        <div class="seccion-tabla">
            <fieldset>
                <legend>Resultados del Listado</legend>
                
                <div class="tabla-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Semestre</th>
                                <th>RUT Alumno</th>
                                <th>Nombre Alumno</th>
                                <th>Modalidad</th>
                                <th>Profesor Guía / Tutor</th>
                                <th>Profesor Comisión</th>
                                <th>Supervisor (Práctica)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-2</td>
                                <td>12345678</td>
                                <td>Ana García López</td>
                                <td>PrIng</td>
                                <td>Profesor DINF Uno</td>
                                <td>Profesor DINF Dos</td>
                                <td>N/A</td>
                            </tr>
                            <tr>
                                <td>2024-1</td>
                                <td>23456789</td>
                                <td>Luis Martínez Fernández</td>
                                <td>PrTut</td>
                                <td>Profesor DINF Tres</td>
                                <td>N/A</td>
                                <td>Juan Pérez (Empresa X)</td>
                            </tr>
                            <tr>
                                <td>2023-2</td>
                                <td>34567890</td>
                                <td>Carla Sánchez Ruiz</td>
                                <td>PrInv</td>
                                <td>Profesor DINF Cuatro</td>
                                <td>Profesor DINF Uno</td>
                                <td>N/A</td>
                            </tr>
                            <tr>
                                <td>2023-1</td>
                                <td>45678901</td>
                                <td>Miguel Rodríguez Pérez</td>
                                <td>PrTut</td>
                                <td>Profesor DINF Dos</td>
                                <td>N/A</td>
                                <td>María González (Empresa Y)</td>
                            </tr>
                            <tr>
                                <td>2022-2</td>
                                <td>56789012</td>
                                <td>Elena Gómez Martín</td>
                                <td>PrIng</td>
                                <td>Profesor DINF Uno</td>
                                <td>Profesor DINF Tres</td>
                                <td>N/A</td>
                            </tr>
                            <tr>
                                <td>2022-1</td>
                                <td>67890123</td>
                                <td>David Fernández Alonso</td>
                                <td>PrTut</td>
                                <td>Profesor DINF Cuatro</td>
                                <td>N/A</td>
                                <td>Pedro Jiménez (Empresa Z)</td>
                            </tr>
                             <tr>
                                <td>2021-2</td>
                                <td>78901234</td>
                                <td>Sofía Moreno Núñez</td>
                                <td>PrInv</td>
                                <td>Profesor DINF Dos</td>
                                <td>Profesor DINF Uno</td>
                                <td>N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </fieldset>
        </div>

    </div>

</x-app-layout>
