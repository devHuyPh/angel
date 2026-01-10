<?php

namespace Botble\Ecommerce\Forms\Fronts\Auth;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\PasswordField;
use Botble\Base\Forms\Fields\PhoneNumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Forms\Fronts\Auth\FieldOptions\EmailFieldOption;
use Botble\Ecommerce\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\Ecommerce\Http\Requests\RegisterRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\Theme\Facades\Theme;

class RegisterReferralForm extends AuthForm
{
    public static function formTitle(): string
    {
        return __('Customer register form');
    }


    public function setup(): void
    {
        parent::setup();
        $this
            ->setUrl(route('customer.register.referral.post'))
            ->setValidatorClass(RegisterRequest::class)
            ->icon('ti ti-user-plus')
            ->heading(__('Đăng ký tài khoản'))
            ->description(__('Dữ liệu cá nhân của bạn sẽ được sử dụng để hỗ trợ trải nghiệm của bạn trên toàn bộ trang web này, để quản lý quyền truy cập vào tài khoản của bạn.'))
            ->when(
                theme_option('register_background'),
                fn(AuthForm $form, string $background) => $form->banner($background)
            )
            ->add(
                'referral',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('ID Mã giới thiệu'))
                    ->value('')
                    ->icon('ti ti-user')
                    ->addAttribute('id', 'registerReferralID')
                    ->addAttribute('readonly', 'true')
                    ->append('<div class="" id="result-uuid"></div></div>')
            )
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Họ và tên'))
                    ->placeholder(__('Họ và tên của bạn'))
                    ->icon('ti ti-user')
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(__('Email'))
                    ->when(EcommerceHelper::isLoginUsingPhone(), function (EmailFieldOption $fieldOption): void {
                        $fieldOption->label(__('Email (lựa chọn)'));
                    })
                    ->placeholder(__('Email của bạn'))
                    ->icon('ti ti-mail')
                    ->addAttribute('autocomplete', 'email')
            )
            ->when(get_ecommerce_setting('enabled_phone_field_in_registration_form', true), static function (FormAbstract $form) {
                $form
                    ->add(
                        'phone',
                        PhoneNumberField::class,
                        TextFieldOption::make()
                            ->label(__('Phone (optional)'))
                            ->when(EcommerceHelper::isLoginUsingPhone() || get_ecommerce_setting('make_customer_phone_number_required', false), static function (TextFieldOption $fieldOption): void {
                                $fieldOption
                                    ->required()
                                    ->label(__('Phone'));
                            })
                            ->placeholder(__('Phone number'))
                            ->icon('')
                            ->addAttribute('autocomplete', 'tel')
                            ->cssClass('input_phone')
                            ->append(
                                '
                                    <div class="custom-dropdown" id="countryDropdown">
                                        <div class="custom-dropdown-toggle form-control" onclick="toggleDropdown()">
                                           <img src="' . url('/vendor/core/core/base/img/flags/vn.svg') . '" alt="Việt Nam" id="selectedFlag"> +84
                                        </div>
                                        <ul class="custom-dropdown-menu" id="dropdownMenu" style="display: none;"></ul>
                                    </div>
                                    <input type="hidden" name="country" id="countryInput" value="vn">
                                </div>
                                '
                            )

                    );
            })
            ->add(
                'password',
                PasswordField::class,
                TextFieldOption::make()
                    ->label(__('Mật khẩu'))
                    ->placeholder(__('Mật khẩu'))
                    ->icon('ti ti-lock')
            )
            ->add(
                'password_confirmation',
                PasswordField::class,
                TextFieldOption::make()
                    ->label(__('Xác nhận mật khẩu'))
                    ->placeholder(__('Xác nhận mật khẩu'))
                    ->icon('ti ti-lock')
            )
            ->add(
                'agree_terms_and_policy',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->when(
                        $privacyPolicyUrl = Theme::termAndPrivacyPolicyUrl(),
                        function (CheckboxFieldOption $fieldOption, string $url): void {
                            $fieldOption->label(__('Tôi đồng ý với :link', ['link' => Html::link($url, __('Điều khoản và Chính sách bảo mật'), attributes: ['class' => 'text-decoration-underline', 'target' => '_blank'])]));
                        }
                    )
                    ->when(! $privacyPolicyUrl, function (CheckboxFieldOption $fieldOption): void {
                        $fieldOption->label(__('Tôi đồng ý với Điều khoản và Chính sách bảo mật'));
                    })
            )
            ->submitButton(__('Đăng kí'), 'ti ti-arrow-narrow-right')
            ->add(
                'login',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->view('plugins/ecommerce::customers.includes.login-link')
            )
            ->add('filters', HtmlField::class, [
                'html' => apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, Customer::class),
            ]);
    }
}
