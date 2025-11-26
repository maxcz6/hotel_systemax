<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHabitacionRequest extends FormRequest
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
            'numero' => [
                'required',
                'string',
                'max:10',
                'unique:habitaciones,numero',
                'regex:/^[A-Z0-9\-]+$/' // Solo letras mayúsculas, números y guiones
            ],
            'tipo_habitacion_id' => 'required|string', // Permitir string number" también
            'precio_por_noche' => [
                'required',
                'numeric',
                'min:10', // Precio mínimo razonable
                'max:99999.99', // Precio máximo razonable
                'regex:/^\d+(\.\d{1,2})?$/' // Máximo 2 decimales
            ],
            'estado' => [
                'required',
                'string',
                'in:disponible,ocupada,limpieza,mantenimiento'
            ],
            'piso' => [
                'nullable',
                'integer',
                'min:1',
                'max:50' // Máximo de pisos razonable
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Si no es "nuevo", debe ser un ID válido de tipo_habitacion
            if ($this->tipo_habitacion_id !== 'nuevo') {
                if (!is_numeric($this->tipo_habitacion_id) || 
                    !\App\Models\TipoHabitacion::where('id', $this->tipo_habitacion_id)->exists()) {
                    $validator->errors()->add('tipo_habitacion_id', 'El tipo de habitación seleccionado no es válido.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'numero.required' => 'El número de habitación es obligatorio.',
            'numero.unique' => 'Este número de habitación ya está registrado.',
            'numero.regex' => 'El número de habitación solo puede contener letras mayúsculas, números y guiones.',
            'tipo_habitacion_id.required' => 'El tipo de habitación es obligatorio type.',
            'precio_por_noche.required' => 'El precio por noche es obligatorio.',
            'precio_por_noche.min' => 'El precio por noche debe ser al menos S/ :min.',
            'precio_por_noche.max' => 'El precio por noche no puede exceder S/ :max.',
            'precio_por_noche.regex' => 'El precio por noche solo puede tener hasta 2 decimales.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'piso.min' => 'El piso debe ser al menos :min.',
            'piso.max' => 'El piso no puede exceder :max.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'numero' => 'número de habitación',
            'tipo_habitacion_id' => 'tipo de habitación',
            'precio_por_noche' => 'precio por noche',
            'estado' => 'estado',
            'piso' => 'piso',
            'descripcion' => 'descripción',
        ];
    }
}
