<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelefonoPeruano implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Teléfono peruano: 9 dígitos que empiezan con 9
        // También acepta formato con código de país +51 o 51
        $value = preg_replace('/[^0-9]/', '', $value); // Eliminar caracteres no numéricos
        
        // Si empieza con 51, quitarlo (código de país)
        if (str_starts_with($value, '51') && strlen($value) == 11) {
            $value = substr($value, 2);
        }
        
        if (!preg_match('/^9\d{8}$/', $value)) {
            $fail('El :attribute debe ser un número de celular válido de 9 dígitos que comience con 9.');
        }
    }
}
