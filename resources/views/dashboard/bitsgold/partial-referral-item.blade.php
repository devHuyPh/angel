@foreach ($referrals as $referral)
    @php
        $children = $referral->referrers;
        $childCount = $children ? $children->count() : 0;
        $hasChildren = $childCount > 0;
    @endphp

    <div class="referral-node referral-node--leaf referral-node--nested">
        <div class="referral-node__summary referral-node__summary--static">
            <div class="referral-node__content">
                <div class="referral-card__header">
                    <div>
                        <div class="referral-card__name">{{ $referral->name }}</div>
                        <div class="referral-card__meta">
                            @if ($referral->email)
                                <span>{{ $referral->email }}</span>
                            @endif
                            @if ($referral->phone)
                                <span>{{ $referral->phone }}</span>
                            @endif
                        </div>
                    </div>

                    @if (! empty($referral->rank))
                        <div class="referral-card__badge">
                            <img src="{{ asset($referral->rank->rank_icon) }}"
                                alt="{{ $referral->rank->rank_name }}" loading="lazy">
                            <span>{{ $referral->rank->rank_name }}</span>
                        </div>
                    @endif
                </div>

                <div class="referral-card__grid">
                    <div class="referral-card__item">
                        <span class="referral-card__label">@lang('plugins/marketplace::marketplace.reference')</span>
                        <span class="referral-card__value">{{ optional($referral->referrer)->name }}</span>
                    </div>
                    <div class="referral-card__item">
                        <span class="referral-card__label">@lang('core/base::layouts.total_dowline')</span>
                        <span class="referral-card__value">{{ format_price($referral->total_dowline) }}</span>
                    </div>
                    <div class="referral-card__item">
                        <span class="referral-card__label">@lang('plugins/marketplace::marketplace.datejoined')</span>
                        <span class="referral-card__value">{{ $referral->created_at }}</span>
                    </div>
                </div>

                <div class="referral-node__count">
                    {{ number_format($childCount) }} @lang('core/base::layouts.referrals')
                </div>
            </div>
        </div>
    </div>
@endforeach
