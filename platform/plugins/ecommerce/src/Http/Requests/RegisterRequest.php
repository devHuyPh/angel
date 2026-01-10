<?php

namespace Botble\Ecommerce\Http\Requests;

use App\Rules\UniquePhone;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Rules\EmailRule;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Models\Customer;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;
use App\Rules\ValidReferralPhone;

class RegisterRequest extends Request
{

    protected function getPhoneLengthRulesByCountry(?string $country): array
    {
        return match (strtolower($country)) {
            'vn' => ['min:10', 'max:11'], // Việt Nam: 10 hoặc 11 số (tùy nhà mạng)
            'us' => ['min:10', 'max:10'], // Mỹ: luôn 10 số
            'cn' => ['min:11', 'max:11'], // Trung Quốc: luôn 11 số
            'jp' => ['min:10', 'max:11'], // Nhật Bản: 10 hoặc 11 số
            'kr' => ['min:9',  'max:11'], // Hàn Quốc: 9 đến 11 số
            'fr' => ['min:9',  'max:9'],  // Pháp: luôn 9 số
            'gb' => ['min:10', 'max:10'], // Anh: 10 số
            'de' => ['min:10', 'max:11'], // Đức: 10–11 số
            'in' => ['min:10', 'max:10'], // Ấn Độ: luôn 10 số
            'id' => ['min:9',  'max:12'], // Indonesia: 9–12 số
            'th' => ['min:9',  'max:9'],  // Thái Lan: 9 số
            'my' => ['min:9',  'max:10'],
            default => ['min:6', 'max:15'], // Mặc định: theo chuẩn E.164
        };
    }

    public function rules(): array
    {
        $country = $this->input('country');

        $rules = [
            'referral_code' => [
                'nullable',
                new ValidReferralPhone(),
            ],
            'referral' => [
                'nullable',
                Rule::exists((new Customer())->getTable(), 'uuid_code'),
            ],
            'name' => ['required', 'max:120', 'min:2'],
            'email' => [
                'nullable',
                // Rule::requiredIf(! EcommerceHelper::isLoginUsingPhone()),
                new EmailRule(),
                Rule::unique((new Customer())->getTable()),
            ],
            'phone' => array_merge(
                ['nullable'],
                Rule::requiredIf(EcommerceHelper::isLoginUsingPhone() || get_ecommerce_setting('make_customer_phone_number_required', false)) ? ['required'] : [],
                explode('|', BaseHelper::getPhoneValidationRule()),
                $this->getPhoneLengthRulesByCountry($country),
                [new UniquePhone()],
            ),
            'password' => ['required', 'min:6', 'confirmed'],
            'agree_terms_and_policy' => ['sometimes', 'accepted:1'],
        ];

        return apply_filters('ecommerce_customer_registration_form_validation_rules', $rules);
    }

    public function attributes(): array
    {
        return apply_filters('ecommerce_customer_registration_form_validation_attributes', [
            'name' => __('Name'),
            'email' => __('Email'),
            'password' => __('Password'),
            'phone' => __('Phone'),
            'referral_code' => __('Referral Code'),
            'agree_terms_and_policy' => __('Term and Policy'),
        ]);
    }

    public function messages(): array
    {
        return apply_filters('ecommerce_customer_registration_form_validation_messages', []);
    }
}
