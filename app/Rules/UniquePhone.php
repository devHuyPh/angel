<?php

namespace App\Rules;

use Botble\Ecommerce\Models\Customer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniquePhone implements ValidationRule
{
    protected $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $normalizedPhone = $this->normalizePhone($value);

        $query = Customer::where('phone', $normalizedPhone);

        if ($this->ignoreId) {
            $query->where('id', '<>', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail(__('Số điện thoại đã tồn tại.'));
        }
    }

    protected function normalizePhone($phone)
    {
        $phone = trim($phone);
        if (str_starts_with($phone, '0')) {
            return '+84' . substr($phone, 1);
        }
        return $phone;
    }
}
