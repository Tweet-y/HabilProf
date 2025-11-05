<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F8F9FA; /* Mismo fondo gris de tus otras páginas */
        }
        .ucsc-container {
            max-width: 500px; /* Un poco más angosto para formularios */
            margin: 50px auto;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #CED4DA;
            overflow: hidden;
        }
        .ucsc-header {
            padding: 20px 30px;
            background-color: #FFFFFF;
            border-bottom: 1px solid #CED4DA;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ucsc-header h1 {
            margin: 0;
            color: #222222;
            font-size: 1.8em;
        }
        .ucsc-header img {
            max-height: 50px;
        }
        /* Ajuste para el botón principal de Tailwind */
        .ucsc-btn {
            background-color: #0056A8 !important; /* Azul UCSC */
        }
        .ucsc-btn:hover {
            background-color: #004180 !important; /* Azul más oscuro */
        }
    </style>
</head>
    <body class="font-sans text-gray-900 antialiased" style="background-color: #F8F9FA;">
    <div class="ucsc-container">
        <header class="ucsc-header">
            <h1>HabilProf</h1>
            <a href="/"><img src="{{ asset('imagenes/ucsc2.png') }}" alt="Logo UCSC"></a>
        </header>

        <div class="w-full px-6 py-4 overflow-hidden">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
