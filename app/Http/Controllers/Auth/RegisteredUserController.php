<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
// Dentro del método store() en RegisteredUserController.php

$request->validate([
    // Requisito 'nombre_user': Alfabético, max:50, sin números/símbolos.
    // El campo en Breeze se llama 'name'.
    'name' => [
        'required', 
        'string', 
        'max:50', 
        'regex:/^[a-zA-Z\s]+$/' // Solo letras y espacios
    ],

    'email' => [
        'required',
        'string',
        'email',
        'min:12',  // 'a@ucsc.cl' (4) + @ (1) + ucsc.cl (7) = 12
        'max:42',  
        'unique:users', // Requisito: "no aceptará que correo_user esté duplicado"
        'regex:/^[a-z]{4,30}@ucsc\.cl$/' // Valida formato X@ucsc.cl
    ],

    // Requisito 'clave_user' y 'confirmar_clave_user': 8-64 chars y coincidir.
    // El campo en Breeze se llama 'password'.
    'password' => [
        'required',
        'confirmed', // Requisito: "verificar que la clave_user y confirma_clave_user son idénticas"
        Rules\Password::min(8)->max(64) // Valida min 8 y max 64
    ],
]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
