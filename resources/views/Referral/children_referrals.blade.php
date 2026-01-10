@foreach ($users as $user)
  <div class="user level-{{ $level }}" data-id="{{ $user->id }}" data-level="{{ $level }}">
    <i class="user-icon fas fa-user"></i>

    <div class="user-main">
      <strong>@lang('core/base::layouts.level') {{ $level }}: {{ $user->name }} (ID: {{ $user->id }}) - {{ $user->email }}</strong>
      <div class="user-info">
          @lang('core/base::layouts.phone') {{ $user->phone }} |
            @lang('core/base::layouts.created_at'): {{ $user->created_at->format('d/m/Y') }} |
            @lang('core/base::layouts.total_doawline'): {{ format_price($user?->total_dowline) }} |
            @lang('core/base::layouts.total_dowline_on_rank'):{{ format_price($user?->total_dowline_on_rank) }} |
            @lang('core/base::layouts.total_dowline_on_month'):{{ format_price($user?->total_dowline_month) }} |
            @lang('core/base::layouts.walet1'): {{ format_price($user?->walet_1) }} |
            @lang('core/base::layouts.walet2'): {{ format_price($user?->walet_2) }} |
            @lang('core/base::layouts.rank'): <img src="{{ $user?->rank?->rank_icon ? asset($user?->rank?->rank_icon) : asset('storage/rank/norank.png') }}" width="18px" height="18px"
              class="rounded-circle rank-icon" /> {{ $user?->rank?->rank_name ?? trans('core/base::layouts.no') }}
      </div>
    </div>
    <i class="toggle-icon fas fa-chevron-right"></i>
  </div>
@endforeach
