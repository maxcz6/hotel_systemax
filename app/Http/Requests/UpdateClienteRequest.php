<?php

namespace App\Http\Requests;

use App\Rules\DniPeruano;
use App\Rules\TelefonoPeruano;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
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
        $clienteId = $this->route('cliente');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/'
            ],
            'apellido' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/'
            ],
            'dni' => [
                'nullable',
                'string',
                new DniPeruano(),
                Rule::unique('clientes')->ignore($clienteId),
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('clientes')->ignore($clienteId),
            ],
            'telefono' => [
                'nullable',
                'string',
                new TelefonoPeruano()
            ],
            'direccion' => [
                'nullable',
                'string',
                'max:500',
                'min:5'
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
            'nombre.min' => 'El nombre debe tener al menos :min caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellido.required' => 'El apellido es obligatorio.',
            'apellido.min' => 'El apellido debe tener al menos :min caracteres.',
            'apellido.regex' => 'El apellido solo puede contener letras y espacios.',
            'dni.unique' => 'Este DNI ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'direccion.min' => 'La dirección debe tener al menos :min caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'apellido' => 'apellido',
            'dni' => 'DNI',
            'email' => 'correo electrónico',
            'telefono' => 'teléfono',
            'direccion' => 'dirección',
        ];
    }
}
