<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreReservaRequest extends FormRequest
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
            'cliente_id' => [
                'required',
                'exists:clientes,id'
            ],
            'habitacion_id' => [
                'required',
                'exists:habitaciones,id'
            ],
            'fecha_entrada' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'fecha_salida' => [
                'required',
                'date',
                'after:fecha_entrada'
            ],
            'estado' => [
                'required',
                'string',
                'in:confirmada,pendiente,cancelada,checkin,checkout'
            ],
            'notas' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'descuento' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100' // Descuento porcentual máximo 100%
            ],
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

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validar que la fecha de salida no sea más de 1 año después de la entrada
            if ($this->fecha_entrada && $this->fecha_salida) {
                $entrada = Carbon::parse($this->fecha_entrada);
                $salida = Carbon::parse($this->fecha_salida);
                
                $diffInDays = $entrada->diffInDays($salida);
                
                // Máximo 365 días (1 año)
                if ($diffInDays > 365) {
                    $validator->errors()->add('fecha_salida', 'La reserva no puede exceder 365 días.');
                }
                
                // Mínimo 1 día
                if ($diffInDays < 1) {
                    $validator->errors()->add('fecha_salida', 'La reserva debe ser de al menos 1 día.');
                }
            }
            
            // Validar disponibilidad de la habitación en las fechas seleccionadas
            if ($this->habitacion_id && $this->fecha_entrada && $this->fecha_salida) {
                $reservasConflictivas = \App\Models\Reserva::where('habitacion_id', $this->habitacion_id)
                    ->where(function ($query) {
                        $query->whereBetween('fecha_entrada', [$this->fecha_entrada, $this->fecha_salida])
                            ->orWhereBetween('fecha_salida', [$this->fecha_entrada, $this->fecha_salida])
                            ->orWhere(function ($q) {
                                $q->where('fecha_entrada', '<=', $this->fecha_entrada)
                                  ->where('fecha_salida', '>=', $this->fecha_salida);
                            });
                    })
                    ->whereNotIn('estado', ['cancelada'])
                    ->exists();
                
                if ($reservasConflictivas) {
                    $validator->errors()->add('habitacion_id', 'La habitación no está disponible en las fechas seleccionadas.');
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
            'cliente_id.required' => 'Debe seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'habitacion_id.required' => 'Debe seleccionar una habitación.',
            'habitacion_id.exists' => 'La habitación seleccionada no existe.',
            'fecha_entrada.required' => 'La fecha de entrada es obligatoria.',
            'fecha_entrada.date' => 'La fecha de entrada debe ser una fecha válida.',
            'fecha_entrada.after_or_equal' => 'La fecha de entrada no puede ser anterior a hoy.',
            'fecha_salida.required' => 'La fecha de salida es obligatoria.',
            'fecha_salida.date' => 'La fecha de salida debe ser una fecha válida.',
            'fecha_salida.after' => 'La fecha de salida debe ser posterior a la fecha de entrada.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'descuento.min' => 'El descuento no puede ser negativo.',
            'descuento.max' => 'El descuento no puede exceder el :max%.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'habitacion_id' => 'habitación',
            'fecha_entrada' => 'fecha de entrada',
            'fecha_salida' => 'fecha de salida',
            'estado' => 'estado',
            'notas' => 'notas',
            'descuento' => 'descuento',
        ];
    }
}
