@extends(EcommerceHelper::viewPath('customers.master'))

@section('content')
<style>
    .wallet-log-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 12px;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }
    .wallet-log-card .amount {
        font-weight: 700;
        font-size: 16px;
    }
    .wallet-log-card .meta {
        font-size: 12px;
    }
    .wallet-badge {
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        min-width: 64px;
        text-align: center;
        display: inline-block;
    }
    .log-in { background: #f6fffa; border-color: #d8f3e4; }
    .log-out { background: #fff7f6; border-color: #f5d7d3; }
    .log-rejected { background: #f8f9fa; border-color: #e0e0e0; }
    .table > :not(caption) > * > .log-in { background: #f6fffa !important; }
    .table > :not(caption) > * > .log-out { background: #fff7f6 !important; }
    .table > :not(caption) > * > .log-rejected { background: #f8f9fa !important; }
    .amount.income { color: #138750; }
    .amount.outcome { color: #c0392b; }
    .amount.rejected { color: #9e9e9e; text-decoration: line-through; }
</style>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
      <h4 class="mb-1">Lịch sử cộng ví</h4>
      <div class="text-muted small">Tất cả khoản tiền được cộng vào ví rút / tiêu dùng của bạn</div>
    </div>
    <a href="{{ route('bitsgold.dashboard') }}" class="btn btn-sm btn-outline-success">← Quay lại Dashboard</a>
  </div>

  {{-- Mobile list --}}
  <div class="d-md-none">
    @forelse($logs as $log)
      <div class="wallet-log-card log-{{ $log->type === 'out' ? 'out' : ($log->type === 'rejected' ? 'rejected' : 'in') }}">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span class="wallet-badge bg-success text-white">{{ $log->wallet_label }}</span>
          <span class="text-muted meta">{{ $log->created_at?->format('d/m/Y H:i') }}</span>
        </div>
        <div class="fw-semibold">{{ $log->display_title }}</div>
        @if($log->display_desc)
          <div class="text-muted meta">{{ $log->display_desc }}</div>
        @endif
        <div class="d-flex justify-content-between align-items-center mt-2">
          <span class="amount {{ $log->type === 'out' ? 'outcome' : ($log->type === 'rejected' ? 'rejected' : 'income') }}">
            {{ $log->computed_amount ? format_price(abs($log->computed_amount)) : '—' }}
          </span>
          <span class="text-muted meta">#{{ $log->id }}</span>
        </div>
      </div>
    @empty
      <div class="text-center text-muted py-4">Chưa có dữ liệu.</div>
    @endforelse
  </div>

  {{-- Desktop table --}}
  <div class="card shadow-sm d-none d-md-block">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th class="text-nowrap">Thời gian</th>
              <th>Nội dung</th>
              <th class="text-end">Số tiền</th>
              <th class="text-nowrap">Ví</th>
            </tr>
          </thead>
          <tbody class="table-group-divider">
            @forelse($logs as $log)
              <tr class="log-{{ $log->type === 'out' ? 'out' : ($log->type === 'rejected' ? 'rejected' : 'in') }}">
                <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                  <div class="fw-semibold">{{ $log->display_title }}</div>
                  @if($log->display_desc)
                    <div class="text-muted small">{{ $log->display_desc }}</div>
                  @endif
                </td>
                <td class="text-end">
                  <span class="{{ $log->type === 'out' ? 'text-danger' : ($log->type === 'rejected' ? 'text-muted text-decoration-line-through' : 'text-success') }}">
                    {{ $log->computed_amount ? format_price(abs($log->computed_amount)) : '—' }}
                  </span>
                </td>
                <td class="text-nowrap">
                  <span class="wallet-badge bg-success text-white">{{ $log->wallet_label }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="pt-3">
    {{ $logs->links() }}
  </div>
</div>
@endsection
