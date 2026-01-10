@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('title', trans('core/base::layouts.referrals'))

@section('content')
  <div id="user-tree">
    @if ($status)
     @foreach ($rootUsers as $rootUser)

      <div class="user level-0" data-id="{{ $rootUser->id }}" data-level="0">
        <i class="folder-icon fas fa-folder"></i>

        <div class="user-main">
          <strong>@lang('core/base::layouts.level') 0: {{ $rootUser->name }} (ID: {{ $rootUser->id }}) - {{ $rootUser->email }}</strong>
          <div class="user-info">
            @lang('core/base::layouts.phone') {{ $rootUser->phone }} |
            @lang('core/base::layouts.created_at'): {{ $rootUser->created_at->format('d/m/Y') }} |
            @lang('core/base::layouts.total_dowline'): {{ format_price($rootUser?->total_dowline) }} |
            @lang('core/base::layouts.total_dowline_on_rank'):{{ format_price($rootUser?->total_dowline_on_rank) }} |
            @lang('core/base::layouts.total_dowline_on_month'):{{ format_price($rootUser?->total_dowline_month) }} |
            @lang('core/base::layouts.walet1'): {{ format_price($rootUser?->walet_1) }} |
            @lang('core/base::layouts.walet2'): {{ format_price($rootUser?->walet_2) }} |
            @lang('core/base::layouts.rank'): <img src="{{ $rootUser?->rank?->rank_icon ? asset($rootUser?->rank?->rank_icon) : asset('storage/rank/norank.png') }}" width="18px" height="18px"
              class="rounded-circle rank-icon" /> {{ $rootUser?->rank?->rank_name ?? trans('core/base::layouts.no') }}
          </div>
        </div>
        <i class="toggle-icon fas fa-chevron-right"></i>
      </div>
    @endforeach
    @else
      <p class="text-danger">@lang('core/base::layouts.not_found_user_root')</p>
    @endif
  </div>

  <style>
    .user {
      margin: 2px 0;
      padding: 5px 8px;
      border-radius: 5px;
      background: linear-gradient(135deg, #ffffff, #f1f3f5);
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: all 0.3s ease;
      border: 1px solid #dee2e6;
      position: relative;
      z-index: 1;
    }

    .user:hover {
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      background: linear-gradient(135deg, #f1f3f5, #ffffff);
      z-index: 2;
    }

    .user.level-0 {
      background: linear-gradient(135deg, #ffecd2, #ffb199);
    }

    .user.level-1 {
      background: linear-gradient(135deg, #d4fc79, #96e6a1);
    }

    .user.level-2 {
      background: linear-gradient(135deg, #a1c4fd, #c2e9fb);
    }

    .user.level-3 {
      background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    }

    .user.level-4 {
      background: linear-gradient(135deg, #e0d4fc, #b9a3e3);
    }

    /* Tím nhạt */
    .user.level-5 {
      background: linear-gradient(135deg, #fce4ec, #f8bbd0);
    }

    /* Hồng nhạt */
    .user.level-6 {
      background: linear-gradient(135deg, #fff9c4, #fff59d);
    }

    /* Vàng nhạt */
    .user.level-7 {
      background: linear-gradient(135deg, #b3e5fc, #81d4fa);
    }

    /* Xanh lam nhạt */
    .user[class*="level-"][class*="level-8"],
    /* Mặc định cho level 8 trở lên */
    .user[class*="level-"][class*="level-9"],
    .user[class*="level-"][class*="level-1"][class*="0"],
    .user[class*="level-"][class*="level-1"][class*="1"],
    .user[class*="level-"][class*="level-1"][class*="2"],
    .user[class*="level-"][class*="level-1"][class*="3"],
    .user[class*="level-"][class*="level-1"][class*="4"],
    .user[class*="level-"][class*="level-1"][class*="5"],
    .user[class*="level-"][class*="level-1"][class*="6"],
    .user[class*="level-"][class*="level-1"][class*="7"],
    .user[class*="level-"][class*="level-1"][class*="8"],
    .user[class*="level-"][class*="level-1"][class*="9"] {
      background: linear-gradient(135deg, #e0e0e0, #bdbdbd);
      /* Xám nhạt cho các level cao hơn */
    }

    .children {
      margin-left: 20px;
      border-left: 2px dashed #dee2e6;
      padding-left: 6px;
    }

    .loading {
      color: #6c757d;
      margin-left: 8px;
      font-style: italic;
    }

    .folder-icon,
    .user-icon {
      margin-right: 5px;
      font-size: 0.9em;
      color: #495057;
    }

    .user-main {
      flex: 1;
      position: relative;
    }

    .user-main strong {
      font-size: 0.85em;
    }

    .user-info {
      display: none;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translateY(-50%);
      margin-left: 10px;
      background: #fff;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      padding: 6px;
      font-size: 0.75em;
      color: #495057;
      line-height: 1.4;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      z-index: 100;
      max-width: 300px;
      white-space: normal;
    }

    .user:hover .user-info {
      display: block;
    }

    .rank-icon {
      margin-right: 5px;
      border: 1px solid #fff;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .toggle-icon {
      margin-left: 6px;
      font-size: 0.7em;
      color: #6c757d;
    }
  </style>
@endsection

@push('style-lib')
@endpush

@push('js')
  <script>
    $(document).ready(function() {
      $(document).on('click', '.user', function(e) {
        e.stopPropagation();
        const $user = $(this);
        const parentId = $user.data('id');
        const level = parseInt($user.data('level')) + 1;
        const $children = $user.next('.children');
        const $toggleIcon = $user.find('.toggle-icon');

        if ($children.length) {
          $children.slideToggle(200);
          $toggleIcon.toggleClass('fa-chevron-down fa-chevron-right');
          return;
        }

        const $loading = $('<div>').addClass('loading').text('Đang tải...');
        $user.after($loading);

        $.ajax({
          url: '{{ route('children_referrals') }}',
          method: 'GET',
          data: {
            parent_id: parentId,
            level: level
          },
          success: function(html) {
            $loading.remove();
            if (html) {
              const $childrenContainer = $('<div>').addClass('children').html(html).hide();
              $user.after($childrenContainer);
              $childrenContainer.slideDown(200);
              $toggleIcon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
            }
          },
          error: function(xhr, status, error) {
            $loading.remove();
            console.error('Lỗi khi lấy danh sách con:', error);
          }
        });
      });
    });
  </script>
@endpush
