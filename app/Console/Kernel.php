<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            // Se usa app() para resolver la instancia del servicio desde el contenedor de Laravel
            $cargaService = app(CargaUCSCService::class); 
            $cargaService->activarCargaPeriodica();
            
            // Opcional: Registrar un mensaje en los logs de Laravel para verificar la ejecución
            // \Log::info('Carga UCSC ejecutada a las ' . now());
            
        })->everyMinute(); // <-- Frecuencia de ejecución requerida
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
