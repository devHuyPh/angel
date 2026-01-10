<div
    class="address-item @if ($address->is_default) is-default @endif"
    data-id="{{ $address->id }}"
>
    <p class="name">{{ $address->name }}</p>

    @php
        $fullAddress = $address->full_address;
    @endphp
    <p
        class="address"
        title="{{ $fullAddress }}"
    >
        {{ $fullAddress }}
    </p>
    <p class="phone">{{ __('Phone') }}: {{ $address->phone }}</p>
    @if ($address->email)
        <p class="email">{{ __('Email') }}: {{ $address->email }}</p>
    @endif
    @if ($address->is_default)
        <span class="default">{{ __('Default') }}</span>
    @endif
</div>
