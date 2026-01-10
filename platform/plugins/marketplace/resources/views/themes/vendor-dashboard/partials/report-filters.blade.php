<x-core::card class="mb-4">
    <x-core::card.header class="d-flex align-items-start flex-wrap gap-3">
        <div>
            <x-core::card.title>{{ trans('plugins/marketplace::marketplace.filter_title') }}</x-core::card.title>
            <x-core::card.subtitle>{{ trans('plugins/marketplace::marketplace.filter_subtitle') }}</x-core::card.subtitle>
        </div>

        <div class="ms-auto">
            <x-core::button
                type="button"
                color="primary"
                :outlined="true"
                class="date-range-picker"
                :data-format-value="trans('plugins/ecommerce::reports.date_range_format_value', ['from' => '__from__', 'to' => '__to__'])"
                :data-format="\Illuminate\Support\Str::upper(config('core.base.general.date_format.js.date'))"
                :data-href="route('marketplace.vendor.dashboard')"
                :data-start-date="$data['startDate']->format('Y-m-d')"
                :data-end-date="$data['endDate']->format('Y-m-d')"
                icon="ti ti-calendar"
            >
                {{ trans('plugins/ecommerce::reports.date_range_format_value', [
                    'from' => BaseHelper::formatDate($data['startDate']),
                    'to' => BaseHelper::formatDate($data['endDate']),
                ]) }}
            </x-core::button>
        </div>
    </x-core::card.header>

    <x-core::card.body>
        <form
            id="vendor-dashboard-filter"
            class="report-filter-form"
            method="GET"
        >
            <input type="hidden" name="date_from" value="{{ request('date_from', $data['startDate']->format('Y-m-d')) }}">
            <input type="hidden" name="date_to" value="{{ request('date_to', $data['endDate']->format('Y-m-d')) }}">
            <input type="hidden" name="predefined_range" value="{{ request('predefined_range', $data['predefinedRange']) }}">

            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-4">
                    <label class="form-label" for="dashboard-keyword">{{ trans('plugins/marketplace::marketplace.filter_search_label') }}</label>
                    <input
                        id="dashboard-keyword"
                        type="search"
                        name="keyword"
                        value="{{ $filterValues['keyword'] }}"
                        class="form-control"
                        placeholder="{{ trans('plugins/marketplace::marketplace.filter_search_placeholder') }}"
                    >
                </div>

                <div class="col-6 col-lg-3">
                    <label class="form-label" for="dashboard-order-status">{{ trans('plugins/marketplace::marketplace.filter_order_status') }}</label>
                    <select
                        id="dashboard-order-status"
                        name="order_status"
                        class="form-select"
                    >
                        <option value="">{{ trans('plugins/marketplace::marketplace.filter_all_statuses') }}</option>
                        @foreach ($filterOptions['order_statuses'] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected($filterValues['order_status'] === $value)
                            >{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-lg-3">
                    <label class="form-label" for="dashboard-payment-status">{{ trans('plugins/marketplace::marketplace.filter_payment_status') }}</label>
                    <select
                        id="dashboard-payment-status"
                        name="payment_status"
                        class="form-select"
                    >
                        <option value="">{{ trans('plugins/marketplace::marketplace.filter_all_payments') }}</option>
                        @foreach ($filterOptions['payment_statuses'] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected($filterValues['payment_status'] === $value)
                            >{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-lg-2 text-lg-end">
                    <x-core::button
                        type="submit"
                        color="primary"
                        class="w-100"
                    >
                        {{ trans('plugins/marketplace::marketplace.filter_apply') }}
                    </x-core::button>
                    <a
                        class="btn btn-link w-100 mt-2"
                        href="{{ route('marketplace.vendor.dashboard') }}"
                    >{{ trans('plugins/marketplace::marketplace.filter_reset') }}</a>
                </div>
            </div>
        </form>
    </x-core::card.body>
</x-core::card>

@push('footer')
    <script>
        'use strict';

        var BotbleVariables = BotbleVariables || {};
        BotbleVariables.languages = BotbleVariables.languages || {};
        BotbleVariables.languages.reports = {!! json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!};
    </script>

    <script>
        (() => {
            const filterForm = document.getElementById('vendor-dashboard-filter');

            if (!filterForm) {
                return;
            }

            filterForm.querySelectorAll('select').forEach((select) => {
                select.addEventListener('change', () => filterForm.submit());
            });

            const keyword = filterForm.querySelector('#dashboard-keyword');
            if (keyword) {
                keyword.addEventListener('keypress', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        filterForm.submit();
                    }
                });
            }
        })();
    </script>
@endpush
