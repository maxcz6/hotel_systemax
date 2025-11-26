<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DniPeruano implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // DNI peruano debe tener exactamente 8 dígitos numéricos
        if (!preg_match('/^\d{8}$/', $value)) {
            $fail('El :attribute debe ser un DNI válido de 8 dígitos.');
        }
    }
}
