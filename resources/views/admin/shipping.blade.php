@extends('layouts.admin')

@section('title', 'طلبات شحن الرصيد')
@section('page-title', 'طلبات شحن الرصيد')

@section('content')
  <div class="page-header">
    <h1><i class="fas fa-wallet"></i> طلبات شحن الرصيد</h1>
  </div>

  @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:600">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  @php
    $pending = $requests->where('status', 'pending')->count();
    $approved = $requests->where('status', 'approved')->count();
    $rejected = $requests->where('status', 'rejected')->count();
  @endphp

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-info"><h3>إجمالي الطلبات</h3><div class="amount">{{ $requests->count() }}</div></div><div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div></div>
    <div class="stat-card"><div class="stat-info"><h3>معلقة</h3><div class="amount">{{ $pending }}</div></div><div class="stat-icon orange"><i class="fas fa-clock"></i></div></div>
    <div class="stat-card"><div class="stat-info"><h3>مقبولة</h3><div class="amount">{{ $approved }}</div></div><div class="stat-icon green"><i class="fas fa-check-circle"></i></div></div>
    <div class="stat-card"><div class="stat-info"><h3>مرفوضة</h3><div class="amount">{{ $rejected }}</div></div><div class="stat-icon purple"><i class="fas fa-times-circle"></i></div></div>
  </div>

  <!-- Filters -->
  <div class="filters-bar">
    <input type="text" id="searchReqs" placeholder="بحث باسم المستخدم..." oninput="filterReqs()">
    <select id="filterReqStatus" onchange="filterReqs()">
      <option value="">كل الحالات</option>
      <option value="pending">معلقة</option>
      <option value="approved">مقبولة</option>
      <option value="rejected">مرفوضة</option>
    </select>
  </div>

  <!-- Table -->
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>المستخدم</th>
          <th>المبلغ</th>
          <th>ملاحظات</th>
          <th>التاريخ</th>
          <th>الحالة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody id="reqsBody">
        @forelse ($requests->sortByDesc(fn($r) => $r->status === 'pending' ? 1 : 0) as $i => $r)
          @php
            $statusMap = ['pending' => ['معلق','medium'], 'approved' => ['مقبول','good'], 'rejected' => ['مرفوض','low']];
            $st = $statusMap[$r->status] ?? $statusMap['pending'];
          @endphp
          <tr data-user="{{ $r->user->name ?? '' }}" data-status="{{ $r->status }}">
            <td>{{ $i + 1 }}</td>
            <td>
              <a href="{{ route('admin.users.profile', $r->user_id) }}" style="color:var(--accent);font-weight:600">
                {{ $r->user->name ?? '-' }}
              </a>
            </td>
            <td><strong>{{ format_currency($r->amount) }}</strong></td>
            <td>{{ $r->note ?: '-' }}</td>
            <td>{{ $r->created_at->format('Y-m-d') }}</td>
            <td><span class="badge-status {{ $st[1] }}">{{ $st[0] }}</span></td>
            <td style="white-space:nowrap">
              @if($r->status === 'pending')
                <form method="POST" action="{{ route('admin.shipping.approve', $r->id) }}" style="display:inline">
                  @csrf
                  <button type="submit" class="btn btn-primary" style="padding:6px 12px;font-size:13px"
                    onclick="return confirm('هل تريد قبول هذا الطلب وإضافة المبلغ لرصيد المستخدم؟')">
                    <i class="fas fa-check"></i> قبول
                  </button>
                </form>
                <button class="btn btn-danger" style="padding:6px 12px;font-size:13px"
                  onclick="rejectRequest({{ $r->id }})">
                  <i class="fas fa-times"></i> رفض
                </button>
                <form id="rejectForm{{ $r->id }}" method="POST" action="{{ route('admin.shipping.reject', $r->id) }}" style="display:none">
                  @csrf
                  <input type="hidden" name="reject_reason" id="rejectReason{{ $r->id }}">
                </form>
              @elseif($r->status === 'approved')
                <span style="color:var(--text-muted);font-size:12px">تمت الموافقة</span>
              @else
                <span style="color:var(--text-muted);font-size:12px" title="{{ $r->reject_reason }}">
                  مرفوض{{ $r->reject_reason ? ': ' . Str::limit($r->reject_reason, 30) : '' }}
                </span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7"><div class="empty-state"><i class="fas fa-wallet"></i><h3>لا يوجد طلبات شحن</h3></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@push('scripts')
<script>
function filterReqs() {
  const search = document.getElementById('searchReqs').value.toLowerCase();
  const statusFilter = document.getElementById('filterReqStatus').value;
  document.querySelectorAll('#reqsBody tr').forEach(tr => {
    const user = (tr.dataset.user || '').toLowerCase();
    const status = tr.dataset.status || '';
    const matchSearch = user.includes(search);
    const matchStatus = !statusFilter || status === statusFilter;
    tr.style.display = matchSearch && matchStatus ? '' : 'none';
  });
}

function rejectRequest(id) {
  const reason = prompt('سبب الرفض (اختياري):');
  if (reason === null) return;
  document.getElementById('rejectReason' + id).value = reason || 'لم يحدد السبب';
  document.getElementById('rejectForm' + id).submit();
}
</script>
@endpush
