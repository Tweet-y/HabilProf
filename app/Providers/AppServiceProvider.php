<?php

namespace App\Providers;
use App\CargaUCSCService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
    {
        // 1. Instanciar el servicio CargaUCSCService usando el contenedor 'app()'.
        // $cargaService = app(CargaUCSCService::class); 

        // 2. Ejecutar la función de carga de datos.
        // NOTA: Esto se ejecutará una vez cada vez que se inicie un nuevo proceso HTTP (una solicitud web).
        // $cargaService->activarCargaPeriodica();
    }
}
