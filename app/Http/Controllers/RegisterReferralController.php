<?php

namespace App\Http\Controllers;

use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Ecommerce\Events\CustomerEmailVerified;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Forms\Fronts\Auth\RegisterReferralForm;
use Botble\Ecommerce\Http\Requests\RegisterRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\JsValidation\Facades\JsValidator;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Faker\Core\Uuid;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class RegisterReferralController extends BaseController
{
    use RegistersUsers;

    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('customer.guest');
    }

    public function showRegistrationForm($username)
    {
        SeoHelper::setTitle(__('Register'));

        Theme::breadcrumb()->add(__('Register'), route('customer.register'));

        if (
            !session()->has('url.intended') &&
            !in_array(url()->previous(), [route('customer.login'), route('customer.register')])
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
                'form' => RegisterReferralForm::create(),
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

        // dd($request->all());
        do_action('customer_register_validation', $request);
        /**
         * @var Customer $customer
         */
        $phone = $request->input('phone');
        try {
            $phoneUtil = PhoneNumberUtil::getInstance();
            $phoneProto = $phoneUtil->parse($request->input('phone'), strtoupper(request()->input('country', 'VN')));
            $phone = $phoneUtil->format($phoneProto, PhoneNumberFormat::E164);
        } catch (\Exception $e) {
            $phone = $request->input('phone'); // fallback nếu parse thất bại
        }
        /**
         * @var Customer $customer
         */
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
        $referral = Customer::where('uuid_code', $data['referral'])->firstOrFail();
        // dd(Str::uuid());
        if (!$referral->getAttributes()) {
            $referral = Customer::where('id', 1)->firstOrFail(); // admin
        }
        return Customer::query()->create([
            'name' => Str::upper(BaseHelper::clean($data['name'])),
            'referral_ids' => $referral['id'],
            'email' => BaseHelper::clean($data['email']),
            'phone' => BaseHelper::clean($data['phone'] ?? null),
            'uuid_code' => Str::uuid(),
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

        if (!$customer) {
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
}
