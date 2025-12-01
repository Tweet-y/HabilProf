<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
// 5.6 El sistema debe verificar que el usuario tiene acceso al email ingresado en el formulario “Registro de nueva cuenta”, 
// mediante el uso de un código de verificación enviado a dicho correo, antes de habilitar el inicio de sesión de la cuenta.

class VerificationController extends Controller
{
    /**
     * Mostrar la pantalla de verificación
     */
    public function show(Request $request): View
    {
        $email = $request->query('email');
        return view('auth.verify', compact('email'));
    }

    /**
     * Verificar el código de verificación
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('verification_code', $request->verification_code)
                    ->first();

        if ($user) {
            $user->update([
                'is_verified' => true,
                'verification_code' => null, // Limpiar el código después de verificar
            ]);

            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Cuenta verificada exitosamente. Ya puede iniciar sesión en HabilProf.');
        } else {
            return back()->withErrors(['verification_code' => 'Código de verificación incorrecto. Intente nuevamente.']);
        }
    }
}
