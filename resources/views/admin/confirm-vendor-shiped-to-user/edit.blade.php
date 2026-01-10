@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <style>
        .ship-infor-item>p {
            padding-left: 10px;
        }
    </style>
    <form method="POST" action="{{ route('store-to-user.update', $confirmVendorShiped->id) }}" accept-charset="UTF-8"
        id="botble-marketplace-forms-withdrawal-form" class="js-base-form dirty-check" novalidate="novalidate"><input
            name="_token" type="hidden" value="N9uvTlGZlkakDMowbKhNbUU8FSu1XjOMz1R0vQ9Z">
        @csrf @method('PUT')
        <div role="alert" class="alert alert-warning bg-warning text-white approve-product-warning">
            <div class="d-flex gap-1">
                <div>
                    <svg class="icon alert-icon text-white svg-icon-ti-ti-alert-circle" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>
                <div class="w-100">
                    {{ trans('core/base::layouts.start_arlet_admin-store-to-user') }} <a href="" target="_blank"
                        rel="noreferrer noopener">{{ $store->name }}</a>.
                    {{ trans('core/base::layouts.end_arlet_admin-store-to-user') }}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="gap-3 col-md-9">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="form-body">
                            <div class="mb-3 position-relative">
                                <div id="bank-info" class="mb-3 row">
                                    <div class="col-12 col-md-6">
                                        <div class="ship-infor-head">
                                            <h3>{{ trans('core/base::layouts.shipment-infor') }}</h3>
                                        </div>
                                        <div class="ship-infor-item">
                                            <h4>{{ trans('core/base::layouts.order-code') }}</h4>
                                            <p>{{ $shipment->order->code }}</p>
                                        </div>
                                        <div class="ship-infor-item">
                                            <h4>{{ trans('core/base::layouts.warehouse-shiped') }}</h4>
                                            <p>{{ $store->name }}</p>
                                            <p>{{ $store->email }}</p>
                                            <p>{{ $store->phone }}</p>
                                        </div>
                                        <div class="ship-infor-item">
                                            <h4>{{ trans('core/base::layouts.order-amount') }}</h4>
                                            <p>{{ format_price($shipment->order->payment->amount) }}</p>
                                        </div>
                                        <div class="ship-infor-item">
                                            <h4>{{ trans('core/base::layouts.ship-completed-date') }}</h4>
                                            <p>{{ date_format($shipment->date_shipped, 'H:i d-m-Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="ship-infor-head">
                                            <h3>{{ trans('core/base::layouts.status-confirm-shipment') }}</h3>
                                        </div>
                                        <div class="ship-infor-item">
                                            <h4>{{ trans('core/base::layouts.warehouse') }}</h4>
                                            <p>
                                                {{ __('Name') }} {{ trans('core/base::layouts.warehouse') }}:
                                                {{ $store->name }}
                                            </p>
                                            <p>
                                                {{ trans('core/base::layouts.ship-completed-date') }}:
                                                {{ date_format($shipment->date_shipped, 'H:i d-m-Y') }}
                                            </p>
                                            <p>
                                                {{ trans('core/base::layouts.shipping-fee') }}:
                                                {{ format_price($confirmVendorShiped->shipping_fee) }}
                                            </p>
                                            <p>
                                                @php
                                                    $statusLabels = [
                                                        '0' => 'core/base::layouts.pending',
                                                        '1' => 'core/base::layouts.approved',
                                                        '2' => 'core/base::layouts.rejected',
                                                    ];

                                                    $statusClasses = [
                                                        '0' => 'bg-warning text-warning-fg',
                                                        '1' => 'bg-success text-success-fg',
                                                        '2' => 'bg-danger text-danger-fg',
                                                    ];
                                                @endphp
                                                {{ trans('core/base::layouts.status') }}:
                                                <span
                                                    class="badge {{ $statusClasses[$confirmVendorShiped->status] ?? 'bg-secondary text-secondary-fg' }}">
                                                    {{ trans($statusLabels[$confirmVendorShiped->status] ?? 'Unknown') }}
                                                </span>
                                            </p>
                                            <p>
                                                @php
                                                    $isConfirmed = !is_null($shipment->customer_delivered_confirmed_at);

                                                    $statusLabel = $isConfirmed
                                                        ? 'core/base::layouts.confirmed'
                                                        : 'core/base::layouts.unconfirmed';
                                                    $statusClass = $isConfirmed
                                                        ? 'bg-success text-success-fg'
                                                        : 'bg-warning text-warning-fg';
                                                @endphp

                                                {{ trans('core/base::layouts.customer_delivered_confirmed_at') }}:
                                                <span class="badge {{ $statusClass }}">
                                                    {{ trans($statusLabel) }}
                                                </span>
                                            </p>
                                            <div class="mb-3 position-relative">

                                                <label class="form-label" for="description">
                                                    {{ trans('core/base::layouts.note') }}
                                                </label>

                                                <textarea @if ($confirmVendorShiped->status != 0) disabled @endif class="form-control" rows="4"
                                                    placeholder="{{ __('Note') }}" data-counter="400" name="description" cols="50" id="description">{{ $confirmVendorShiped->note }}</textarea>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="mb-3 position-relative">-->

                            <!--    <label class="form-label" for="images[]">-->
                            <!--        {{ trans('core/base::layouts.image') }}-->
                            <!--    </label>-->


                            <!--    <div class="gallery-images-wrapper list-images form-fieldset">-->
                            <!--        <div class="images-wrapper mb-2">-->
                            <!--            <div data-bb-toggle="gallery-add"-->
                            <!--                class="text-center cursor-pointer default-placeholder-gallery-image"-->
                            <!--                data-name="images[]">-->
                            <!--                <div class="mb-3">-->
                            <!--                    <svg class="icon icon-md text-secondary svg-icon-ti-ti-photo-plus"-->
                            <!--                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"-->
                            <!--                        viewBox="0 0 24 24" fill="none" stroke="currentColor"-->
                            <!--                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">-->
                            <!--                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>-->
                            <!--                        <path d="M15 8h.01"></path>-->
                            <!--                        <path-->
                            <!--                            d="M12.5 21h-6.5a3 3 0 0 1 -3 -3v-12a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v6.5">-->
                            <!--                        </path>-->
                            <!--                        <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l4 4"></path>-->
                            <!--                        <path d="M14 14l1 -1c.67 -.644 1.45 -.824 2.182 -.54"></path>-->
                            <!--                        <path d="M16 19h6"></path>-->
                            <!--                        <path d="M19 16v6"></path>-->
                            <!--                    </svg>-->
                            <!--                </div>-->
                            <!--                <p class="mb-0 text-body">-->
                            <!--                    {{ trans('core/base::layouts.click_add_image') }}-->
                            <!--                </p>-->
                            <!--            </div>-->
                            <!--            <input name="images[]" type="hidden">-->
                            <!--            <div class="row w-100 list-gallery-media-images hidden ui-sortable"-->
                            <!--                data-name="images[]" data-allow-thumb="1" style="">-->
                            <!--            </div>-->
                            <!--        </div>-->
                            <!--        <div style="display: none;" class="footer-action">-->
                            <!--            <a data-bb-toggle="gallery-add" href="#" class="me-2 cursor-pointer">Add-->
                            <!--                Images</a>-->
                            <!--            <a href="#" class="text-danger" data-bb-toggle="gallery-reset">-->
                            <!--                Reset-->
                            <!--            </a>-->
                            <!--        </div>-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ trans('core/base::layouts.publish') }}
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="btn-list">
                            <button @if ($confirmVendorShiped->status != 0) disabled @endif class="btn btn-primary"
                                type="submit" value="apply" name="submitter">
                                <svg class="icon icon-left svg-icon-ti-ti-device-floppy"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2">
                                    </path>
                                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M14 4l0 4l-6 0l0 -4"></path>
                                </svg>
                                {{ trans('core/base::layouts.save') }}

                            </button>

                            <button @if ($confirmVendorShiped->status != 0) disabled @endif class="btn" type="submit"
                                name="submitter_exit" value="save">
                                <svg class="icon icon-left svg-icon-ti-ti-transfer-in" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M4 18v3h16v-14l-8 -4l-8 4v3"></path>
                                    <path d="M4 14h9"></path>
                                    <path d="M10 11l3 3l-3 3"></path>
                                </svg>
                                {{ trans('core/base::layouts.save') }} &amp; {{ trans('core/base::layouts.exit') }}

                            </button>

                            <button @if ($confirmVendorShiped->status != 0) disabled @endif class="btn btn-danger"
                                type="submit" value="apply" name="submit_cancel">
                                <svg class="icon icon-left svg-icon-ti-ti-device-floppy"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2">
                                    </path>
                                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M14 4l0 4l-6 0l0 -4"></path>
                                </svg>
                                {{ trans('core/base::layouts.reject') }}

                            </button>


                        </div>
                    </div>
                </div>

                <div data-bb-waypoint="" data-bb-target="#form-actions"></div>

                <header class="top-0 w-100 position-fixed end-0 z-1000" id="form-actions" style="display: none;">
                    <div class="navbar">
                        <div class="container-xl">
                            <div class="row g-2 align-items-center w-100">
                                <div class="col">
                                    <div class="page-pretitle">
                                        <nav aria-label="breadcrumb">
                                            <ol class="breadcrumb">
                                            </ol>
                                        </nav>

                                    </div>
                                </div>
                                <div class="col-auto ms-auto d-print-none">
                                    <div class="btn-list">
                                        <button class="btn btn-primary" type="submit" value="apply" name="submitter"
                                            @if ($confirmVendorShiped->status != 0) disabled @endif>
                                            <svg class="icon icon-left svg-icon-ti-ti-device-floppy"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2">
                                                </path>
                                                <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                                <path d="M14 4l0 4l-6 0l0 -4"></path>
                                            </svg>
                                            {{ trans('core/base::layouts.request') }}

                                        </button>

                                        <button class="btn" type="submit" name="submitter" value="save"
                                            @if ($confirmVendorShiped->status != 0) disabled @endif>
                                            <svg class="icon icon-left svg-icon-ti-ti-transfer-in"
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M4 18v3h16v-14l-8 -4l-8 4v3"></path>
                                                <path d="M4 14h9"></path>
                                                <path d="M10 11l3 3l-3 3"></path>
                                            </svg>
                                            {{ trans('core/base::layouts.save') }} &amp;
                                            {{ trans('core/base::layouts.exit') }}

                                        </button>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>



                <div class="card meta-boxes">
                    <div class="card-header">
                        <h4 class="card-title">
                            <label class="form-label form-label required" for="status">
                                {{ trans('core/base::layouts.status') }}


                            </label>
                        </h4>
                    </div>


                    <div class=" card-body">
                        <select @if ($confirmVendorShiped->status != 0) disabled @endif class="form-control form-select"
                            required="required" id="status-select-65825" name="status" aria-required="true">
                            <option @selected($confirmVendorShiped->status == 0) value="0">{{ trans('core/base::layouts.pending') }}
                            </option>
                            <option @selected($confirmVendorShiped->status == 1) value="1">
                                {{ trans('core/base::layouts.completed') }}</option>
                            <option @selected($confirmVendorShiped->status == 2) value="2">
                                {{ trans('core/base::layouts.cancelled') }}</option>
                        </select>

                    </div>
                </div>

            </div>
        </div>

    </form>
@endsection

@push('footer')
@endpush
