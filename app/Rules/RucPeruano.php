<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RucPeruano implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // RUC peruano debe tener exactamente 11 dígitos
        if (!preg_match('/^\d{11}$/', $value)) {
            $fail('El :attribute debe ser un RUC válido de 11 dígitos.');
            return;
        }
        
        // Validar que empiece con 10, 15, 16, 17 o 20 (tipos de RUC válidos)
        $prefijo = substr($value, 0, 2);
        $prefijosValidos = ['10', '15', '16', '17', '20'];
        
        if (!in_array($prefijo, $prefijosValidos)) {
            $fail('El :attribute no tiene un prefijo válido de RUC peruano.');
        }
    }
}
