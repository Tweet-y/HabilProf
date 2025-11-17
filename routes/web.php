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
    // Ruta para mostrar formulario de creación de nuevas habilitaciones
    Route::get('/ingreso', [HabilitacionController::class, 'create'])->name('habilitaciones.create');
    // Ruta para procesar y almacenar nueva habilitación en base de datos
    Route::post('/ingreso', [HabilitacionController::class, 'store'])->name('habilitaciones.store');
    // Ruta AJAX para verificar límites de profesores antes de guardar
    Route::post('/habilitaciones/check-limit', [HabilitacionController::class, 'checkLimit'])->name('habilitaciones.checkLimit');

    // 4. Modificar o Eliminar Habilitaciones (PROTEGIDO)
    // Ruta principal para buscar y seleccionar habilitación a modificar/eliminar
    Route::get('/actualizar_eliminar', [HabilitacionController::class, 'index'])->name('habilitaciones.index');
    // Ruta para mostrar formulario de edición de habilitación específica
    Route::get('/actualizar_eliminar/{alumno}/edit', [HabilitacionController::class, 'edit'])->name('habilitaciones.edit');
    // Ruta para actualizar habilitación existente
    Route::put('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'update'])->name('habilitaciones.update');
    // Ruta para eliminar habilitación y registros relacionados
    Route::delete('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'destroy'])->name('habilitaciones.destroy');

    // 5. Generar Listados (PROTEGIDO)
    Route::get('/listados', [ListadoController::class, 'index'])->name('listados');
    Route::any('/listados/generar', [ListadoController::class, 'generar'])->name('listados.generar');

});


