@extends(EcommerceHelper::viewPath('customers.master'))

<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    .total-rank{
        color:black;
        font-size:16px;
    }
            @media (max-width: 767.98px) {

                .bg-custom-moblie {
                    padding: 0 !important;
                }

                .profile__tab-content {
                    padding: 0 !important;
                }

                .form-control {
                    font-size: 16px !important;
                }

                .card-body-dasboard-mobile {
                    padding: 10px 0px !important;
                }

                .icon-dashboard-mobile {
                    width: 30px !important;
                    height: 30px !important;
                    font-size: 15px !important;
                }

                .h6-dashboard-mobile {
                    font-size: 12px !important;
                    text-align: center !important;
                }

                .h4-dashboard-mobile {
                    font-size: 14px !important;
                    /* padding-top: 10px !important; */
                    /* margin-left: 10px !important; */
                }

                .text-content-row-item-dashboard-mobile {
                    display: flex !important;
                    flex-direction: column !important;
                }

                .h3-mobile-dashboard {
                    font-size: 16px !important;
                    background: #f8f8f8;
                    padding: 0.5rem 0 0.5rem 10px !important;
                }

                .h5-mobile-dashboard {
                    font-size: 16px !important;
                }

                .card-dashboard-mobile {
                    border-radius: 0 !important;
                }
                .total-rank{
                    font-size:12px;
                }
                .card-text{
                    text-align: center;
                }
                .text-rank{
                    font-size:10px;
                    margin-left:10px;
                }
			}

        </style>
      <style>
    .glow-green {
        background: linear-gradient(270deg, #4BA113, #38761D);
        background-size: 400% 400%;
        animation: greenFlow 1s ease infinite;
        color: white;
        box-shadow: 0 0 20px rgba(72, 180, 97, 0.6);
        transition: background 0.3s ease;
    }

    @keyframes greenFlow {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }
</style>



@section('content')
      <div class="group-sticky">
          <div class="header d-flex d-md-none align-items-center mb-3 bg-white py-2 px-3"
              style="position: fixed; top: 0; left: 0; right: 0; z-index: 1050;">
              <a href="{{ route('setting') }}" class="back-btn text-success">
                  <i class="bi bi-chevron-left"></i>
              </a>
              <h1 class="header-title text-success">{{ __('Bảng điều khiển Marketing') }}</h1>
          </div>
          @include('notification_alert.active_account')

          <section class="">
              <div class="container">
                  <div class="row g-2 g-md-3">


  {{-- Link xem toàn bộ lịch sử ví --}}
  <div class="col-12">
    <a class="text-success text-decoration-underline small" href="{{ route('bitsgold.wallet_history') }}">Xem tất cả tiền
      vào các ví</a>
  </div>

                      {{-- Ví rút --}}
                      <div class="col-6 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-wallet text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.main_wallet_1')</h6>
                                      <h4 class="card-text mb-0 h4-dashboard-mobile">{{ format_price($customer->walet_1) }}
                                      </h4>
                                  </div>
                              </div>
                          </div>
                      </div>
                      {{-- ví tiêu dùng --}}
                      <div class="col-6 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-wallet text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.main_wallet_2')</h6>
                                      <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($customer->walet_2) }}
                                      </h4>
                                  </div>
                              </div>
                          </div>
                      </div>


                      {{-- Thu nhập thực tế --}}
                      <div class="col-6 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-coins text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.actual_income')</h6>
                                      <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($totalAmount) }}</h4>
                                  </div>
                              </div>
                          </div>
                      </div>
                      {{-- Thu nhập giao đơn --}}
                      <div class="col-6 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-truck text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">Thu nhập giao đơn</h6>
                                      <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($shippingIncome) }}</h4>
                                  </div>
                              </div>
                          </div>
                      </div>
                      {{-- Hoa hồng kho --}}
                      <div class="col-6 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-coins text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.warehouse_referral_commission')</h6>
                                      <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($customer->total_warehouse_referral) }}</h4>
                                  </div>
                              </div>
                          </div>
                      </div>

                      {{-- Hoa hồng trực tiếp --}}
                      <div class="col-6 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-coins text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.referral_commission')</h6>
                                      <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($referralCommission) }}</h4>
                                  </div>
                              </div>
                          </div>
                      </div>

                       {{-- Hoa hồng đồng chia --}}
                      <div class="col-6 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-coins text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.rose_split')</h6>
                                      <!-- <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($totalAmount) }}</h4> -->
                                      @if($rewardsByRank)
                                          @foreach($rewardsByRank as $item)
                                          <div class="d-flex align-items-center text-rank">
                                              <i class="fas fa-medal me-1"></i>
                                              <small class="fw-bold">{{ $item['rank_name'] }}: <span class="total-rank">{{ format_price($item['total']) }}</span> </small> </br>
                                          </div>
                                          @endforeach
                                      @else
                                          <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price(0) }}</h4>
                                      @endif
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- {{-- Quỹ cộng đồng --}}

                      {{--<div class="col-4 col-sm-6 col-lg-6">
                          <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                              <div
                                  class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                  <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                      style="width: 60px; height: 60px;">
                                      <i class="fas fa-users text-white fs-4"></i>
                                  </div>
                                  <div class="row">
                                      <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.community_fund')</h6>
                                      <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($community_sharing) }}
                                      </h4>
                                  </div>
                              </div>
                          </div>
                      </div>--}} -->

                      <!-- {{-- Các quỹ cấp bậc --}}
                      @foreach ($results as $result)
                          <div class="col-4 col-sm-6 col-lg-6">
                              <div class="card h-100 text-white border-0 rounded-3 shadow" style="background-color: #4BA213;">
                                  <div
                                      class="card-body d-flex flex-column flex-sm-row justify-content-start align-items-center gap-3 card-body-dasboard-mobile">
                                      <div class="p-3 rounded-circle bg-white bg-opacity-25 d-flex justify-content-center align-items-center icon-dashboard-mobile"
                                          style="width: 60px; height: 60px;">
                                          @if (!empty($result['rank_icon']))
                                              <img src="{{ asset($result['rank_icon']) }}" alt="Rank Icon"
                                                  style="width: 40px; height: 40px; object-fit: contain;">
                                          @else
                                              <i class="fas fa-trophy text-white fs-4"></i>
                                          @endif
                                      </div>
                                      <div class="row">
                                          <h6 class="card-title mb-1 h6-dashboard-mobile">@lang('plugins/marketplace::marketplace.fund')
                                              {{ $result['name'] }}</h6>
                                          <h4 class="card-text mb-0 h4-dashboard-mobile text-left">{{ format_price($result['result']) }}
                                          </h4>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endforeach -->
                  </div>
              </div>
          </section>

          {{-- Rank hiện tại --}}
          <div class="card my-4 shadow-sm card-dashboard-mobile">
              <div class="card-header bg-md-white h3-mobile-dashboard">
                  <h5 class="mb-0 h5-mobile-dashboard text-success">️🏅️ @lang('plugins/marketplace::marketplace.your_current_rank')</h5>
              </div>
              <div class="card-body d-flex">
                  @if ($currentRank)
                          <p class="mb-0">
                              <strong>{{ $currentRank->rank_name }}</strong><br>
                              <small class="text-muted">@lang('plugins/marketplace::marketplace.levell'):
                                  {{ $currentRank->rank_lavel }}</small>
                          </p>

                  @else
                      <p class="text-muted">@lang('plugins/marketplace::marketplace.you_have_no_rank_yet')</p>
                  @endif
              </div>
          </div>

          {{-- Tiến độ lên cấp --}}
          @if ($progressToNextRank)
              <div class="card shadow-sm mb-4 card-dashboard-mobile">
                  <div class="card-header bg-md-white h3-mobile-dashboard">
                      <h5 class="mb-0 h5-mobile-dashboard text-success">🚀 @lang('plugins/marketplace::marketplace.progress_to_next_level') :
                          <strong>{{ $progressToNextRank['rank'] }}</strong>
                      </h5>
                  </div>
                  <div class="card-body">
                      @if($progressToNextRank['required_referrals'] !== 0)
                          {{-- Số người giới thiệu --}}
                          <p>@lang('plugins/marketplace::marketplace.number_of_friends_referred'): <strong>{{ $referralCount }}</strong> /
                              {{ $progressToNextRank['required_referrals'] }}</p>
                          <div class="progress mb-3" style="height: 24px;">
                              <div class="progress-bar" role="progressbar"
                                  style="width: {{ $progressToNextRank['referral_progress'] }}%; background-color: #4BA213"
                                  aria-valuenow="{{ $progressToNextRank['referral_progress'] }}" aria-valuemin="0"
                                  aria-valuemax="100">
                                  {{ $progressToNextRank['referral_progress'] }}%
                              </div>
                          </div>
                      @endif

                      {{-- Doanh thu tuyến dưới --}}
                      <p>@lang('plugins/marketplace::marketplace.downline_revenue_for_the_month') <strong>{{ $progressToNextRank['ranking_date_conditions'] }}</strong> @lang('plugins/marketplace::marketplace.day'): <br>
                          <strong>{{ format_price($totalDownlineMonthbyuser) }}</strong> /
                          {{ format_price($progressToNextRank['required_revenue']) }}
                      </p>
                      <div class="progress" style="height: 24px;">
                          <div class="progress-bar" role="progressbar"
                              style="width: {{ $progressToNextRank['revenue_progress'] }}%; background-color: #4BA213"
                              aria-valuenow="{{ $progressToNextRank['revenue_progress'] }}" aria-valuemin="0"
                              aria-valuemax="100">
                              {{ $progressToNextRank['revenue_progress'] }}%
                          </div>
                      </div>
                  </div>
              </div>
          @else
              <div class="alert alert-success mt-4">
                  🎉 @lang('plugins/marketplace::marketplace.congratulations')
              </div>
          @endif
                      <div class="card my-4 shadow-sm card-dashboard-mobile">
          <div class="card-header bg-md-white h3-mobile-dashboard">
              <h5 class="mb-0 h5-mobile-dashboard text-success">
                  @lang('core/base::layouts.spend_this_month')
              </h5>
          </div>
          <div class="card-body">
              @if ($totalSpendMonth)
               <p> 💰 @lang('core/base::layouts.spend_the_month'): <br>
                  <strong>{{ format_price($totalSpendMonth) }}</strong> /
                  {{ format_price($total) }}
              </p>
              @php
    $isCompleted = $totalSpendMonth >= $total;
  @endphp

            <div class="progress">
      <div class="progress-bar {{ $isCompleted ? 'glow-green' : 'bg-success' }}"
           role="progressbar"
           style="width: {{ $totalProgress }}%"
           aria-valuenow="{{ $totalProgress }}"
           aria-valuemin="0"
           aria-valuemax="100">
          {{ format_price($totalSpendMonth) }} / {{ format_price($total) }}
      </div>
  </div>

              @else
              <p class="text-muted">Bạn chưa có chi tiêu trong tháng này.</p>
              @endif
          </div>

      </div>
      </div>
@endsection

@push('footer')
    <script>
        'use strict';

        var BotbleVariables = BotbleVariables || {};
        BotbleVariables.languages = BotbleVariables.languages || {};
        BotbleVariables.languages.reports = {!! json_encode(trans('plugins/ecommerce::reports.ranges'), JSON_HEX_APOS) !!}
    </script>
@endpush
