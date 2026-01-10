@if(!$shipment->ghn_created)
<x-core::card class="mb-3">
    <x-core::card.header>
        <x-core::card.title>
            {{ trans('plugins/ecommerce::shipping.additional_shipment_information') }}
        </x-core::card.title>
    </x-core::card.header>
    <x-core::card.body>
        {!! Botble\Ecommerce\Forms\ShipmentInfoForm::createFromModel($shipment)->renderForm() !!}
    </x-core::card.body>
</x-core::card>
@else
<x-core::card class="mb-3">
    <x-core::card.header>
        {{-- <x-core::card.title>
            <strong>Thông tin đơn hàng</strong>
        </x-core::card.title> --}}
        <div style="width:100%" class="d-flex justify-content-between align-items-center">
            <h4 class="card-title">Thông tin đơn hàng</h4>
            <div>
                <button type="button" class="btn btn-warning" id="ghn-update-order">Chỉnh sửa</button>
                <button type="button" class="btn btn-danger" id="ghnCancel">Hủy đơn GHN</button>
            </div>
        </div>
    </x-core::card.header>
    <x-core::card.body>
        <div id="order-detail"></div>
    </x-core::card.body>
</x-core::card>
@endif
