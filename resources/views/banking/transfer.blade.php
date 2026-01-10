@extends(EcommerceHelper::viewPath('customers.master'))

@section('title', 'Chuyen tien vi rut')
{{-- resources\views\banking\transfer.blade.php --}}
@section('content')
  <style>
    :root {
      --tone-1: var(--primary-color, #0f9a7f);
      --tone-2: var(--primary-color-dark, #0b5aa3);
      --tone-3: var(--primary-color-light, #0e7c4f);
      --surface: #f8fbfd;
      --card: #ffffff;
      --border: #e6eef5;
    }
    .transfer-hero {
      background: linear-gradient(135deg, var(--tone-1), var(--tone-2));
      color: #fff;
      border-radius: 16px;
      padding: 18px;
      box-shadow: 0 14px 32px rgba(11, 90, 163, 0.22);
    }
    .glass-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(15, 90, 163, 0.06);
    }
    .input-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(15, 154, 127, 0.12);
      color: var(--tone-2);
      border-radius: 999px;
      padding: 6px 12px;
      font-size: 12px;
      font-weight: 600;
      border: 1px solid rgba(15, 154, 127, 0.24);
    }
    .section-title { font-weight: 700; font-size: 15px; margin-bottom: 10px; }
    .helper-text { color: #005e24; font-size: 13px; }.helper-text-ahuy { color: #f9f9f9; font-size: 13px; }
    .qr-box svg { max-width: 220px; width: 100%; height: auto; }
    .tag-pill {
      background: rgba(255,255,255,0.18);
      border: 1px solid rgba(255,255,255,0.28);
      border-radius: 999px;
      padding: 6px 12px;
      font-size: 12px;
  }
  .summary-box {
    border-left: 3px solid var(--tone-3);
    padding-left: 12px;
  }
  .guide-highlight {
    outline: 3px solid rgba(11, 90, 163, 0.35);
    box-shadow: 0 10px 24px rgba(11, 90, 163, 0.15);
    border-radius: 10px;
    transition: outline 0.2s ease, box-shadow 0.2s ease;
    position: relative;
    z-index: 1051;
    isolation: isolate;
  }
  .guide-panel {
    position: fixed;
    right: 18px;
    bottom: 18px;
    z-index: 1050;
    max-width: 320px;
    border: 1px solid var(--border);
    box-shadow: 0 12px 32px rgba(15, 90, 163, 0.2);
  }
  .guide-panel .progress { height: 5px; }
  .guide-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    backdrop-filter: blur(1px);
    z-index: 1049;
    pointer-events: none;
  }
  .flow-hint {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 10px;
    margin-bottom: 12px;
  }
  .flow-step {
    border: 1px dashed var(--border);
    border-radius: 10px;
    padding: 10px 12px;
    background: #f9fbff;
    display: flex;
    gap: 10px;
    align-items: flex-start;
  }
  .flow-step .badge {
    background: var(--tone-1);
  }
  .reset-btn { font-size: 13px; }
  body { background: var(--surface) !important; }
  @media (max-width: 767.98px) {
    .desktop-only { display: none !important; }
    .transfer-hero { padding: 16px; }
  }
  @media (min-width: 768px) {
    .mobile-only { display: none !important; }
  }
</style>

  <div class="mobile-only mb-3">
    <div class="transfer-hero">
      <div class="small opacity-75 mb-1">Số dư ví rút</div>
      <div class="h4 mb-2">{{ format_price($customer->walet_1) }}</div>
      <div class="d-flex gap-2 flex-wrap">
        <span class="tag-pill">Ví rút → Ví rút</span>
        <span class="tag-pill">Bảo mật mật khẩu</span>
      </div>
      <div class="helper-text-ahuy mt-2">Mã/QR của bạn nằm ở mục bên dưới.</div>
    </div>
  </div>

  <div class="container">
    <div class="row g-3 align-items-stretch">
      <div class="col-lg-7">
        <div class="transfer-hero mb-3 desktop-only">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
              <div class="small opacity-75 mb-1">Số dư ví rút</div>
              <div class="h3 mb-2">{{ format_price($customer->walet_1) }}</div>
              <div class="d-flex gap-2 flex-wrap">
                <span class="tag-pill">Ví rút → Ví rút</span>
                <span class="tag-pill">Nhập mật khẩu để xác nhận</span>
              </div>
            </div>
            <div class="text-end helper-text-ahuy text-white-75">
              Mã/QR chia sẻ nằm ở khối “Mã/QR của bạn” bên phải.
            </div>
          </div>
        </div>

      <div class="glass-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="fw-semibold">Hướng dẫn nhanh</div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="start-guide">Bắt đầu</button>
            <button type="button" class="btn btn-sm btn-outline-success" id="speak-guide">
              <i class="bi bi-volume-up"></i> Nghe hướng dẫn
            </button>
          </div>
        </div>
        {{-- <div class="flow-hint">
          <div class="flow-step">
            <span class="badge text-white">1</span>
            <div>
              <div class="fw-semibold small mb-1">Chọn người nhận</div>
              <div class="helper-text">Nhập email hoặc dán mã QR, hệ thống sẽ khóa trường sau khi xác nhận.</div>
            </div>
          </div>
          <div class="flow-step">
            <span class="badge text-white">2</span>
            <div>
              <div class="fw-semibold small mb-1">Nhập số tiền</div>
              <div class="helper-text">Hiện cảnh báo nếu vượt số dư ví rút.</div>
            </div>
          </div>
          <div class="flow-step">
            <span class="badge text-white">3</span>
            <div>
              <div class="fw-semibold small mb-1">Xác nhận</div>
              <div class="helper-text">Nhập mật khẩu đăng nhập để gửi và thông báo cho hai bên.</div>
            </div>
          </div>
        </div> --}}

        <form id="wallet-transfer-form" method="POST" action="{{ route('wallet.transfer.store') }}" class="js-base-form">
          @csrf

            <div class="section-title">Người nhận</div>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label required" for="recipient-email">Email người nhận</label>
                <input type="email" class="form-control" name="email" id="recipient-email"
                       value="{{ old('email', $prefillEmail ?? '') }}" placeholder="vd: user@email.com">
                <div class="helper-text">Nhập email hoặc để trống rồi quét mã QR ở bên dưới.</div>
              </div>
              <div class="col-12">
                <label class="form-label" for="recipient-code">Mã/QR người nhận</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="code" id="recipient-code"
                         value="{{ old('code', request()->query('code')) }}" placeholder="Dán chuỗi QR người nhận">
                  <button class="btn btn-outline-secondary" type="button" id="decode-code-btn">Đọc mã</button>
                </div>
                <div class="helper-text">Dán chuỗi trả về sau khi quét QR của người nhận.</div>
              </div>
              <div class="col-12">
                <div class="alert alert-light border d-flex flex-column gap-1" id="recipient-info-box">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <div class="small text-muted">Người nhận</div>
                      <div class="fw-semibold" id="recipient-name">Chưa xác định</div>
                      <div class="text-muted" id="recipient-email-display"></div>
                    </div>
                    <span class="badge bg-secondary" id="recipient-empty">Chưa có dữ liệu</span>
                  </div>
                </div>
                <div class="text-danger small" id="recipient-error"></div>
                <button type="button" class="btn btn-link px-0 reset-btn" id="reset-recipient-btn">Đổi người nhận</button>
              </div>
            </div>

            <hr class="my-3">

            <div class="section-title">Số tiền & bảo mật</div>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label required" for="amount">Số tiền muốn chuyển</label>
                <input type="number" class="form-control" name="amount" id="amount" min="1000"
                       step="1000" max="{{ (float) $customer->walet_1 }}" required
                       value="{{ old('amount') }}" placeholder="Nhập số tiền">
                <div class="text-danger small mt-1" id="amount-error" style="display:none"></div>
                <div class="helper-text">Số dư ví rút sẽ bị trừ ngay khi xác nhận.</div>
              </div>
              <div class="col-12">
                <label class="form-label required" for="password">Mật khẩu đăng nhập</label>
                <input type="password" class="form-control" name="password" id="password" required
                       placeholder="Nhập mật khẩu để xác nhận">
              </div>
              <div class="col-12">
                <label class="form-label" for="note">Nội dung (không bắt buộc)</label>
                <textarea class="form-control" name="note" id="note" rows="2" maxlength="255"
                          placeholder="Ví dụ: Chuyển hoàn tiền đơn #1234">{{ old('note') }}</textarea>
              </div>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center mt-4">
              <button type="submit" class="btn btn-success px-4">Chuyển tiền</button>
              <a href="{{ route('bitsgold.wallet_history') }}" class="btn btn-outline-secondary">Xem lịch sử ví</a>
              <span class="input-chip"><span class="bi bi-shield-lock"></span>Bảo vệ bằng mật khẩu</span>
            </div>
          </form>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="glass-card p-3 h-100 d-flex flex-column gap-3">
          <div>
            <div class="section-title d-flex align-items-center gap-2">Mã/QR của bạn</div>
            <div class="bg-light rounded p-3 text-center mb-3 qr-box">
              {!! $myQrSvg !!}
            </div>
            <div class="d-flex align-items-center gap-2">
              <code class="flex-grow-1 text-break" id="my-code-text-side">{{ $myCode }}</code>
              <button type="button" class="btn btn-outline-primary btn-sm" id="copy-code-btn-side">Sao chép</button>
            </div>
            <div class="helper-text mt-2">Người khác quét mã này sẽ tự động điền đúng email của bạn khi chuyển.</div>
          </div>
          <div class="summary-box">
            <div class="fw-semibold mb-1">Lưu ý nhanh</div>
            <ul class="text-muted small ps-3 mb-0">
              <li>Nhập email hoặc dán chuỗi QR người nhận để xác thực.</li>
              <li>Kiểm tra thông tin hiển thị trước khi bấm chuyển.</li>
              <li>Thông báo sẽ gửi tới cả người gửi và người nhận.</li>
            </ul>
          </div>
        </div>
    </div>
  </div>
</div>

<div class="guide-overlay d-none" id="guide-overlay"></div>

<div class="guide-panel glass-card p-3 d-none" id="guide-panel">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="fw-semibold" id="guide-title">Hướng dẫn</div>
    <button type="button" class="btn-close" aria-label="Close" id="guide-close"></button>
  </div>
  <div class="progress mb-2">
    <div class="progress-bar bg-success" id="guide-progress" style="width: 0%"></div>
  </div>
  <div class="small text-muted mb-3" id="guide-desc">Làm theo các bước bên dưới.</div>
  <div class="d-flex justify-content-between">
    <button type="button" class="btn btn-outline-secondary btn-sm" id="guide-prev">Trước</button>
    <button type="button" class="btn btn-success btn-sm" id="guide-next">Tiếp</button>
  </div>
</div>

<script>
(() => {
    const recipientUrl = @json(route('wallet.transfer.recipient'));
    const prefillRecipient = @json($prefillRecipient ?? null);
    const walletBalance = @json((float) $customer->walet_1);
    const submitBtn = document.querySelector('#wallet-transfer-form button[type="submit"]');
    const amountInput = document.getElementById('amount');
    const amountError = document.getElementById('amount-error');
    const emailInput = document.getElementById('recipient-email');
    const codeInput = document.getElementById('recipient-code');
    const nameField = document.getElementById('recipient-name');
    const emailField = document.getElementById('recipient-email-display');
    const infoBadge = document.getElementById('recipient-empty');
    const errorBox = document.getElementById('recipient-error');
    const decodeBtn = document.getElementById('decode-code-btn');
    const resetBtn = document.getElementById('reset-recipient-btn');
    const copyBtn = document.getElementById('copy-code-btn');
    const copyBtnMobile = null;
  const copyBtnSide = document.getElementById('copy-code-btn-side');
  const myCodeTextSide = document.getElementById('my-code-text-side');
  const guidePanel = document.getElementById('guide-panel');
  const guideTitle = document.getElementById('guide-title');
  const guideDesc = document.getElementById('guide-desc');
  const guidePrev = document.getElementById('guide-prev');
  const guideNext = document.getElementById('guide-next');
  const guideClose = document.getElementById('guide-close');
  const guideProgress = document.getElementById('guide-progress');
  const guideOverlay = document.getElementById('guide-overlay');
  const speakBtn = document.getElementById('speak-guide');
    let lockedByEmail = false;
    let lockedByCode = false;

    const validateAmount = () => {
      if (!amountInput) return;
      const value = parseFloat(amountInput.value || '0');
      const exceeded = value > walletBalance;
      if (exceeded) {
        amountError.textContent = `Số dư ví rút chỉ còn ${walletBalance.toLocaleString('vi-VN')}đ.`;
        amountError.style.display = 'block';
        submitBtn?.setAttribute('disabled', 'disabled');
        amountInput.classList.add('is-invalid');
      } else {
        amountError.textContent = '';
        amountError.style.display = 'none';
        submitBtn?.removeAttribute('disabled');
        amountInput.classList.remove('is-invalid');
      }
    };

    amountInput?.addEventListener('input', validateAmount);
    validateAmount();

    const debounce = (fn, delay = 400) => {
      let timer;
      return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(null, args), delay);
      };
    };

    const renderRecipient = (data) => {
      if (!data) {
        nameField.textContent = 'Chưa xác định';
        emailField.textContent = '';
        infoBadge.classList.replace('bg-success', 'bg-secondary');
        infoBadge.textContent = 'Chưa có dữ liệu';
        lockedByEmail = false;
        lockedByCode = false;
        return;
      }

      nameField.textContent = data.name || 'Không rõ tên';
      emailField.textContent = data.email || '';
      infoBadge.classList.replace('bg-secondary', 'bg-success');
      infoBadge.textContent = 'Đã xác nhận';

      if (data.email) {
        lockedByEmail = true;
        emailInput.value = data.email;
        emailInput.setAttribute('readonly', 'readonly');
        emailInput.classList.add('bg-light');
      }

      if (data.code) {
        lockedByCode = true;
        codeInput.value = data.code;
        codeInput.setAttribute('readonly', 'readonly');
        codeInput.classList.add('bg-light');
        decodeBtn?.setAttribute('disabled', 'disabled');
      }
    };

    const lookupRecipient = async (params) => {
      if (!recipientUrl) return;

      const query = new URLSearchParams(params).toString();
      errorBox.textContent = '';

      try {
        const response = await fetch(`${recipientUrl}?${query}`, {
          headers: { 'Accept': 'application/json' },
        });
        const payload = await response.json();

        if (!response.ok) {
          throw new Error(payload.message || 'Không tìm thấy người nhận.');
        }

        const data = payload.data || null;
        renderRecipient(data);
      } catch (error) {
        renderRecipient(null);
        errorBox.textContent = error.message;
      }
    };

    if (prefillRecipient) {
      renderRecipient(prefillRecipient);
    }

    if (emailInput) {
      emailInput.addEventListener('input', debounce(() => {
        if (lockedByCode) return;
        const value = emailInput.value.trim();
        if (!value) {
          renderRecipient(null);
          return;
        }
        lookupRecipient({ email: value });
      }));
    }

    const triggerCodeLookup = () => {
      if (lockedByEmail) return;
      const value = codeInput?.value.trim();
      if (value) {
        lookupRecipient({ code: value });
      }
    };

    codeInput?.addEventListener('change', triggerCodeLookup);
    decodeBtn?.addEventListener('click', triggerCodeLookup);

    resetBtn?.addEventListener('click', () => {
      lockedByEmail = false;
      lockedByCode = false;
      emailInput?.removeAttribute('readonly');
      codeInput?.removeAttribute('readonly');
      decodeBtn?.removeAttribute('disabled');
      emailInput?.classList.remove('bg-light');
      codeInput?.classList.remove('bg-light');
      if (emailInput) emailInput.value = '';
      if (codeInput) codeInput.value = '';
      renderRecipient(null);
      errorBox.textContent = '';
    });

  const copyCode = (textEl, buttonEl) => {
    if (!textEl?.textContent || !buttonEl) return;
    navigator.clipboard?.writeText(textEl.textContent).then(() => {
      buttonEl.textContent = 'Đã sao chép';
      setTimeout(() => (buttonEl.textContent = 'Sao chép'), 1400);
    });
  };

  copyBtnSide?.addEventListener('click', () => copyCode(myCodeTextSide, copyBtnSide));

  // Guided tour (simple Google-like helper)
  const steps = [
    {
      el: emailInput,
      title: 'Chọn người nhận',
      desc: 'Nhập email hoặc dán mã QR, hệ thống sẽ khóa trường sau khi xác nhận.',
    },
    {
      el: amountInput,
      title: 'Nhập số tiền',
      desc: 'Nếu vượt số dư ví rút, hệ thống sẽ cảnh báo ngay.',
    },
    {
      el: document.getElementById('password'),
      title: 'Xác nhận giao dịch',
      desc: 'Nhập mật khẩu đăng nhập để gửi và gửi thông báo cho hai bên.',
    },
  ];

  let guideIndex = 0;

  const clearHighlight = () => {
    steps.forEach(step => step.el?.classList.remove('guide-highlight'));
  };

  const showStep = (index) => {
    if (!guidePanel) return;
    guideIndex = Math.max(0, Math.min(index, steps.length - 1));
    const step = steps[guideIndex];
    clearHighlight();
    guideOverlay?.classList.remove('d-none');
    step.el?.classList.add('guide-highlight');
    guideTitle.textContent = step.title;
    guideDesc.textContent = step.desc;
    guidePrev.disabled = guideIndex === 0;
    guideNext.textContent = guideIndex === steps.length - 1 ? 'Hoàn tất' : 'Tiếp';
    guideProgress.style.width = `${((guideIndex + 1) / steps.length) * 100}%`;
    step.el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    guidePanel.classList.remove('d-none');
    if ('speechSynthesis' in window) {
      speakStep(step.title + '. ' + step.desc);
    }
  };

  guidePrev?.addEventListener('click', () => showStep(guideIndex - 1));
  guideNext?.addEventListener('click', () => {
    if (guideIndex === steps.length - 1) {
      clearHighlight();
      guidePanel.classList.add('d-none');
      guideOverlay?.classList.add('d-none');
      return;
    }
    showStep(guideIndex + 1);
  });
  guideClose?.addEventListener('click', () => {
    clearHighlight();
    guidePanel.classList.add('d-none');
    guideOverlay?.classList.add('d-none');
  });

  document.getElementById('start-guide')?.addEventListener('click', () => showStep(0));

  // Voice guide (Web Speech API)
  const speakStep = (text) => {
    if (!('speechSynthesis' in window)) return;
    const utter = new SpeechSynthesisUtterance(text);
    utter.lang = 'vi-VN';
    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(utter);
  };

  speakBtn?.addEventListener('click', () => {
    const step = steps[guideIndex];
    if (step) speakStep(step.title + '. ' + step.desc);
  });
})();
</script>
@endsection
