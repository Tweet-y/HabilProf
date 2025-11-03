<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal - Gestión de Habilitaciones</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #F8F9FA; /* Gris Claro (Fondo) */
            color: #222222;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90vh;
        }
        .container {
            max-width: 800px;
            width: 100%;
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
        
        .menu-container {
            padding: 30px 40px;
            text-align: center;
        }
        .menu-container h2 {
            color: #E60026; /* Rojo Primario (UCSC) */
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.6em;
        }
        .menu-container p.intro {
            font-size: 1.1em;
            color: #555555;
            margin-bottom: 30px;
        }

        .menu-item {
            display: block;
            background-color: #F8F9FA; /* Gris Claro (Fondo Tarjeta) */
            border: 1px solid #CED4DA; /* Gris (Borde) */
            border-radius: 6px;
            padding: 20px 25px;
            margin-bottom: 15px;
            text-decoration: none;
            text-align: left;
            transition: all 0.2s ease-in-out;
        }
        .menu-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            border-color: #E60026; /* Rojo Primario (UCSC) */
        }
        .menu-item h3 {
            margin: 0 0 8px 0;
            color: #E60026; /* Rojo Primario (UCSC) */
            font-size: 1.3em;
        }
        .menu-item p {
            margin: 0;
            color: #333333;
            font-size: 0.95em;
            line-height: 1.5;
        }

    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Menú Principal</h1>
            <img src="imagenes/ucsc2.png" alt="Logo UCSC">
        </header>

        <main class="menu-container">
            <h2>Sistema de Gestión de Habilitaciones HabilProf</h2>
            <p class="intro">Seleccione la operación que desea realizar:</p>

            <a href="habilitaciones/ingreso" class="menu-item">
                <h3>1. Ingresar Nueva Habilitación</h3>
                <p>Registrar una nueva Habilitación Profesional (PrIng, PrInv o PrTut) para un alumno.</p>
            </a>

            <a href="actualizar_eliminar" class="menu-item">
                <h3>2. Actualizar o Eliminar Habilitación</h3>
                <p>Buscar una habilitación existente por alumno para modificar sus datos o eliminar el registro.</p>
            </a>

            <a href="listados" class="menu-item">
                <h3>3. Generar Listado de Habilitaciones</h3>
                <p>Consultar y generar un reporte de todas las habilitaciones, filtrado por semestre o historial completo.</p>
            </a>

        </main>
    </div>

</body>
</html>