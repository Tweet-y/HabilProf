<?php
use App\Http\Controllers\HabilitacionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Menu principal
Route::get('/menu', function () {
    return view('menu');
});

// Ingreso de Habilitaciones Profesionales
Route::get('/ingreso', function () {
    return view('ingreso');
});

// Modifcar o Eliminar Habilitaciones Profesionales

Route::get('/actualizar_eliminar', [HabilitacionController::class, 'index'])->name('habilitaciones.index');
Route::get('/actualizar_eliminar/{alumno}/edit', [HabilitacionController::class, 'edit'])->name('habilitaciones.edit');
Route::put('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'update'])->name('habilitaciones.update');
Route::delete('/actualizar_eliminar/{alumno}', [HabilitacionController::class, 'destroy'])->name('habilitaciones.destroy');

// Generar Listados de Habilitaciones Profesionales
Route::get('/listados', function () {
    return view('listados');
});

// Dashboard (post-login)
Route::get('/dashboard', function () {
    return view('dashboard');
    
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

