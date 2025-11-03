<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CurrencyCode implements ValidationRule
{
    private array $allowed;

    public function __construct(array $allowed = ['TRY', 'USD', 'EUR', 'GBP'])
    {
        $this->allowed = array_map('strtoupper', $allowed);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        } // nullable destekle
        $val = strtoupper($value);
        if (! in_array($val, $this->allowed, true)) {
            $fail($attribute.' geçersiz. Desteklenen para birimleri: '.implode(', ', $this->allowed));
        }
    }
}
