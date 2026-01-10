<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Events\CustomerEmailVerified;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Forms\Fronts\Auth\RegisterForm;
use Botble\Ecommerce\Http\Requests\RegisterRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\JsValidation\Facades\JsValidator;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class RegisterController extends BaseController
{
    use RegistersUsers;

    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('customer.guest');
    }

    public function showRegistrationForm()
    {
        SeoHelper::setTitle(__('Register'));

        Theme::breadcrumb()->add(__('Register'), route('customer.register'));

        if (
            ! session()->has('url.intended') &&
            ! in_array(url()->previous(), [route('customer.login'), route('customer.register')])
        ) {
            session(['url.intended' => url()->previous()]);
        }

        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery'], version: '1.0.1');

        add_filter(THEME_FRONT_FOOTER, function ($html) {
            return $html . JsValidator::formRequest(RegisterRequest::class)->render();
        });

        return Theme::scope(
            'ecommerce.customers.register',
            [
                'form' => RegisterForm::create(),
            ],
            'plugins/ecommerce::themes.customers.register'
        )->render();
    }

    public function register(RegisterRequest $request)
    {
        if (empty($request->email)) {
            $request->merge([
                'email' => 'templinhchi' . rand(10000, 99999) . '@gmail.com',
            ]);
        }

        do_action('customer_register_validation', $request);

        $phone = $this->formatPhone($request->input('phone'), $request->input('country'));

        $data = $request->input();
        $data['phone'] = $phone;

        $customer = $this->create($data);

        event(new Registered($customer));

        if (EcommerceHelper::isEnableEmailVerification()) {
            $this->registered($request, $customer);

            $message = __('We have sent you an email to verify your email. Please check and confirm your email address!');

            return $this
                ->httpResponse()
                ->setNextUrl(route('customer.login'))
                ->with(['auth_warning_message' => $message])
                ->setMessage($message);
        }

        $customer->confirmed_at = Carbon::now();
        $customer->save();

        $this->guard()->login($customer);

        return $this
            ->httpResponse()
            ->setNextUrl($this->redirectPath())
            ->setMessage(__('Registered successfully!'));
    }

    protected function create(array $data)
    {
        $referralId = $this->resolveReferralId($data['referral'] ?? $data['referral_code'] ?? null);

        return Customer::query()->create([
            'name' => Str::upper(BaseHelper::clean($data['name'])),
            'referral_ids' => $referralId,
            'email' => BaseHelper::clean($data['email']),
            'phone' => BaseHelper::clean($data['phone'] ?? null),
            'uuid_code' => $this->generateUuid(),
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function guard()
    {
        return auth('customer');
    }

    public function confirm(int|string $id, Request $request)
    {
        abort_unless(URL::hasValidSignature($request), 404);

        /**
         * @var Customer $customer
         */
        $customer = Customer::query()->findOrFail($id);

        $customer->confirmed_at = Carbon::now();
        $customer->save();

        $this->guard()->login($customer);

        CustomerEmailVerified::dispatch($customer);

        return $this
            ->httpResponse()
            ->setNextUrl(route('customer.overview'))
            ->setMessage(__('You successfully confirmed your email address.'));
    }

    public function resendConfirmation(Request $request)
    {
        /**
         * @var Customer $customer
         */
        $customer = Customer::query()->where('email', $request->input('email'))->first();

        if (! $customer) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Cannot find this customer!'));
        }

        $customer->sendEmailVerificationNotification();

        return $this
            ->httpResponse()
            ->setMessage(__('We sent you another confirmation email. You should receive it shortly.'));
    }

    protected function resolveReferralId(?string $referralCode): ?int
    {
        $fallbackId = Customer::query()->find(1)?->id;

        if (! $referralCode) {
            return $fallbackId;
        }

        $referralCode = trim($referralCode);

        $query = Customer::query()
            ->where('uuid_code', $referralCode)
            ->orWhere('phone', $referralCode);

        $alternatePhone = $this->convertPhoneFormat($referralCode);

        if ($alternatePhone !== $referralCode) {
            $query->orWhere('phone', $alternatePhone);
        }

        $referral = $query->first();

        return $referral?->id ?? $fallbackId;
    }

    protected function convertPhoneFormat(string $phone): string
    {
        if (str_starts_with($phone, '0')) {
            return '+84' . substr($phone, 1);
        }

        if (str_starts_with($phone, '+84')) {
            return '0' . substr($phone, 3);
        }

        return $phone;
    }

    protected function formatPhone(?string $phone, ?string $country): ?string
    {
        if (! $phone) {
            return null;
        }

        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phoneProto = $phoneUtil->parse($phone, strtoupper($country ?: 'VN'));

            return $phoneUtil->format($phoneProto, PhoneNumberFormat::E164);
        } catch (\Exception $exception) {
            return $phone;
        }
    }

    protected function generateUuid(): string
    {
        $uuid = Str::uuid()->toString();

        while (Customer::query()->where('uuid_code', $uuid)->exists()) {
            $uuid = Str::uuid()->toString();
        }

        return $uuid;
    }
}
