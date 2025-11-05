<?php

require_once 'vendor/autoload.php';

use App\CargaUCSCService;

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(CargaUCSCService::class);
$result = $service->activarCargaPeriodica();

var_dump($result);
