@extends(EcommerceHelper::viewPath('customers.layouts.account-settings'))

@section('title', __('Account information'))

@section('account-content')
  @php
    $customer = auth('customer')->user();
    $customerMeta = $customer ? ($customer->phone ?: $customer->email) : null;

    $customerName = $customer?->name ?: __('Customer');
    $customerInitial = $customer && trim((string) $customer->name) !== ''
      ? mb_strtoupper(mb_substr(trim((string) $customer->name), 0, 1))
      : 'C';
  @endphp

  @once
    <style>
      /* Trả lại behavior tab tràn full trên mobile */
      @media (max-width: 767.98px) {
        .profile__tab-content {
          padding: 0 !important;
        }
      }

      /* ===== Clean Profile Edit (padding compact) ===== */
      .profile-edit {
        --pe-ink: #1c1b23;
        --pe-muted: #7a7f92;
        --pe-surface: #ffffff;
        --pe-bg: #f6f7fb;

        /* from settings/theme */
        --pe-primary: var(--primary-color, #80279b);
        --pe-primary-rgb: var(--primary-color-rgb, 128, 39, 155);
        --pe-font: var(--primary-font, system-ui, -apple-system, Segoe UI, Roboto, Arial);

        --pe-border: rgba(var(--pe-primary-rgb), 0.14);
        --pe-soft: rgba(var(--pe-primary-rgb), 0.06);

        --pe-radius: 14px;
        --pe-shadow: 0 14px 34px rgba(0, 0, 0, 0.08);

        /* compact header + bottom bar */
        --pe-header-h: 48px;
        --pe-bottom-h: 66px;
        --pe-bottom-menu-h: 66px;

        font-family: var(--pe-font);
        color: var(--pe-ink);
      }

      /* layout padding để tránh đè header/bottom bar */
      .profile-edit__wrap {
        background: transparent;
        padding-top: calc(var(--pe-header-h) + 6px + env(safe-area-inset-top, 0px));
        padding-bottom: calc(var(--pe-bottom-h) + var(--pe-bottom-menu-h) + 10px + env(safe-area-inset-bottom, 0px));
        padding-left: 0;
        padding-right: 0;
      }

      /* padding ngang nhẹ trên mobile cho form/card */
      @media (max-width: 767.98px) {
        .profile-edit__wrap {
          padding-left: 12px;
          padding-right: 12px;
        }
      }

      /* Top header (fixed) */
      .profile-edit__header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--pe-header-h);
        z-index: 1030;
        /* dưới modal/backdrop */
        background: var(--pe-primary);
        color: #fff;
        display: flex;
        align-items: center;
        padding: 0 10px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.10);
      }

      .profile-edit__header .pe-icon-btn {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: rgba(255, 255, 255, 0.10);
        color: #fff;
        text-decoration: none;
        flex: 0 0 auto;
      }

      .profile-edit__header .pe-title {
        margin: 0;
        font-weight: 700;
        letter-spacing: 0.2px;
        font-size: 1.02rem;
        line-height: 1.2;
        flex: 1 1 auto;
        text-align: center;
        padding: 0 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }

      /* Avatar block – giảm khoảng trống trên/dưới */
      .profile-edit__avatar-block {
        display: grid;
        place-items: center;
        padding: 8px 0 4px;
      }

      .profile-edit__avatar {
        width: 80px;
        height: 80px;
        border-radius: 999px;
        overflow: hidden;
        background: rgba(var(--pe-primary-rgb), 0.16);
        border: 3px solid #fff;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.10);
        display: grid;
        place-items: center;
        color: var(--pe-primary);
        font-weight: 800;
      }

      .profile-edit__avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
      }

      /* Card – padding nhỏ lại */
      .profile-edit__card {
        background: var(--pe-surface);
        border-radius: var(--pe-radius);
        border: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
        padding: 12px;
      }

      .profile-edit__section-title {
        font-weight: 800;
        font-size: 1.02rem;
        margin: 0 0 10px;
      }

      /* Form styling – giữ nguyên field, chỉ make đẹp */
      .profile-edit form .form-label,
      .profile-edit form label {
        font-weight: 700;
        color: var(--pe-ink);
        margin-bottom: 6px;
      }

      .profile-edit form .text-danger {
        font-weight: 800;
      }

      .profile-edit form .form-control,
      .profile-edit form .form-select,
      .profile-edit form input[type="text"],
      .profile-edit form input[type="email"],
      .profile-edit form input[type="tel"],
      .profile-edit form input[type="date"],
      .profile-edit form input[type="password"],
      .profile-edit form textarea,
      .profile-edit form select {
        border-radius: 12px !important;
        border: 1px solid rgba(0, 0, 0, 0.12);
        background: #fff;
        padding: 12px 12px;
        box-shadow: none !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
      }

      .profile-edit form .form-control:focus,
      .profile-edit form .form-select:focus,
      .profile-edit form input:focus,
      .profile-edit form textarea:focus,
      .profile-edit form select:focus {
        border-color: rgba(var(--pe-primary-rgb), 0.55) !important;
        box-shadow: 0 0 0 0.18rem rgba(var(--pe-primary-rgb), 0.16) !important;
      }

      .profile-edit form .input-group>.form-control,
      .profile-edit form .input-group>.form-select {
        border-radius: 12px !important;
      }

      .profile-edit form .input-group .input-group-text {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.12);
        background: var(--pe-soft);
        color: var(--pe-ink);
        font-weight: 700;
      }

      .profile-edit .dob-selects {
        background: var(--pe-soft);
        border-radius: 12px;
        padding: 10px 10px 6px;
        border: 1px dashed rgba(var(--pe-primary-rgb), 0.28);
      }

      .profile-edit #dob_preview {
        font-size: 0.84rem;
        font-weight: 800;
        color: var(--pe-muted);
      }

      .profile-edit .form-check {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-right: 16px;
      }

      .profile-edit .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0;
        cursor: pointer;
      }

      .profile-edit .form-check-label {
        margin: 0;
        cursor: pointer;
        font-weight: 700;
        color: var(--pe-ink);
      }

      .profile-edit form button[type="submit"],
      .profile-edit form .btn[type="submit"] {
        display: none !important;
      }

      /* Bottom save bar */
      .profile-edit__bottom {
        position: fixed;
        left: 0;
        right: 0;
        bottom: var(--pe-bottom-menu-h);
        z-index: 1030;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        padding: 10px 12px calc(10px + env(safe-area-inset-bottom, 0px));
      }

      .profile-edit__save-btn {
        width: 100%;
        border: 0;
        border-radius: 14px;
        padding: 12px 14px;
        font-weight: 900;
        letter-spacing: 0.2px;
        color: #fff;
        background: var(--pe-primary);
        box-shadow: 0 14px 24px rgba(var(--pe-primary-rgb), 0.32);
      }

      .profile-edit__danger {
        margin-top: 14px;
        border-radius: var(--pe-radius);
        padding: 14px;
        border: 1px solid rgba(214, 51, 108, 0.30);
        background: rgba(214, 51, 108, 0.05);
      }

      .profile-edit__danger h2 {
        margin: 0 0 8px;
        font-size: 1.02rem;
        font-weight: 900;
      }

      @media (min-width: 992px) {
        .profile-edit__wrap {
          padding-top: 18px;
          padding-bottom: 24px;
        }

        .profile-edit__header,
        .profile-edit__bottom {
          position: static;
          height: auto;
          box-shadow: none;
          background: transparent;
          border: 0;
          padding: 0;
          backdrop-filter: none;
        }

        .profile-edit__header {
          display: none;
        }

        .profile-edit__bottom {
          margin-top: 14px;
        }

        .profile-edit__save-btn {
          width: auto;
          min-width: 220px;
          padding: 12px 18px;
        }

        .profile-edit__desktop-actions {
          display: flex;
          justify-content: flex-end;
        }
      }
    </style>
  @endonce

  <div class="profile-edit">
    {{-- Header mobile --}}
    <div class="profile-edit__header d-lg-none">
      <a href="{{ route('setting') }}" class="pe-icon-btn" aria-label="{{ __('Back') }}">
        <i class="bi bi-chevron-left"></i>
      </a>

      <h1 class="pe-title">{{ __('Chỉnh sửa thông tin') }}</h1>

      <a href="{{ route('setting') }}" class="pe-icon-btn" aria-label="{{ __('Close') }}">
        <i class="bi bi-x-lg"></i>
      </a>
    </div>

    {{-- KHÔNG dùng .container để không ăn padding Bootstrap --}}
    <div class="profile-edit__wrap">
      <div class="profile-edit__avatar-block">
        <div class="profile-edit__avatar" title="{{ $customerName }}">
          @if ($customer && $customer->avatar_url)
            <img src="{{ $customer->avatar_url }}" alt="{{ $customerName }}" loading="lazy">
          @else
            <span>{{ $customerInitial }}</span>
          @endif
        </div>
      </div>

      <div class="profile-edit__card">
        <div class="profile-edit__section-title">{{ __('Thông tin cá nhân') }}</div>
        {!! $form->renderForm() !!}
      </div>

      @if (get_ecommerce_setting('enabled_customer_account_deletion', true))
        <div class="profile-edit__danger">
          <h2 class="text-danger">{{ __('Delete account') }}</h2>

          <p class="mb-3">
            {{ __('This action will permanently delete your account and all associated data and irreversible. Please be sure before proceeding.') }}
          </p>

          <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
            data-bs-target="#delete-account-modal">
            {{ __('Delete your account') }}
          </button>

          <div class="modal fade" id="delete-account-modal" tabindex="-1" aria-labelledby="delete-account-modal-label"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title fs-6" id="delete-account-modal-label">
                    {{ __('Are you sure you want to do this?') }}
                  </h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                  <p class="text-muted">
                    {{ __('We will send you an email to confirm your account deletion. Once you confirm, your account will be deleted permanently.') }}
                  </p>

                  <x-core::form :url="route('customer.delete-account.store')" method="post">
                    <div class="mb-3">
                      <label for="password" class="form-label">{{ __('Confirm your password') }}</label>
                      <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                      <label for="reason" class="form-label">{{ __('Reason (optional)') }}</label>
                      <textarea id="reason" name="reason" class="form-control" rows="3"></textarea>
                    </div>

                    <button type="submit" class="w-100 btn btn-danger">
                      {{ __('Request delete account') }}
                    </button>
                  </x-core::form>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif

      <div class="profile-edit__bottom d-none d-lg-block">
        <div class="profile-edit__desktop-actions">
          <button type="button" class="profile-edit__save-btn" id="pe_save_btn_desktop">
            {{ __('Lưu thay đổi') }}
          </button>
        </div>
      </div>
    </div>

    {{-- Bottom bar mobile --}}
    <div class="profile-edit__bottom d-lg-none">
      <button type="button" class="profile-edit__save-btn" id="pe_save_btn_mobile">
        {{ __('Lưu thay đổi') }}
      </button>
    </div>
  </div>

  @once
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const wrap = document.querySelector('.profile-edit');
        const form = wrap ? wrap.querySelector('form') : null;
        const btnMobile = document.getElementById('pe_save_btn_mobile');
        const btnDesktop = document.getElementById('pe_save_btn_desktop');

        function syncBottomMenuOffset() {
          if (!wrap) return;
          const bottomMenu = document.getElementById('tp-bottom-menu-sticky');
          const height = bottomMenu ? bottomMenu.offsetHeight : 0;
          wrap.style.setProperty('--pe-bottom-menu-h', `${height}px`);
        }

        function submitForm() {
          if (!form) return;
          if (typeof form.reportValidity === 'function' && !form.reportValidity()) return;
          form.submit();
        }

        syncBottomMenuOffset();
        window.addEventListener('resize', syncBottomMenuOffset);

        if (btnMobile) btnMobile.addEventListener('click', submitForm);
        if (btnDesktop) btnDesktop.addEventListener('click', submitForm);

        const daySelect = document.querySelector('select[name="select_day"]');
        const monthSelect = document.querySelector('select[name="select_month"]');
        const yearSelect = document.querySelector('select[name="select_year"]');
        const dobInput = document.getElementById("date_of_birth");
        const dobPreview = document.getElementById("dob_preview");

        if (!daySelect || !monthSelect || !yearSelect || !dobInput) return;

        const pad2 = (v) => String(v).padStart(2, "0");
        const isLeapYear = (y) => (y % 4 === 0 && y % 100 !== 0) || (y % 400 === 0);

        function maxDayOf(month, year) {
          const m = Number(month);
          const y = Number(year);
          if (!Number.isFinite(m) || !Number.isFinite(y) || !m || !y) return 31;
          if (m === 2) return isLeapYear(y) ? 29 : 28;
          return [4, 6, 9, 11].includes(m) ? 30 : 31;
        }

        function updateDayOptions() {
          const maxDay = maxDayOf(monthSelect.value, yearSelect.value);

          Array.from(daySelect.options).forEach(opt => {
            const d = Number(opt.value);
            if (!Number.isFinite(d) || !d) return;
            opt.disabled = d > maxDay;
          });

          const current = Number(daySelect.value);
          if (Number.isFinite(current) && current > maxDay) {
            daySelect.value = String(maxDay);
          }
        }

        function updateDOB() {
          updateDayOptions();

          const d = Number(daySelect.value);
          const m = Number(monthSelect.value);
          const y = Number(yearSelect.value);

          if (!Number.isFinite(d) || !Number.isFinite(m) || !Number.isFinite(y) || !d || !m || !y) {
            dobInput.value = "";
            if (dobPreview) dobPreview.textContent = "";
            return;
          }

          const day = pad2(d);
          const month = pad2(m);

          dobInput.value = `${y}-${month}-${day}`;
          if (dobPreview) dobPreview.textContent = `${day}/${month}/${y}`;
        }

        ["change", "input"].forEach(evt => {
          daySelect.addEventListener(evt, updateDOB);
          monthSelect.addEventListener(evt, updateDOB);
          yearSelect.addEventListener(evt, updateDOB);
        });

        updateDOB();
      });
    </script>
  @endonce
@endsection