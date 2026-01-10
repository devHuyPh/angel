<div class="row mb-3 mt-5">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="ps-block--stat yellow">
            <div class="ps-block__left"><span><i class="icon-bag2"></i></span></div>
            <div class="ps-block__content">
                <p>{{ trans('plugins/marketplace::marketplace.orders_label') }}</p>
                <h4>{{ $totalOrders }}</h4>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="ps-block--stat pink">
            <div class="ps-block__left"><span><i class="icon-bag-dollar"></i></span></div>
            <div class="ps-block__content">
                <p>{{ trans('plugins/marketplace::marketplace.revenue_label') }}</p>
                <h4>{{ format_price($data['revenue']['amount']) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="ps-block--stat green">
            <div class="ps-block__left"><span><i class="icon-database"></i></span></div>
            <div class="ps-block__content">
                <p>{{ trans('plugins/marketplace::marketplace.products_label') }}</p>
                <h4>{{ $totalProducts }}</h4>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="ps-block--stat yellow">
            <div class="ps-block__left"><span><i class="icon-database"></i></span></div>
            <div class="ps-block__content">
                <p>{{ trans('plugins/marketplace::marketplace.items_sold') }}</p>
                <h4>{{ number_format($data['sold_quantity'] ?? 0) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3 mt-6">
    @if (!$totalProducts)
        <div class="col-12">
            <svg
                style="display: none;"
                xmlns="http://www.w3.org/2000/svg"
            >
                <symbol
                    id="check-circle-fill"
                    fill="currentColor"
                    viewBox="0 0 16 16"
                >
                    <path
                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"
                    />
                </symbol>
            </svg>
            <div
                class="alert alert-success"
                role="alert"
            >
                <h4 class="alert-heading">
                    <svg
                        class="bi flex-shrink-0 me-2"
                        role="img"
                        aria-label="Info:"
                        width="24"
                        height="24"
                    >
                        <use xlink:href="#check-circle-fill" />
                    </svg>
                    {{ trans('plugins/marketplace::marketplace.congratulations_vendor', ['site_title' => theme_option('site_title')]) }}
                </h4>
                <p>{{ trans('plugins/marketplace::marketplace.attract_customers') }}</p>
                <hr>
                <p class="mb-0">{!! BaseHelper::clean(trans('plugins/marketplace::marketplace.create_product_here', ['url' => route('marketplace.vendor.products.create')])) !!}</p>
            </div>
        </div>
    @elseif (!$totalOrders)
        <div class="col-12">
            <svg
                style="display: none;"
                xmlns="http://www.w3.org/2000/svg"
            >
                <symbol
                    id="info-fill"
                    fill="currentColor"
                    viewBox="0 0 16 16"
                >
                    <path
                        d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"
                    />
                </symbol>
            </svg>
            <div
                class="alert alert-info"
                role="alert"
            >
                <h4 class="alert-heading">
                    <svg
                        class="bi flex-shrink-0 me-2"
                        role="img"
                        aria-label="Info:"
                        width="24"
                        height="24"
                    >
                        <use xlink:href="#info-fill" />
                    </svg>
                    {{ trans('plugins/marketplace::marketplace.products_but_no_orders', ['total' => $totalProducts]) }}
                </h4>
                <hr>
                <p class="mb-0">{!! BaseHelper::clean(trans('plugins/marketplace::marketplace.view_store_here', ['url' => $user->store->url])) !!}</p>
            </div>
        </div>
    @else
        <div class="col-md-8">
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <div>
                        <x-core::card.title>{{ trans('plugins/marketplace::marketplace.sales_reports') }}</x-core::card.title>
                        <x-core::card.subtitle>
                            <a href="{{ route('marketplace.vendor.revenues.index') }}">
                                {{ trans('plugins/marketplace::marketplace.revenues_in_label', ['label' => $data['predefinedRange']]) }}
                                <x-core::icon name="ti ti-arrow-right" />
                            </a>
                        </x-core::card.subtitle>
                    </div>
                </x-core::card.header>
                <x-core::table.body>
                    <div id="sales-report-chart">
                        <sales-reports-chart
                            url="{{ route('marketplace.vendor.chart.month') }}"
                            date_from='{{ $data['startDate']->format('Y-m-d') }}'
                            date_to='{{ $data['endDate']->format('Y-m-d') }}'
                        ></sales-reports-chart>
                    </div>
                </x-core::table.body>
            </x-core::card>
        </div>

        <div class="col-md-4">
            <x-core::card>
                <x-core::card.header>
                    <div>
                        <x-core::card.title>{{ trans('plugins/marketplace::marketplace.earnings_label') }}</x-core::card.title>
                        <x-core::card.subtitle>{{ trans('plugins/marketplace::marketplace.earnings_in_label', ['label' => $data['predefinedRange']]) }}</x-core::card.subtitle>
                    </div>
                </x-core::card.header>
                <x-core::card.body>
                    <div id="revenue-chart">
                        <revenue-chart
                            :data="{{ json_encode([
                                    ['label' => trans('plugins/marketplace::marketplace.revenue_label'), 'value' => $data['revenue']['amount'], 'color' => '#80bc00'],
                                    ['label' => trans('plugins/marketplace::marketplace.fees_label'), 'value' => $data['revenue']['fee'], 'color' => '#fcb800'],
                                    ['label' => trans('plugins/marketplace::marketplace.withdrawals_label'), 'value' => $data['revenue']['withdrawal'], 'color' => '#fc6b00'],
                                ]) }}"
                        ></revenue-chart>
                    </div>

                    <div class="row mt-4">
                        <x-core::datagrid.item class="col-6 mb-2">
                            <x-slot:title>
                                <x-core::icon name="ti ti-wallet"></x-core::icon>
                                {{ trans('plugins/marketplace::marketplace.earnings_label') }}
                            </x-slot:title>
                            {{ format_price($data['revenue']['sub_amount']) }}
                        </x-core::datagrid.item>

                        <x-core::datagrid.item class="col-6 mb-2">
                            <x-slot:title>
                                {{ trans('plugins/marketplace::marketplace.revenue_label') }}
                            </x-slot:title>
                            {{ format_price($data['revenue']['sub_amount'] - $data['revenue']['fee']) }}
                        </x-core::datagrid.item>

                        <x-core::datagrid.item class="col-6">
                            <x-slot:title>
                                    <span
                                        data-bs-toggle="tooltip"
                                        data-bs-original-title="{{ trans('plugins/marketplace::marketplace.includes_statuses_hint') }}"
                                    >
                                        {{ trans('plugins/marketplace::marketplace.withdrawals_label') }}
                                    </span>
                            </x-slot:title>
                            {{ format_price($data['revenue']['withdrawal']) }}
                        </x-core::datagrid.item>

                        <x-core::datagrid.item class="col-6">
                            <x-slot:title>
                                {{ trans('plugins/marketplace::marketplace.fees_label') }}
                            </x-slot:title>
                            {{ format_price($data['revenue']['fee']) }}
                        </x-core::datagrid.item>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
    @endif
