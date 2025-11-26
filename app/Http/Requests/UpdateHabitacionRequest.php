<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHabitacionRequest extends FormRequest
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
        $habitacionId = $this->route('habitacion');

        return [
            'numero' => [
                'required',
                'string',
                'max:255',
                Rule::unique('habitaciones')->ignore($habitacionId),
            ],
            'tipo_habitacion_id' => 'required|exists:tipo_habitaciones,id',
            'precio_por_noche' => 'required|numeric|min:0',
            'estado' => 'required|string|in:disponible,ocupada,limpieza,mantenimiento',
        ];
    }
}
