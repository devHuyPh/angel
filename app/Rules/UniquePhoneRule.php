<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Botble\Ecommerce\Models\Customer;

class UniquePhoneRule implements Rule
{
    protected $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function passes($attribute, $value): bool
    {
        $normalized = $this->normalizePhone($value);

        return !Customer::query()
            ->whereRaw("REPLACE(REPLACE(phone, '+84', '0'), ' ', '') REGEXP ?", ['^0[0-9]{8,}$'])
            ->where(function ($query) use ($normalized) {
                $query->whereRaw("REPLACE(REPLACE(phone, '+84', '0'), ' ', '') = ?", [$normalized]);
            })
            ->when($this->ignoreId, fn($query) => $query->where('id', '!=', $this->ignoreId))
            ->exists();
    }

    public function message(): string
    {
        return 'Số điện thoại đã tồn tại.';
    }

    protected function normalizePhone(string $phone): string
    {
        // Chuyển +84xxx → 0xxx
        if (str_starts_with($phone, '+84')) {
            $phone = '0' . substr($phone, 3);
        }

        // Loại bỏ mọi ký tự không phải số
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
