<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar actualización de habilitaciones.
 * Similar a Store pero permite título más largo y cambios de tipo.
 */
class UpdateHabilitacionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Todos los usuarios autenticados pueden actualizar
    }

    /**
     * Prepara los datos para validación.
     * Convierte los RUT de string a integer para compatibilidad con la base de datos.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'seleccion_guia_rut' => $this->input('seleccion_guia_rut') ? (int) $this->input('seleccion_guia_rut') : null,
            'seleccion_co_guia_rut' => $this->input('seleccion_co_guia_rut') ? (int) $this->input('seleccion_co_guia_rut') : null,
            'seleccion_comision_rut' => $this->input('seleccion_comision_rut') ? (int) $this->input('seleccion_comision_rut') : null,
            'seleccion_tutor_rut' => $this->input('seleccion_tutor_rut') ? (int) $this->input('seleccion_tutor_rut') : null,
        ]);
    }

    /**
     * Reglas de validación para la solicitud de actualización.
     */
    public function rules(): array
    {
        $rules = [
            // Datos básicos obligatorios
            'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
            'semestre_inicio' => 'required|string',
            'titulo' => 'required|string|max:80|min:6|regex:/^[a-zA-Z0-9\s.,;:\'\"\&\-_()áéíóúñÁÉÍÓÚ]+$/u',
            'descripcion' => 'required|string|max:500|min:30|regex:/^[a-zA-Z0-9\s.,;:\'\"\&\-_()áéíóúñÁÉÍÓÚ]+$/u',
        ];

        // Reglas específicas para PrIng/PrInv (Proyectos)
        if ($this->tipo_habilitacion === 'PrIng' || $this->tipo_habilitacion === 'PrInv') {
            $rules['seleccion_guia_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|integer|exists:profesor,rut_profesor';
            $rules['seleccion_co_guia_rut'] = 'nullable|integer|exists:profesor,rut_profesor';
            $rules['seleccion_comision_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|integer|exists:profesor,rut_profesor';

        // Reglas específicas para PrTut (Práctica Tutelada)
        } elseif ($this->tipo_habilitacion === 'PrTut') {
            $rules['nombre_empresa'] = 'required_if:tipo_habilitacion,PrTut|string|max:50|regex:/^[a-zA-Z0-9\sñÑáéíóúÁÉÍÓÚ]+$/u';
            $rules['nombre_supervisor'] = 'required_if:tipo_habilitacion,PrTut|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u';
            $rules['seleccion_tutor_rut'] = 'required_if:tipo_habilitacion,PrTut|integer|exists:profesor,rut_profesor';
        }

        return $rules;
    }

    /**
    * Mensajes de error personalizados para las reglas de validación.
    */
    public function messages(): array
    {
        return [
            // Mensajes generales
            '*.required' => 'Este campo es obligatorio.',
            '*.required_if' => 'Este campo es obligatorio para la modalidad seleccionada.',
            '*.exists' => 'El valor seleccionado no es válido o no existe.',
            '*.string' => 'El campo debe ser texto.',
            '*.max' => 'El campo no puede tener más de :max caracteres.',
            '*.min' => 'El campo debe tener al menos :min caracteres.',
            '*.regex' => 'El formato del campo no es válido.',

            // Mensajes específicos para profesores
            'seleccion_guia_rut.required_if' => 'Debe seleccionar un Profesor Guía.',
            'seleccion_comision_rut.required_if' => 'Debe seleccionar un Profesor Comisión.',
            'seleccion_tutor_rut.required_if' => 'Debe seleccionar un Profesor Tutor.',

            // Mensajes para campos de ingreso manual
            'titulo.regex' => 'El título solo puede contener letras, números y símbolos: . , ; : \' " - _ ( )',
            'descripcion.regex' => 'La descripción solo puede contener letras, números y símbolos: . , ; : \' " - _ ( )',
            'nombre_empresa.regex' => 'El nombre de empresa solo puede contener letras y números.',
            'nombre_supervisor.regex' => 'El nombre del supervisor solo puede contener letras y espacios.',

            // Mensajes para tipos
            'tipo_habilitacion.in' => 'El tipo de habilitación debe ser PrIng, PrInv o PrTut.',
        ];
    }
}
