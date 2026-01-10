<?php

namespace App\Rules;

use Botble\Ecommerce\Models\Customer;
use Illuminate\Contracts\Validation\Rule;

class ValidReferralPhone implements Rule
{
    public function passes($attribute, $value): bool
    {
          if (!$value) {
            return false; // hoặc true nếu bạn cho phép bỏ trống
        }

        $formatted1 = $value;
        $formatted2 = $this->convertPhoneFormat($value);

        return Customer::query()
            ->where('phone', $formatted1)      // nhập đúng định dạng đang lưu
            ->orWhere('phone', $formatted2)    // nhập 0xxxx hoặc +84xxxx
            ->orWhere('uuid_code', $value)     // nhập UUID
            ->exists();
    }

    protected function convertPhoneFormat($phone): string
    {
        // Nếu bắt đầu bằng 0 -> chuyển sang +84
        if (str_starts_with($phone, '0')) {
            return '+84' . substr($phone, 1);
        }

        // Nếu bắt đầu bằng +84 -> chuyển sang 0
        if (str_starts_with($phone, '+84')) {
            return '0' . substr($phone, 3);
        }

        return $phone;
    }

    public function message(): string
    {
        return __('The referral code is invalid.');
    }
}
