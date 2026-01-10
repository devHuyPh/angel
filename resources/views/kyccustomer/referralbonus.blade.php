@extends('plugins/marketplace::themes.bitsgold-dashboard.layouts.master')

@section('content')
    <div class="container py-5">
        <!-- Tiêu đề -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="text-primary mb-3">{{ trans('core/base::layouts.reward_history_title') }}</h2>
                <p class="text-muted">{{ trans('core/base::layouts.reward_history_subtitle') }}</p>
            </div>
        </div>

        <!-- Danh sách thông báo phần thưởng -->
        <div class="row justify-content-center">
            <div class="col-md-10">
                @forelse ($rewards as $reward)
                    <div class="notification-item mb-4 p-4 bg-light border rounded shadow-sm">
                        <div class="d-flex align-items-start">
                            <!-- Icon phần thưởng -->
                            <div class="notification-icon me-4">
                                @if ($reward->reward_type === 'money')
                                    <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                                @elseif ($reward->reward_type === 'discount')
                                    <i class="fas fa-tags fa-3x text-primary"></i>
                                @else
                                    <i class="fas fa-gift fa-3x text-info"></i>
                                @endif
                            </div>

                            <!-- Nội dung thông báo -->
                            <div class="notification-content flex-grow-1">
                                <!-- Loại và giá trị phần thưởng -->
                                <h5 class="mb-2">
                                    {{ trans('core/base::layouts.reward_type_' . $reward->reward_type) }}
                                    <span class="text-primary">
                                        @if ($reward->reward_type === 'money')
                                            {{ format_price($reward->reward_value, 0) }} 
                                        @elseif ($reward->reward_type === 'discount')
                                            {{ $reward->reward_value }}%
                                        @else
                                            {{ $reward->reward_value }}
                                        @endif
                                    </span>
                                </h5>

                                <!-- Mô tả phần thưởng -->
                                <p class="text-muted mb-2">
                                    <strong>{{ trans('core/base::layouts.description') }}:</strong> {{ $reward->description }}
                                </p>

                                <!-- Ngày nhận -->
                                <p class="text-muted mb-2">
                                    <strong>{{ trans('core/base::layouts.received_at') }}:</strong> {{ $reward->created_at->format('d/m/Y H:i') }}
                                </p>

                                <!-- Thông tin bổ sung (giả định) -->
                                @if ($reward->reward_type === 'discount')
                                    <p class="text-muted small mb-2">
                                        <strong>{{ trans('core/base::layouts.expiry_date') }}:</strong>
                                        {{ $reward->expiry_date ? $reward->expiry_date->format('d/m/Y') : trans('core/base::layouts.no_expiry') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Hành động -->
                            <!--@if ($reward->reward_type === 'discount')-->
                            <!--    <div class="notification-action ms-3">-->
                            <!--        <button class="btn btn-sm btn-outline-primary copy-code-btn"-->
                            <!--                data-code="{{ $reward->code }}"-->
                            <!--                onclick="navigator.clipboard.writeText('{{ $reward->code }}')">-->
                            <!--            <i class="fas fa-copy me-1"></i>-->
                            <!--            {{ trans('core/base::layouts.copy_code') }} ({{ $reward->code }})-->
                            <!--        </button>-->
                            <!--    </div>-->
                            <!--@endif-->
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <div class="alert alert-info" role="alert">
                            {{ trans('core/base::layouts.no_rewards_found') }}
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Phân trang -->
        @if ($rewards->hasPages())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $rewards->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- CSS tùy chỉnh -->
    <style>
        .notification-item {
            transition: background-color 0.3s ease, transform 0.3s ease;
            border-radius: 10px;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }

        .notification-icon i {
            opacity: 0.9;
        }

        .notification-content h5 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }

        .notification-content .text-primary {
            font-weight: 500;
        }

        .notification-content .text-muted {
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .copy-code-btn {
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .copy-code-btn:hover {
            background-color: #007bff;
            color: #fff;
        }

        .text-primary {
            color: #007bff !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        @media (max-width: 768px) {
            .notification-content h5 {
                font-size: 1.1rem;
            }

            .notification-content .text-muted {
                font-size: 0.9rem;
            }

            .copy-code-btn {
                font-size: 0.85rem;
            }

            .notification-icon i {
                font-size: 2rem;
            }
        }
    </style>
@endsection