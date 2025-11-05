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
        // Tu bloque de validación existente
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
                'confirmed', //verificar que la clave_user y confirma_clave_user son idénticas
                Rules\Password::min(8)->max(64) // Valida min 8 y max 64
            ],
        ], 
        // --- INICIO: ARRAY DE MENSAJES PERSONALIZADOS ---
        // Este es el bloque que debes añadir (la coma de arriba es importante)
        [
            // Mensajes para el campo 'name' (nombre_user)
            'name.required' => 'El campo nombre es obligatorio.',
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'name.max' => 'El nombre no puede tener más de 50 caracteres.',

            // Mensajes para el campo 'email' (correo_user)
            'email.required' => 'El campo correo es obligatorio.',
            'email.email' => 'El correo debe ser una dirección de email válida.',
            'email.unique' => 'Este correo ya está en uso.', // Requisito 5.4
            'email.regex' => 'El correo debe tener el formato usuario@ucsc.cl (4-30 caracteres).',
            'email.min' => 'El correo debe tener al menos 12 caracteres.',
            'email.max' => 'El correo no puede tener más de 42 caracteres.',

            // Mensajes para el campo 'password' (clave_user)
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede tener más de 64 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.', 
        ]
        );

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