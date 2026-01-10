@php
    PageTitle::setTitle(__('403 Access Denied'));
@endphp

@extends('core/base::errors.master')

@section('content')
    <div class="empty">
        <div class="empty-header">403</div>
        <p class="empty-title">{{ __('Access Denied') }}</p>
        <p class="empty-subtitle text-secondary">
            {{ __('You are not authorized to access this page. Please contact the administrator if you believe this is a mistake.') }}
        </p>
        <div class="empty-action">
            <x-core::button
                tag="a"
                href="{{ route('dashboard.index') }}"
                color="primary"
                icon="ti ti-arrow-left"
            >
                {{ __('Take me home') }}
            </x-core::button>
        </div>
    </div>
@endsection
