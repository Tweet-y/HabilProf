<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Gestión de Habilitaciones</title>
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

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }
        .button-link {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: background-color 0.2s ease;
            text-decoration: none;
            display: inline-block;
            background-color: #0056A8;
            color: white;
        }
        .button-link:hover {
            background-color: #004180;
        }
        .button-link.primary {
            background-color: #0056A8;
        }
        .button-link.primary:hover {
            background-color: #004180;
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Bienvenido</h1>
            <img src="{{ asset('imagenes/ucsc2.png') }}" alt="Logo UCSC">
        </header>

        <main class="menu-container">
            <h2>Sistema de Gestión de Habilitaciones HabilProf</h2>
            <p class="intro">Sistema para la gestión de habilitaciones profesionales (PrIng, PrInv, PrTut).</p>

            <div class="button-container">
                <a href="/menu" class="button-link">Ir al Menú</a>
                <a href="/login" class="button-link primary">Iniciar Sesión</a>
            </div>
        </main>
    </div>

</body>
</html>
