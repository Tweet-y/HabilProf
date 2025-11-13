<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHabilitacionRequest extends FormRequest
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
            'selector_alumno_rut' => 'required|exists:alumno,rut_alumno',
            'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
            'semestre_inicio' => 'required|string',
            'titulo' => 'required|string|max:50|min:6|regex:/^[a-zA-Z0-9\s.,;:\'"&-_()]+$/',
            'descripcion' => 'required|string|max:500|min:30',
        ];

        if ($this->tipo_habilitacion === 'PrIng' || $this->tipo_habilitacion === 'PrInv') {
            $rules['seleccion_guia_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor';
            $rules['seleccion_co_guia_rut'] = 'nullable|exists:profesor,rut_profesor';
            $rules['seleccion_comision_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor';
        } elseif ($this->tipo_habilitacion === 'PrTut') {
            $rules['nombre_empresa'] = 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z0-9\s]+$/u';
            $rules['nombre_supervisor'] = 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u';
            $rules['seleccion_tutor_rut'] = 'required_if:tipo_habilitacion,PrTut|nullable|exists:profesor,rut_profesor';
        }

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
            '*.required' => 'El campo es obligatorio.',
            '*.required_if' => 'Este campo es obligatorio para la modalidad seleccionada.',
            '*.exists' => 'El valor seleccionado no es válido.',

            // Mensajes específicos para profesores
            'seleccion_guia_rut.required_if' => 'Debe seleccionar un Profesor Guía.',
            'seleccion_comision_rut.required_if' => 'Debe seleccionar un Profesor Comisión.',
            'seleccion_tutor_rut.required_if' => 'Debe seleccionar un Profesor Tutor.',

            // Mensajes para duplicados
            '*.different' => 'Un profesor no puede tener múltiples roles (Guía, Co-Guía, Comisión).',
        ];
    }
}
