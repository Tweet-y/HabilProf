<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request para validar creación de habilitaciones.
 * Incluye reglas condicionales según tipo de habilitación.
 */
class StoreHabilitacionRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Todos los usuarios autenticados pueden crear
    }

    /**
     * Reglas de validación para la solicitud.
     */
    public function rules(): array
    {
        $rules = [
            // Datos básicos obligatorios
            'selector_alumno_rut' => 'required|exists:alumno,rut_alumno',
            'tipo_habilitacion' => 'required|in:PrIng,PrInv,PrTut',
            'semestre_inicio' => 'required|string',
            'titulo' => 'required|string|max:50|min:6|regex:/^[a-zA-Z0-9\s.,;:\'"&-_()]+$/',
            'descripcion' => 'required|string|max:500|min:30',
        ];

        // Reglas específicas para PrIng/PrInv (Proyectos)
        if ($this->tipo_habilitacion === 'PrIng' || $this->tipo_habilitacion === 'PrInv') {
            $rules['seleccion_guia_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor';
            $rules['seleccion_co_guia_rut'] = 'nullable|exists:profesor,rut_profesor'; // Opcional
            $rules['seleccion_comision_rut'] = 'required_if:tipo_habilitacion,PrIng,PrInv|nullable|exists:profesor,rut_profesor';

        // Reglas específicas para PrTut (Práctica Tutelada)
        } elseif ($this->tipo_habilitacion === 'PrTut') {
            $rules['nombre_empresa'] = 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z0-9\s]+$/u';
            $rules['nombre_supervisor'] = 'required_if:tipo_habilitacion,PrTut|nullable|string|max:50|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u';
            $rules['seleccion_tutor_rut'] = 'required_if:tipo_habilitacion,PrTut|nullable|exists:profesor,rut_profesor';
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
            '*.required' => 'El campo es obligatorio.',
            '*.required_if' => 'Este campo es obligatorio para la modalidad seleccionada.',
            '*.exists' => 'El valor seleccionado no es válido.',
            '*.string' => 'El campo debe ser texto.',
            '*.max' => 'El campo no puede tener más de :max caracteres.',
            '*.min' => 'El campo debe tener al menos :min caracteres.',
            '*.regex' => 'El formato del campo no es válido.',

            // Mensajes específicos para profesores
            'seleccion_guia_rut.required_if' => 'Debe seleccionar un Profesor Guía.',
            'seleccion_comision_rut.required_if' => 'Debe seleccionar un Profesor Comisión.',
            'seleccion_tutor_rut.required_if' => 'Debe seleccionar un Profesor Tutor.',

            // Mensajes para títulos y descripciones
            'titulo.regex' => 'El título solo puede contener letras, números y símbolos: . , ; : \' " - _ ( )',
            'descripcion.regex' => 'La descripción solo puede contener letras, números y símbolos: . , ; : \' " - _ ( )',
            'nombre_empresa.regex' => 'El nombre de empresa solo puede contener letras y números.',
            'nombre_supervisor.regex' => 'El nombre del supervisor solo puede contener letras y espacios.',

            // Mensajes para tipos
            'tipo_habilitacion.in' => 'El tipo de habilitación debe ser PrIng, PrInv o PrTut.',
        ];
    }
}
