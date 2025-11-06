<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Gestión de Habilitaciones</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ucsc-red: #E60026;
            --ucsc-blue: #0056A8;
            --dark-blue: #004180;
            --gray-bg: #F0F2F5;
            --white: #FFFFFF;
            --text-dark: #333333;
            --text-light-gray: #777777;
            --border-color: #DDDDDD;
            --shadow-light: 0 6px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            
            background-color: var(--gray-bg);
            position: relative; 
        }

        body::before {
            content: '';
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -1; 

            /* Imagen de fondo */
            background-image: url("{{ asset('imagenes/fondo_ucsc.jpg') }}");
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            
            filter: blur(3px);
            -webkit-filter: blur(3px);

            transform: scale(1.05);
        }

        .container {
            position: relative; 
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            /* Efecto vidrio esmerilado */
            background-color: rgba(255, 255, 255, 0.85); 
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            
            border-radius: 12px;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
            overflow: hidden;
            z-index: 1; 

            animation: fadeInScale 0.6s ease-out forwards;
        }

        /* Animación de aparición */
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /*Header*/
        header {
            padding: 25px 35px;
            background-color: var(--white);
            border-bottom: 1px solid var(--border-color);
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
        }

        header img {
            max-height: 50px;
            width: auto;
        }
        
        /* Logo izquierdo */
        header img:first-of-type {
            justify-self: start;
        }

        /* Logo derecho */
        header img:last-of-type {
            justify-self: end;
        }

        header h1 {
            margin: 0;
            color: var(--text-dark);
            font-size: 2.2em;
            font-weight: 700;
            justify-self: center; 
        }

        /* --- Contenido Principal --- */
        .menu-container {
            padding: 40px 50px;
            text-align: center;
        }

        .menu-container h2 {
            color: var(--ucsc-red); 
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.8em;
            font-weight: 500;
        }
        .menu-container p.intro {
            font-size: 1.15em;
            color: var(--text-light-gray);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 25px;
            margin-top: 35px;
        }
        .button-link {
            padding: 14px 28px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.05em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            text-decoration: none;
            display: inline-block;
            background-color: var(--ucsc-blue);
            color: var(--white);
            box-shadow: 0 4px 10px rgba(0, 86, 168, 0.2);
        }
        .button-link:hover {
            background-color: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 86, 168, 0.3);
        }
        .button-link.logout-button {
            background-color: #6C757D;
            box-shadow: 0 4px 10px rgba(108, 117, 125, 0.2);
        }
        .button-link.logout-button:hover {
            background-color: #5A6268;
            box-shadow: 0 6px 15px rgba(108, 117, 125, 0.3);
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <img src="{{ asset('imagenes/ucsc2.png') }}" alt="Logo UCSC">
            <h1>¡Bienvenido!</h1>
            <img src="{{ asset('imagenes/hprof.png') }}" alt="Logo HabilProf" height="35">
        </header>

        <main class="menu-container">
            
            <h2>Sistema de Gestión de Habilitaciones HabilProf</h2>
            <p class="intro">
                Bienvenido al sistema HabilProf, su plataforma para la gestión
                de habilitaciones profesionales, incluyendo Proyectos de Título y Prácticas Profesionales.
            </p>

            <div class="button-container">
                @auth
                    <a href="{{ route('dashboard') }}" class="button-link">Ir al Menú</a>

                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <a href="{{ route('logout') }}"
                           class="button-link logout-button"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            Cerrar Sesión
                        </a>
                    </form>

                @else
                    <a href="{{ route('login') }}" class="button-link">Iniciar Sesión</a>
                @endauth
            </div>
        </main>
    </div>

</body>
</html>