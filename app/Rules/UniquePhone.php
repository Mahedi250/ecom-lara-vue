<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Phone numbers are stored as digits and must be unique across both common
 * formats — 01700123456 and 8801700123456 are the same number.
 */
class UniquePhone implements ValidationRule
{
    public function __construct(private readonly ?int $ignoreId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = (string) $value;

        $exists = fn (string $phone) => User::where('phone', $phone)
            ->when($this->ignoreId, fn ($query) => $query->where('id', '!=', $this->ignoreId))
            ->exists();

        if ($exists($value)) {
            $fail('This phone number is already registered.');

            return;
        }

        $alternate = null;
        if (strlen($value) === 13 && str_starts_with($value, '880')) {
            $alternate = '0'.substr($value, 3);
        } elseif (strlen($value) === 11 && str_starts_with($value, '0')) {
            $alternate = '880'.substr($value, 1);
        }

        if ($alternate && $exists($alternate)) {
            $fail('This phone number is already registered.');
        }
    }
}
