<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Verifica si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */

/**
 * Obtiene las reglas de validación que se aplican a la solicitud.
 *
 * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
 */

//5.1.1.2 Reglas de validación para el login.
public function rules(): array
{
    return [
        // Requisito 'correo_user': 'X@ucsc.cl' o 'X@ing.ucsc.cl', X=4-30 chars.
        'email' => [
            'required',
            'string',
            'email',
            'min:12',
            'max:42',
            'regex:/^[a-z]{4,30}@(ucsc|ing\.ucsc)\.cl$/'
        ],

        // Requisito 'clave_user': 8-64 chars.
        'password' => [
            'required',
            'string',
            'min:8',
            'max:64'
        ],
    ];
}

    /**
     * Intenta autenticar al usuario.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Se asegura de que la solicitud no esté limitada por la tasa.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Obtiene la clave de limitación de tasa para la solicitud.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
