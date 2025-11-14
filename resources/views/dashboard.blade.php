<x-app-layout>
    <style>
        /* Ajuste para que el fondo de la app sea el gris claro */
        body {
            background-color: #F8F9FA !important;
        }
        .container {
            max-width: 800px;
            width: 100%;
            margin: 20px auto; /* Reducido el margen superior para que esté más cerca de la nav */
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #CED4DA;
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
            color: #222222;
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
            color: #E60026;
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
            background-color: #F8F9FA;
            border: 1px solid #CED4DA;
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
            border-color: #E60026;
        }
        .menu-item h3 { margin: 0 0 8px 0; color: #E60026; font-size: 1.3em; }
        .menu-item p { margin: 0; color: #333333; font-size: 0.95em; }
    </style>

    <div class="container">
        <header>
            <h1>Menú Principal</h1>
            <img src="{{ asset('imagenes/ucsc2.png') }}" alt="Logo UCSC">
        </header>

        <main class="menu-container">
<h2 class="relative inline-block text-2xl md:text-3xl font-semibold tracking-tight text-gray-900">
  <span class="bg-gradient-to-r from-[#E60026] to-[#0056A8] bg-clip-text text-transparent">Sistema de Gestión de Habilitaciones HabilProf</span>
  <span class="absolute left-0 -bottom-1 h-[3px] w-full rounded-full bg-gradient-to-r from-[#E60026] to-[#0056A8]"></span>
</h2>
            <p class="intro">Seleccione la operación que desea realizar:</p>

            <a href="{{ route('habilitaciones.create') }}" class="menu-item">
                <h3>1. Ingresar Nueva Habilitación</h3>
                <p>Registrar una nueva Habilitación Profesional para un alumno.</p>
            </a>

            <a href="{{ route('habilitaciones.index') }}" class="menu-item">
                <h3>2. Actualizar o Eliminar Habilitación</h3>
                <p>Buscar una habilitación existente para modificar sus datos o eliminar.</p>
            </a>

            <a href="{{ route('listados') }}" class="menu-item">
                <h3>3. Generar Listado de Habilitaciones</h3>
                <p>Generar un reporte de todas las habilitaciones, filtrado por semestre o historial completo.</p>
            </a>

        </main>
    </div>

</x-app-layout>
