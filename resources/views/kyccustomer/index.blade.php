@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', __('Overview'))

@section('content')
    @once
        <style>
            .marketing-kyc {
                --mk-primary: #4BA213;
                --mk-primary-rgb: 75, 162, 19;
                --mk-muted: #6b7280;
                --mk-border: rgba(0, 0, 0, 0.08);
                --mk-soft: rgba(var(--mk-primary-rgb), 0.08);
                --mk-header-h: 56px;

                color: #1c1b23;
            }

            .marketing-kyc__header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 1050;
                background: #fff;
                border-bottom: 1px solid var(--mk-border);
                padding: 10px 14px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            }

            .marketing-kyc__back {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                border: 1px solid rgba(75, 162, 19, 0.2);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: var(--mk-primary);
                text-decoration: none;
            }

            .marketing-kyc .icon-tabler {
                width: 1rem;
                height: 1rem;
            }

            .marketing-kyc__title {
                margin: 0;
                font-size: 1rem;
                font-weight: 700;
                color: var(--mk-primary);
            }



            .marketing-kyc__section {
                margin-bottom: 18px;
            }

            .marketing-card {
                background: #fff;
                border: 1px solid var(--mk-border);
                border-radius: 16px;
                box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
            }

            .marketing-card__header {
                padding: 16px 16px 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .marketing-card__title {
                margin: 0;
                font-size: 1rem;
                font-weight: 700;
                color: var(--mk-primary);
            }

            .marketing-card__body {
                padding: 16px;
            }

            .kyc-profile {
                display: grid;
                grid-template-columns: 120px 1fr;
                gap: 18px;
                align-items: center;
            }

            .kyc-profile__avatar {
                width: 120px;
                height: 120px;
                border-radius: 999px;
                overflow: hidden;
                border: 3px solid #fff;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
                background: var(--mk-soft);
                display: grid;
                place-items: center;
                color: var(--mk-primary);
                font-weight: 800;
                font-size: 40px;
            }

            .kyc-profile__avatar-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .kyc-profile__details {
                display: grid;
                gap: 10px;
            }

            .kyc-profile__item {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: baseline;
            }

            .kyc-profile__label {
                font-weight: 600;
                color: var(--mk-muted);
                min-width: 120px;
            }

            .kyc-profile__value {
                font-weight: 600;
                color: #1c1b23;
                word-break: break-word;
            }

            .kyc-status {
                display: grid;
                gap: 10px;
            }

            .kyc-status__row {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 10px;
            }

            .kyc-status__label {
                font-weight: 600;
                color: var(--mk-muted);
                min-width: 160px;
            }

            .btn-kyc {
                background: var(--mk-primary);
                border-color: var(--mk-primary);
                color: #fff;
                border-radius: 999px;
                padding: 10px 18px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .btn-kyc:hover,
            .btn-kyc:focus {
                background: #3f9111;
                border-color: #3f9111;
                color: #fff;
                box-shadow: 0 0 0 0.2rem rgba(var(--mk-primary-rgb), 0.2);
            }

            .marketing-kyc .form-label {
                font-weight: 600;
                color: #1c1b23;
            }

            .marketing-kyc .form-control,
            .marketing-kyc .form-select,
            .marketing-kyc input[type="text"],
            .marketing-kyc input[type="email"],
            .marketing-kyc input[type="tel"],
            .marketing-kyc input[type="date"],
            .marketing-kyc input[type="password"],
            .marketing-kyc textarea,
            .marketing-kyc select {
                border-radius: 12px !important;
                border: 1px solid rgba(0, 0, 0, 0.12);
                background: #fff;
                padding: 12px 12px;
                box-shadow: none !important;
                transition: border-color 0.18s ease, box-shadow 0.18s ease;
            }

            .marketing-kyc input[type="file"].form-control {
                padding: 10px;
                height: auto;
            }

            .marketing-kyc .form-control:focus,
            .marketing-kyc .form-select:focus,
            .marketing-kyc input:focus,
            .marketing-kyc textarea:focus,
            .marketing-kyc select:focus {
                border-color: rgba(var(--mk-primary-rgb), 0.55) !important;
                box-shadow: 0 0 0 0.18rem rgba(var(--mk-primary-rgb), 0.16) !important;
            }

            .marketing-kyc .alert {
                border-radius: 12px;
            }

            @media (max-width: 767.98px) {
                .marketing-kyc__body {
                    padding-left: 12px;
                    padding-right: 12px;
                }

                .kyc-profile {
                    grid-template-columns: 90px 1fr;
                    gap: 14px;
                }

                .kyc-profile__avatar {
                    width: 90px;
                    height: 90px;
                    font-size: 32px;
                }

                .kyc-profile__label,
                .kyc-status__label {
                    min-width: 0;
                }

                .btn-kyc {
                    width: 100%;
                    justify-content: center;
                }
            }

            @media (min-width: 768px) {
                .marketing-kyc__header {
                    display: none;
                }

                .marketing-kyc__body {
                    padding-top: 0;
                    padding-left: 0;
                    padding-right: 0;
                }
            }
        </style>
    @endonce

    @php
        $customer = Auth::guard('customer')->user();
    @endphp

    <div class="marketing-kyc">
        <div class="marketing-kyc__header d-md-none">
            <a href="{{ route('setting') }}" class="marketing-kyc__back" aria-label="{{ __('Back') }}">
                <x-core::icon name="ti ti-chevron-left" />
            </a>
            <h1 class="marketing-kyc__title">{{ __('Xác thực tài khoản') }}</h1>
        </div>

        <div class="marketing-kyc__body">
            <div class="container px-0 px-md-3">
                <section class="marketing-kyc__section">
                    <div class="marketing-card">
                        <div class="marketing-card__header">
                            <h3 class="marketing-card__title">{{ trans('core/base::layouts.your_infomation') }}</h3>
                        </div>
                        <div class="marketing-card__body">
                            <div class="kyc-profile">
                                <div class="kyc-profile__avatar">
                                    @if (!empty($customer->avatar))
                                        <img src="{{ asset('storage/' . $customer->avatar) }}" alt="{{ $customer->name }}"
                                            class="kyc-profile__avatar-img" loading="lazy">
                                    @else
                                        <span>{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="kyc-profile__details">
                                    <div class="kyc-profile__item">
                                        <span class="kyc-profile__label">{{ trans('core/base::layouts.name') }}</span>
                                        <span class="kyc-profile__value">{{ $customer->name }}</span>
                                    </div>
                                    <div class="kyc-profile__item">
                                        <span class="kyc-profile__label">{{ trans('core/base::layouts.email') }}</span>
                                        <span class="kyc-profile__value">{{ $customer->email }}</span>
                                    </div>
                                    <div class="kyc-profile__item">
                                        <span class="kyc-profile__label">{{ trans('core/base::layouts.phone') }}</span>
                                        <span class="kyc-profile__value">{{ $customer->phone ?? 'N/A' }}</span>
                                    </div>
                                    <div class="kyc-profile__item">
                                        <span class="kyc-profile__label">{{ trans('core/base::layouts.rank') }}</span>
                                        <span class="kyc-profile__value">{{ $customer->rank->rank_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="marketing-kyc__section">
                    <div class="marketing-card">
                        <div class="marketing-card__header">
                            <h3 class="marketing-card__title">{{ trans('core/base::layouts.kyc_status') }}</h3>
                        </div>
                        <div class="marketing-card__body">
                            @if ($kycPending)
                                <div class="kyc-status">
                                    <div class="kyc-status__row">
                                        <span class="kyc-status__label">{{ trans('core/base::layouts.status') }}</span>
                                        <span
                                            class="badge {{ $kycPending->status == 'approved' ? 'bg-success' : ($kycPending->status == 'pending' ? 'bg-warning' : 'bg-danger') }} text-white px-3 py-2">
                                            {{ trans('core/base::layouts.' . $kycPending->status) }}
                                        </span>
                                    </div>
                                    <div class="kyc-status__row">
                                        <span class="kyc-status__label">{{ trans('core/base::layouts.verification_type') }}</span>
                                        <span>{{ $kycPending->verification_type }}</span>
                                    </div>
                                    <div class="kyc-status__row">
                                        <span class="kyc-status__label">{{ trans('core/base::layouts.submitted_at') }}</span>
                                        <span>{{ optional($kycPending->created_at)->format('d-m-Y H:i') ?? 'N/A' }}</span>
                                    </div>
                                    @if ($kycPending->status == 'rejected' && $kycPending->logs->where('action', 'rejected')->first())
                                        <div class="kyc-status__row">
                                            <span class="kyc-status__label">{{ trans('core/base::layouts.rejection_reason') }}</span>
                                            <span class="text-danger">
                                                @php
                                                    $kycLog =
                                                        $kycPending->logs->where('action', 'rejected')->first()->reason ??
                                                        'N/A';
                                                @endphp
                                                {{ trans('core/base::layouts.' . $kycLog) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted mb-0">
                                    {{ trans('core/base::layouts.you_have_not_submitted_a_kyc_request_yet') }}.
                                </p>
                            @endif
                        </div>
                    </div>
                </section>

                @if (!$kycPending || $kycPending->status == 'rejected')
                    <section class="marketing-kyc__section">
                        <div class="marketing-card">
                            <div class="marketing-card__header">
                                <h3 class="marketing-card__title">{{ trans('core/base::layouts.submit_new_kyc_request') }}</h3>
                            </div>
                            <div class="marketing-card__body">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="{{ trans('core/base::layouts.close') }}"></button>
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="{{ trans('core/base::layouts.close') }}"></button>
                                    </div>
                                @endif

                                <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data"
                                    id="kycForm">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="kyc_form_id" class="form-label">
                                            {{ trans('core/base::layouts.kyc_form_type') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="kyc_form_id" id="kyc_form_id"
                                            class="form-select form-control @error('kyc_form_id') is-invalid @enderror"
                                            required>
                                            <option value="">{{ trans('core/base::layouts.select_a_kyc_form_type') }}</option>
                                            @foreach ($kycForms as $form)
                                                <option value="{{ $form->id }}"
                                                    {{ old('kyc_form_id') == $form->id ? 'selected' : '' }}>
                                                    {{ $form->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('kyc_form_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div id="dynamicFields"></div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-kyc">
                                            <x-core::icon name="ti ti-send" />
                                            {{ trans('core/base::layouts.submit_kyc') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal chọn ngày tháng năm -->
    <div class="modal fade" id="datePickerModal" tabindex="-1" aria-labelledby="datePickerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="datePickerModalLabel" style="color:#ffff !important">
                        {{ trans('core/base::layouts.select_date') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ trans('core/base::layouts.close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <label for="modal_day" class="form-label">{{ trans('core/base::layouts.day') }}</label>
                            <select id="modal_day" class="form-select"
                                aria-label="{{ trans('core/base::layouts.day') }}">
                                <option value="" disabled selected>{{ trans('core/base::layouts.select_day') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="modal_month" class="form-label">{{ trans('core/base::layouts.month') }}</label>
                            <select id="modal_month" class="form-select"
                                aria-label="{{ trans('core/base::layouts.month') }}">
                                <option value="" disabled selected>{{ trans('core/base::layouts.select_month') }}
                                </option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="modal_year" class="form-label">{{ trans('core/base::layouts.year') }}</label>
                            <select id="modal_year" class="form-select"
                                aria-label="{{ trans('core/base::layouts.year') }}">
                                <option value="" disabled selected>{{ trans('core/base::layouts.select_year') }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div id="dateError" class="text-danger mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ trans('core/base::layouts.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirmDate"
                        style="background-color: #228822; border-color: #228822;">
                        {{ trans('core/base::layouts.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        #datePickerModal .modal-dialog {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 60px);
        }

        #datePickerModal .modal-dialog.modal-md {
            max-width: 500px;
        }

        #datePickerModal .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        #datePickerModal .modal-header {
            padding: 1.5rem;
            background-color: #4BA213;
            color: #fff;
            border-bottom: none;
        }

        #datePickerModal .modal-title {
            font-size: 1.5rem;
        }

        #datePickerModal .modal-body {
            padding: 1.5rem;
        }

        .marketing-kyc .date-display {
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 10px 12px;
            cursor: pointer;
            background-color: #fff;
            transition: border-color 0.2s;
        }

        .marketing-kyc .date-display:hover {
            border-color: #4BA213;
        }

        .marketing-kyc .date-display.is-invalid {
            border-color: #dc3545;
        }

        .marketing-kyc .form-select:focus {
            border-color: rgba(75, 162, 19, 0.55);
            box-shadow: 0 0 0 0.2rem rgba(75, 162, 19, 0.25);
        }

        .marketing-kyc .form-select option:checked {
            background-color: #4BA213;
            color: #fff;
        }

        .tooltip-inner {
            background-color: rgba(0, 128, 0, 0.85);
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 1rem;
            max-width: 300px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .tooltip .bs-tooltip-top .tooltip-arrow::before {
            border-top-color: rgba(0, 128, 0, 0.85);
        }

        .tooltip .bs-tooltip-bottom .tooltip-arrow::before {
            border-bottom-color: rgba(0, 128, 0, 0.85);
        }

        .tooltip .bs-tooltip-start .tooltip-arrow::before {
            border-left-color: rgba(0, 128, 0, 0.85);
        }

        .tooltip .bs-tooltip-end .tooltip-arrow::before {
            border-right-color: rgba(0, 128, 0, 0.85);
        }

        .tooltip {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .tooltip.show {
            opacity: 1;
        }

        .tooltip-inner {
            transform: scale(0.95);
            transition: transform 0.2s ease-in-out;
        }

        .tooltip.show .tooltip-inner {
            transform: scale(1);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kycFormSelect = document.getElementById('kyc_form_id');
            const dynamicFieldsContainer = document.getElementById('dynamicFields');
            const modalDaySelect = document.getElementById('modal_day');
            const modalMonthSelect = document.getElementById('modal_month');
            const modalYearSelect = document.getElementById('modal_year');
            const datePickerModal = document.getElementById('datePickerModal');

            const currentYear = new Date().getFullYear();
            for (let i = currentYear; i >= currentYear - 100; i--) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                modalYearSelect.appendChild(option);
            }

            function updateDays() {
                const month = parseInt(modalMonthSelect.value) || null;
                const year = parseInt(modalYearSelect.value) || null;
                const previousDayValue = modalDaySelect.value;
                modalDaySelect.innerHTML =
                    `<option value="" disabled selected>${@js(trans('core/base::layouts.select_day'))}</option>`;

                let daysInMonth = 31;
                if (month && year) {
                    daysInMonth = new Date(year, month, 0).getDate();
                }

                for (let i = 1; i <= daysInMonth; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    modalDaySelect.appendChild(option);
                }

                if (previousDayValue && parseInt(previousDayValue) <= daysInMonth) {
                    modalDaySelect.value = previousDayValue;
                }
            }

            function isValidDate(day, month, year) {
                if (!day || !month || !year) return false;
                const daysInMonth = new Date(year, month, 0).getDate();
                return parseInt(day) <= daysInMonth && parseInt(day) > 0;
            }

            modalMonthSelect.addEventListener('change', updateDays);
            modalYearSelect.addEventListener('change', updateDays);

            datePickerModal.addEventListener('show.bs.modal', function() {
                updateDays();
            });

            const kycForms = @json($kycForms);

            const userInfo = {
                full_name: @json(Auth::guard('customer')->user()->name),
                phone: @json(Auth::guard('customer')->user()->phone ?? ''),
                date_of_birth: @json(Auth::guard('customer')->user()->date_of_birth ?? '')
            };

            const validationErrors = @json($errors->messages());

            let currentFieldKey = '';

            kycFormSelect.addEventListener('change', function() {
                const selectedFormId = this.value;
                dynamicFieldsContainer.innerHTML = '';

                if (selectedFormId) {
                    const selectedForm = kycForms.find(form => form.id == selectedFormId);
                    if (selectedForm) {
                        const formData = JSON.parse(selectedForm.form);

                        formData.field_name.forEach((fieldKey, index) => {
                            const fieldType = formData.type[index];
                            const fieldValidation = formData.validation[index];
                            const fieldLength = formData.field_length[index];
                            const isRequired = fieldValidation.includes('required');

                            const fieldLabel = formData.field_labels[index] || fieldKey;

                            let defaultValue = '';
                            if (fieldKey === 'ho_va_ten') {
                                defaultValue = userInfo.full_name;
                            } else if (fieldKey === 'so_dien_thoai') {
                                defaultValue = userInfo.phone;
                            }

                            const errorMessage = validationErrors[`data.${fieldKey}`] ?
                                validationErrors[`data.${fieldKey}`][0] : '';

                            let tooltipText = '';
                            switch (fieldKey) {
                                case 'ho_va_ten':
                                    tooltipText = @js(trans('core/base::layouts.enter_your_full_name'));
                                    break;
                                case 'so_dien_thoai':
                                    tooltipText = @js(trans('core/base::layouts.enter_your_phone_number'));
                                    break;
                                case 'so_cccd':
                                    tooltipText = @js(trans('core/base::layouts.enter_referral_information'));
                                    break;
                                case 'dia_chi':
                                    tooltipText = @js(trans('core/base::layouts.enter_your_address'));
                                    break;
                                case 'mat_truoc_cccd':
                                    tooltipText = @js(trans('core/base::layouts.upload_front_id_card'));
                                    break;
                                case 'mat_sau_cccd':
                                    tooltipText = @js(trans('core/base::layouts.upload_back_id_card'));
                                    break;
                                default:
                                    tooltipText = @js(trans('core/base::layouts.fill_in_the_field'));
                            }

                            let inputHtml = '';
                            if (fieldType === 'file') {
                                inputHtml =
                                    `<input type="file" name="data[${fieldKey}]" id="${fieldKey}" class="form-control ${errorMessage ? 'is-invalid' : ''}" accept="image/*" ${isRequired ? 'required' : ''} data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}">`;
                            } else if (fieldType === 'datetime') {
                                const [defaultYear, defaultMonth, defaultDay] = defaultValue ?
                                    defaultValue.split('-') : ['', '', ''];
                                const displayDate = defaultValue ?
                                    `${defaultDay}-${defaultMonth}-${defaultYear}` :
                                    @js(trans('core/base::layouts.select_date'));
                                inputHtml = `
                        <div class="date-display ${errorMessage ? 'is-invalid' : ''}" id="${fieldKey}_display" data-bs-toggle="modal" data-bs-target="#datePickerModal" data-field-key="${fieldKey}" role="button" aria-label="${@js(trans('core/base::layouts.select_date'))}">
                            ${displayDate}
                        </div>
                        <input type="hidden" name="data[${fieldKey}]" id="${fieldKey}" class="${errorMessage ? 'is-invalid' : ''}" ${isRequired ? 'required' : ''} value="${defaultValue || ''}">
                        `;
                            } else {
                                inputHtml =
                                    `<input type="text" name="data[${fieldKey}]" id="${fieldKey}" class="form-control ${errorMessage ? 'is-invalid' : ''}" maxlength="${fieldLength}" ${isRequired ? 'required' : ''} value="${defaultValue}" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipText}">`;
                            }

                            const fieldHtml = `
                    <div class="mb-4">
                        <label for="${fieldKey}" class="form-label">${fieldLabel} ${isRequired ? '<span class="text-danger">*</span>' : ''}</label>
                        ${inputHtml}
                        ${errorMessage ? `<div class="invalid-feedback">${errorMessage}</div>` : ''}
                    </div>
                    `;

                            dynamicFieldsContainer.insertAdjacentHTML('beforeend', fieldHtml);
                        });

                        // Gắn sự kiện click cho các div date-display
                        document.querySelectorAll('.date-display').forEach(display => {
                            display.addEventListener('click', function() {
                                currentFieldKey = this.getAttribute('data-field-key');
                                const hiddenInput = document.getElementById(
                                    currentFieldKey);
                                const [year, month, day] = hiddenInput.value ? hiddenInput
                                    .value.split('-') : ['', '', ''];
                                modalDaySelect.value = day ? parseInt(day) : '';
                                modalMonthSelect.value = month ? parseInt(month) : '';
                                modalYearSelect.value = year || '';
                            });
                        });

                        // Kích hoạt tooltip cho các input mới tạo
                        const tooltipTriggerList = [].slice.call(dynamicFieldsContainer.querySelectorAll(
                            '[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                            new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    }
                }
            });

            document.getElementById('confirmDate').addEventListener('click', function() {
                const day = modalDaySelect.value;
                const month = modalMonthSelect.value;
                const year = modalYearSelect.value;
                const dateError = document.getElementById('dateError');

                dateError.style.display = 'none';
                dateError.textContent = '';

                if (!day || !month || !year) {
                    dateError.textContent = @js(trans('core/base::layouts.please_select_date'));
                    dateError.style.display = 'block';
                    return;
                }

                const formattedDay = day.padStart(2, '0');
                const formattedMonth = month.padStart(2, '0');
                const dateValue = `${year}-${formattedMonth}-${formattedDay}`;

                if (isValidDate(day, month, year)) {
                    document.getElementById(currentFieldKey).value = dateValue;
                    document.getElementById(`${currentFieldKey}_display`).textContent =
                        `${formattedDay}-${formattedMonth}-${year}`;
                    bootstrap.Modal.getInstance(datePickerModal).hide();
                } else {
                    dateError.textContent = @js(trans('core/base::layouts.invalid_date'));
                    dateError.style.display = 'block';
                }
            });

            if (kycFormSelect.value) {
                kycFormSelect.dispatchEvent(new Event('change'));
            }

            document.getElementById('kycForm').addEventListener('submit', function(event) {
                const hiddenDateInputs = document.querySelectorAll(
                    'input[type="hidden"][name^="data["][value=""]');
                if (hiddenDateInputs.length > 0) {
                    event.preventDefault();
                    alert(@js(trans('core/base::layouts.please_select_date')));
                }
            });
        });
    </script>
@endsection

@push('footer')
    <script>
        'use strict';

        var BotbleVariables = BotbleVariables || {};
        BotbleVariables.languages = BotbleVariables.languages || {};
        BotbleVariables.languages.reports = {
            !!json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!
        };
    </script>
@endpush
