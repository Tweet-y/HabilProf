<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHabilitacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
            'semestre_inicio' => 'required|string|regex:/^(202[5-9]|20[3-4][0-9]|2050)-[1-2]$/',
            'titulo' => 'required|string|max:80|min:6|regex:/^[a-zA-Z0-9\s.,;:\'\"&-_()]+$/',
            'descripcion' => 'required|string|max:500|min:30',

            // PrIng/PrInv Rules
            'seleccion_guia_rut' => 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor',
            'seleccion_comision_rut' => 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor',
            'seleccion_co_guia_rut' => 'nullable|exists:profesor,rut_profesor',

            // PrTut Rules
            'nombre_empresa' => 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z0-9\s]+$/u',
            'nombre_supervisor' => 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u',
            'seleccion_tutor_rut' => 'required_if:tipo_habilitacion,PrTut|nullable|exists:profesor,rut_profesor',
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            '*.required' => 'Este campo es obligatorio.',
            '*.required_if' => 'Este campo es obligatorio para la modalidad seleccionada.',
            '*.exists' => 'El valor seleccionado no es válido o no existe.',

            // Mensajes para duplicados
            'seleccion_comision_rut.different' => 'El Profesor de Comisión no puede ser el mismo que el Guía.',
            'seleccion_co_guia_rut.different' => 'El Co-Guía no puede ser el mismo que el Guía o el de Comisión.',
        ];
    }
}
