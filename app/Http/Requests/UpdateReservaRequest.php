<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservaRequest extends FormRequest
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
            'cliente_id' => 'required|exists:clientes,id',
            'habitacion_id' => 'required|exists:habitaciones,id',
            'fecha_entrada' => 'required|date',
            'fecha_salida' => 'required|date|after:fecha_entrada',
            'estado' => 'required|string|in:confirmada,pendiente,cancelada,checkin,checkout',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'notas' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Normalizar check-in y check-out a checkin y checkout (sin guión)
        if ($this->has('estado')) {
            $estado = str_replace('-', '', $this->input('estado'));
            $this->merge(['estado' => $estado]);
        }
    }
}