</div>

<div class="row">
    @if ($totalOrders)
        <div class="col-12">
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <x-core::card.title>{{ trans('plugins/marketplace::marketplace.recent_orders') }}</x-core::card.title>
                </x-core::card.header>

                <div class="table-responsive">
                    <x-core::table>
                        <x-core::table.header>
                            <x-core::table.header.cell>{{ trans('core/base::tables.id') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.date_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.customer_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.payment_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.status_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.total_label') }}</x-core::table.header.cell>
                        </x-core::table.header>
                        <x-core::table.body>
                            @forelse ($data['orders'] as $order)
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>
                                        <a href="{{ route('marketplace.vendor.orders.edit', $order->id) }}">
                                            {{ get_order_code($order->id) }}
                                        </a>
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {{ $order->created_at->translatedFormat('M d, Y') }}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        <a href="{{ route('marketplace.vendor.orders.edit', $order->id) }}">
                                            {{ $order->user->name ?: $order->address->name }}
                                        </a>
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {!! BaseHelper::clean($order->payment->status->toHtml()) !!}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {!! BaseHelper::clean($order->status->toHtml()) !!}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {{ format_price($order->amount) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            @empty
                                <x-core::table.body.row>
                                    <x-core::table.body.cell class="text-center" colspan="6">
                                        {{ trans('plugins/marketplace::marketplace.no_orders') }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            @endforelse
                        </x-core::table.body>
                    </x-core::table>
                </div>

                <x-core::card.footer>
                    <a href="{{ route('marketplace.vendor.orders.index') }}">
                        {{ trans('plugins/marketplace::marketplace.view_full_orders') }}
                        <x-core::icon name="ti ti-chevron-right" />
                    </a>
                </x-core::card.footer>
            </x-core::card>
        </div>
    @endif

    @if ($totalProducts)
        <div class="col-12">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>{{ trans('plugins/marketplace::marketplace.top_selling_products') }}</x-core::card.title>
                </x-core::card.header>

                <div class="table-responsive">
                    <x-core::table>
                        <x-core::table.header>
                            <x-core::table.header.cell>{{ trans('core/base::tables.id') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.name_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.amount_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.status_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.created_at_label') }}</x-core::table.header.cell>
                        </x-core::table.header>
                        <x-core::table.body>
                            @forelse ($data['products'] as $product)
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>
                                        {{ $product->id }}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        // <a href="{{ route('marketplace.vendor.products.edit', $product->id) }}">
                                            {{ $product->name }}
                                        // </a>
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {!! BaseHelper::clean($product->price_in_table) !!}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {!! BaseHelper::clean($product->status->toHtml()) !!}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {{ $product->created_at->translatedFormat('M d, Y') }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            @empty
                                <x-core::table.body.row>
                                    <x-core::table.body.cell class="text-center" colspan="6">
                                        {{ trans('plugins/marketplace::marketplace.no_products') }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            @endforelse
                        </x-core::table.body>
                    </x-core::table>
                </div>

                <x-core::card.footer>
                    <a href="{{ route('marketplace.vendor.products.index') }}">
                        {{ trans('plugins/marketplace::marketplace.view_full_products') }}
                        <x-core::icon name="ti ti-chevron-right" />
                    </a>
                </x-core::card.footer>
            </x-core::card>
        </div>
    @endif

    @if (!empty($data['sold_products']) && $data['sold_products']->count())
        <div class="col-12">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>{{ trans('plugins/marketplace::marketplace.products_sold_in_range') }}</x-core::card.title>
                    <x-core::card.subtitle>{{ trans('plugins/marketplace::marketplace.sold_products_hint') }}</x-core::card.subtitle>
                </x-core::card.header>

                <div class="table-responsive">
                    <x-core::table>
                        <x-core::table.header>
                            <x-core::table.header.cell>{{ trans('core/base::tables.id') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.name_label') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.sold_qty') }}</x-core::table.header.cell>
                            <x-core::table.header.cell>{{ trans('plugins/marketplace::marketplace.stock_on_hand') }}</x-core::table.header.cell>
                        </x-core::table.header>
                        <x-core::table.body>
                            @foreach ($data['sold_products'] as $item)
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>
                                        {{ $item->product_id ?? '-' }}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        @if ($item->product_id)
                                            <a href="{{ route('marketplace.vendor.products.edit', $item->product_id) }}">
                                                {{ $item->product_name }}
                                            </a>
                                        @else
                                            {{ $item->product_name }}
                                        @endif
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {{ number_format($item->sold_qty) }}
                                    </x-core::table.body.cell>
                                    <x-core::table.body.cell>
                                        {{ number_format(optional($item->product)->quantity ?? 0) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            @endforeach
                        </x-core::table.body>
                    </x-core::table>
                </div>
            </x-core::card>
        </div>
    @endif
</div>
