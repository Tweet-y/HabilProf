<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CargaUCSCService;

class SimulacionCarga extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulacion:sync';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza la carga y sincronización periódica de datos';

    /**
     * Execute the console command.
     */


public function handle(CargaUCSCService $service)
{
    $this->info('Iniciando carga UCSC...');

    // 1. Ejecutar el servicio de carga (ya inyectado)
    $resultados = $service->activarCargaPeriodica();

    // 2. Opcional: Mostrar un resumen de los resultados
    $this->table(
        ['RUT / Status', 'Mensaje'],
        collect($resultados)->map(function ($r) {
            return [$r['rut'] ?? $r['status'], $r['mensaje'] ?? ($r['status'] . ' con nota: ' . ($r['nota_aplicada'] ?? 'N/A'))];
        })
    );
        
    $this->info('Carga UCSC finalizada.');
}
}

