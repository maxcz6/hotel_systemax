<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoHabitacionRequest extends FormRequest
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
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'unique:tipo_habitaciones'
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'capacidad' => [
                'nullable',
                'integer',
                'min:1',
                'max:20'
            ],
            'precio_por_noche' => [
                'required',
                'numeric',
                'min:10',
                'max:99999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique' => 'Ya existe un tipo de habitación con este nombre.',
            'capacidad.min' => 'La capacidad debe ser al menos :min persona.',
            'capacidad.max' => 'La capacidad no puede exceder :max personas.',
            'precio_por_noche.required' => 'El precio por noche es obligatorio.',
            'precio_por_noche.min' => 'El precio por noche debe ser al menos S/ :min.',
            'precio_por_noche.max' => 'El precio por noche no puede exceder S/ :max.',
            'precio_por_noche.regex' => 'El precio por noche solo puede tener hasta 2 decimales.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'capacidad' => 'capacidad',
            'precio_por_noche' => 'precio por noche',
        ];
    }
}
