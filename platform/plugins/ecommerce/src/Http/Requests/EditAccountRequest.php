<?php

namespace Botble\Ecommerce\Http\Requests;

use App\Rules\UniquePhoneRule;
use Botble\Base\Facades\BaseHelper;
use Botble\Ecommerce\Models\Customer;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class EditAccountRequest extends Request
{
    public function rules(): array
    {
        $customer = auth('customer')->user();
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique((new Customer())->getTable(), 'email')->ignore($customer->id),
            ],
            'phone' => array_merge(
                ['sometimes'],
                explode('|', BaseHelper::getPhoneValidationRule()),
                [new UniquePhoneRule($customer->id)]
            ),
            'dob' => ['date_format:' . BaseHelper::getDateFormat(), 'max:20', 'sometimes'],
        ];
    }
}
