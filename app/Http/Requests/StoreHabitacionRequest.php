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
            'numero' => 'required|string|max:255|unique:habitaciones',
            'tipo_habitacion_id' => 'required|string', // Permitir "nuevo" también
            'precio_por_noche' => 'required|numeric|min:0',
            'estado' => 'required|string|in:disponible,ocupada,limpieza,mantenimiento',
            'piso' => 'nullable|integer|min:1',
            'descripcion' => 'nullable|string',
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
}
