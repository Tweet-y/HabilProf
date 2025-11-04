<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menú Principal - Sistema HabilProf') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="menu-container" style="text-align: center;">
                        <h2>Sistema de Gestión de Habilitaciones HabilProf</h2>
                        <p class="intro" style="font-size: 1.1em; color: #555555; margin-bottom: 30px;">Seleccione la operación que desea realizar:</p>

                        <div style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
                            <a href="{{ route('habilitaciones.create') }}" class="menu-item" style="display: block; background-color: #F8F9FA; border: 1px solid #CED4DA; border-radius: 6px; padding: 20px 25px; text-decoration: none; text-align: left; transition: all 0.2s ease-in-out; max-width: 600px; width: 100%;">
                                <h3 style="margin: 0 0 8px 0; color: #E60026; font-size: 1.3em;">1. Ingresar Nueva Habilitación</h3>
                                <p style="margin: 0; color: #333333; font-size: 0.95em; line-height: 1.5;">Registrar una nueva Habilitación Profesional (PrIng, PrInv o PrTut) para un alumno.</p>
                            </a>

                            <a href="{{ route('habilitaciones.index') }}" class="menu-item" style="display: block; background-color: #F8F9FA; border: 1px solid #CED4DA; border-radius: 6px; padding: 20px 25px; text-decoration: none; text-align: left; transition: all 0.2s ease-in-out; max-width: 600px; width: 100%;">
                                <h3 style="margin: 0 0 8px 0; color: #E60026; font-size: 1.3em;">2. Actualizar o Eliminar Habilitación</h3>
                                <p style="margin: 0; color: #333333; font-size: 0.95em; line-height: 1.5;">Buscar una habilitación existente por alumno para modificar sus datos o eliminar el registro.</p>
                            </a>

                            <a href="{{ route('listados') }}" class="menu-item" style="display: block; background-color: #F8F9FA; border: 1px solid #CED4DA; border-radius: 6px; padding: 20px 25px; text-decoration: none; text-align: left; transition: all 0.2s ease-in-out; max-width: 600px; width: 100%;">
                                <h3 style="margin: 0 0 8px 0; color: #E60026; font-size: 1.3em;">3. Generar Listado de Habilitaciones</h3>
                                <p style="margin: 0; color: #333333; font-size: 0.95em; line-height: 1.5;">Consultar y generar un reporte de todas las habilitaciones, filtrado por semestre o historial completo.</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .menu-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            border-color: #E60026;
        }
    </style>
</x-app-layout>
