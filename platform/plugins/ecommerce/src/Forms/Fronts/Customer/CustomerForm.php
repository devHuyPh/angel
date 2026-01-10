<?php

namespace Botble\Ecommerce\Forms\Fronts\Customer;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\InputFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Ecommerce\Http\Requests\EditAccountRequest;
use Botble\Ecommerce\Models\Customer;
use Botble\Theme\FormFront;
use Illuminate\Support\Facades\App;

class CustomerForm extends FormFront
{
    public function setup(): void
    {
        $this
            ->model(Customer::class)
            ->setUrl(route('customer.edit-account'))
            ->setValidatorClass(EditAccountRequest::class)
            ->contentOnly()
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Full Name'))
            )
            // ->when(get_ecommerce_setting('enabled_customer_dob_field', true), function (CustomerForm $form): void {
            //     $form->add(
            //         'dob',
            //         TextField::class,
            //         InputFieldOption::make()
            //             ->addAttribute('id', 'date_of_birth')
            //             ->addAttribute('data-date-format', config('core.base.general.date_format.js.date'))
            //             ->addAttribute('data-locale', App::getLocale())
            //             ->value($this->getModel()->dob ? BaseHelper::formatDate($this->getModel()->dob) : null)
            //             ->label(__('Date of birth'))
            //     );
            // })
            ->when(get_ecommerce_setting('enabled_customer_dob_field', true), function (CustomerForm $form): void {
                $dob = $form->getModel()->dob;

                $selectedDay = $dob ? date('d', strtotime($dob)) : null;
                $selectedMonth = $dob ? date('m', strtotime($dob)) : null;
                $selectedYear = $dob ? date('Y', strtotime($dob)) : null;

                $form
                    ->add(
                        'dob_group',
                        HtmlField::class,
                        HtmlFieldOption::make()
                            ->label(__('Date of birth'))
                            ->content('<div class="row dob-selects align-items-end mb-3"><p id="dob_preview" class="text-muted mb-1"></p>')
                    )
                    ->add(
                        'select_year',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Year'))
                            ->choices(array_combine(range(date('Y'), 1900), range(date('Y'), 1900)))
                            ->selected($selectedYear)
                            ->wrapperAttributes(['class' => 'col'])
                    )
                    ->add(
                        'select_month',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Month'))
                            ->choices([
                                '01' => __('January'),
                                '02' => __('February'),
                                '03' => __('March'),
                                '04' => __('April'),
                                '05' => __('May'),
                                '06' => __('June'),
                                '07' => __('July'),
                                '08' => __('August'),
                                '09' => __('September'),
                                '10' => __('October'),
                                '11' => __('November'),
                                '12' => __('December'),
                            ])
                            ->selected($selectedMonth)
                            ->wrapperAttributes(['class' => 'col'])
                    )
                    ->add(
                        'select_day',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Date'))
                            ->choices(array_combine(range(1, 31), range(1, 31)))
                            ->selected($selectedDay)
                            ->wrapperAttributes(['class' => 'col'])
                    )

                    ->add(
                        'dob_group_end',
                        HtmlField::class,
                        HtmlFieldOption::make()
                            ->content('</div>')
                    )

                    ->add(
                        'dob',
                        TextField::class,
                        InputFieldOption::make()
                            ->addAttribute('id', 'date_of_birth')
                            ->addAttribute('data-date-format', config('core.base.general.date_format.js.date'))
                            ->addAttribute('data-locale', App::getLocale())
                            ->value($dob ? BaseHelper::formatDate($dob) : null)
                            ->label(__('Date of birth'))
                            ->addAttribute('readonly', 'readonly')
                            ->wrapperAttributes(['class' => 'd-none'])
                    );
            })

            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    // ->disabled()
            )
            ->add(
                'phone',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Phone'))
            )
            ->add(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->label(__('Update'))
                    ->cssClass('btn btn-primary')
            );
    }
}
