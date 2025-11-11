<?php
use App\Http\Controllers\HabilitacionController;
use App\Http\Controllers\ListadoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página de bienvenida (Pública)
Route::get('/', function () {
    return view('welcome');
});

// Login y autenticación (Públicas)
require __DIR__.'/auth.php';

// Verificación CSRF para todas las rutas POST
Route::middleware(['web'])->group(function () {
    // Aquí van las rutas que necesitan CSRF
});

// --- INICIO DE LA ZONA SEGURA (SOLO USUARIOS LOGUEADOS) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 1. Dashboard (Tu menú principal)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // 2. Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. Ingreso de Habilitaciones (PROTEGIDO)
    Route::get('/habilitaciones/ingreso', [HabilitacionController::class, 'create'])->name('habilitaciones.create');
    Route::post('/habilitaciones/ingreso', [HabilitacionController::class, 'store'])->name('habilitaciones.store');
    Route::post('/habilitaciones/check-limit', [HabilitacionController::class, 'checkLimit'])->name('habilitaciones.checkLimit');

    // 4. Modifcar o Eliminar Habilitaciones (PROTEGIDO)
    Route::get('/actualizar_eliminar', [HabilitacionController::class, 'index'])->name('habilitaciones.index');
    Route::get('/actualizar_eliminar/{alumno}/edit', [HabilitacionController::class, 'edit'])->name('habilitaciones.edit');
    Route::put('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'update'])->name('habilitaciones.update');
    Route::delete('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'destroy'])->name('habilitaciones.destroy');

    // 5. Generar Listados (PROTEGIDO)
    Route::get('/listados', [ListadoController::class, 'index'])->name('listados');
    Route::any('/listados/generar', [ListadoController::class, 'generar'])->name('listados.generar');

});


