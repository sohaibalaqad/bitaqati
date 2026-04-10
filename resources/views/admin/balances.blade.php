@extends('layouts.admin')

@section('title', 'أرصدة المستخدمين')
@section('page-title', 'أرصدة المستخدمين')

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-shekel-sign"></i> أرصدة المستخدمين</h1>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info">
        <h3>إجمالي الأرصدة</h3>
        <div class="amount">{{ format_currency($totalBalance) }}</div>
      </div>
      <div class="stat-icon green"><i class="fas fa-wallet"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info">
        <h3>عدد المستخدمين</h3>
        <div class="amount">{{ $users->count() }}</div>
      </div>
      <div class="stat-icon blue"><i class="fas fa-users"></i></div>
    </div>
  </div>

  <!-- Search -->
  <div class="filters-bar">
    <input type="text" id="searchBalances" placeholder="بحث بالاسم أو رقم الهاتف..." oninput="filterBalances()">
  </div>

  <!-- Balances Table -->
  <div class="table-container">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>رقم الهاتف</th>
          <th>الرصيد الحالي</th>
          <th>الحالة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody id="balancesTableBody">
        @forelse($users as $i => $user)
          <tr
            data-name="{{ strtolower($user->name) }}"
            data-phone="{{ $user->phone ?? '' }}">
            <td>{{ $i + 1 }}</td>
            <td>
              <a href="{{ route('admin.users.profile', $user->id) }}" style="font-weight:bold;color:var(--accent);text-decoration:none">
                {{ $user->name }}
              </a>
            </td>
            <td>{{ $user->phone ?? '-' }}</td>
            <td>
              <strong style="color: {{ $user->balance > 0 ? '#10b981' : '#ef4444' }}">
                {{ format_currency($user->balance) }}
              </strong>
            </td>
            <td>
              <span class="badge-status {{ $user->status === 'active' ? 'good' : 'low' }}">
                {{ $user->status === 'active' ? 'نشط' : 'غير نشط' }}
              </span>
            </td>
            <td>
              <button class="btn btn-primary" style="padding:6px 12px;font-size:13px"
                onclick="openAddBalance({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->balance }})">
                <i class="fas fa-plus"></i> شحن
              </button>
              <button class="btn btn-danger" style="padding:6px 12px;font-size:13px"
                onclick="openDeductBalance({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->balance }})">
                <i class="fas fa-minus"></i> خصم
              </button>
            </td>
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="6">
              <div class="empty-state">
                <i class="fas fa-wallet"></i>
                <h3>لا يوجد مستخدمين</h3>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Recent Transactions -->
  @if($transactions->count() > 0)
  <div class="table-container" style="margin-top:24px">
    <div class="table-header">
      <h3>آخر العمليات</h3>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>المستخدم</th>
          <th>النوع</th>
          <th>المبلغ</th>
          <th>التاريخ</th>
          <th>ملاحظات</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transactions as $i => $tx)
          @php
            $typeMap = [
              'deposit'  => ['label' => 'إيداع',  'cls' => 'good',   'icon' => 'fa-arrow-down'],
              'withdraw' => ['label' => 'سحب',    'cls' => 'low',    'icon' => 'fa-arrow-up'],
              'purchase' => ['label' => 'شراء',   'cls' => 'medium', 'icon' => 'fa-shopping-cart'],
            ];
            $tp = $typeMap[$tx->type] ?? ['label' => 'أخرى', 'cls' => 'medium', 'icon' => 'fa-circle'];
          @endphp
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $tx->user->name ?? '-' }}</td>
            <td>
              <span class="badge-status {{ $tp['cls'] }}">
                <i class="fas {{ $tp['icon'] }}" style="margin-left:4px"></i> {{ $tp['label'] }}
              </span>
            </td>
            <td>
              <strong style="color: {{ $tx->amount >= 0 ? '#10b981' : '#ef4444' }}">
                {{ ($tx->amount >= 0 ? '+' : '') . format_currency(abs($tx->amount)) }}
              </strong>
            </td>
            <td>{{ $tx->created_at->format('Y-m-d') }}</td>
            <td>{{ $tx->note ?? '-' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
@endsection

@section('modals')
  <!-- Add Balance Modal -->
  <div class="modal-overlay" id="addBalanceModal">
    <div class="modal">
      <div class="modal-header">
        <h3>شحن رصيد</h3>
        <button class="modal-close" onclick="closeModal('addBalanceModal')">&times;</button>
      </div>
      <form method="POST" action="" id="addBalanceForm">
        @csrf
        <div class="form-group">
          <label>المستخدم</label>
          <input type="text" class="form-control" id="balanceUserName" readonly>
        </div>
        <div class="form-group">
          <label>الرصيد الحالي</label>
          <input type="text" class="form-control" id="balanceCurrentAmount" readonly>
        </div>
        <div class="form-group">
          <label>المبلغ المراد إضافته ({{ currency_symbol() }})</label>
          <input type="number" class="form-control" name="amount" required min="0.01" step="0.01" placeholder="أدخل المبلغ">
        </div>
        <div class="form-group">
          <label>ملاحظات</label>
          <input type="text" class="form-control" name="note" placeholder="سبب العملية (اختياري)">
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1">
            <i class="fas fa-plus-circle"></i> شحن الرصيد
          </button>
          <button type="button" class="btn btn-outline" onclick="closeModal('addBalanceModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Deduct Balance Modal -->
  <div class="modal-overlay" id="deductBalanceModal">
    <div class="modal">
      <div class="modal-header">
        <h3>خصم رصيد</h3>
        <button class="modal-close" onclick="closeModal('deductBalanceModal')">&times;</button>
      </div>
      <form method="POST" action="" id="deductBalanceForm">
        @csrf
        <div class="form-group">
          <label>المستخدم</label>
          <input type="text" class="form-control" id="deductUserName" readonly>
        </div>
        <div class="form-group">
          <label>الرصيد الحالي</label>
          <input type="text" class="form-control" id="deductCurrentAmount" readonly>
        </div>
        <div class="form-group">
          <label>المبلغ المراد خصمه ({{ currency_symbol() }})</label>
          <input type="number" class="form-control" name="amount" required min="0.01" step="0.01" placeholder="أدخل المبلغ">
        </div>
        <div class="form-group">
          <label>ملاحظات</label>
          <input type="text" class="form-control" name="note" placeholder="سبب العملية (اختياري)">
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-danger" style="flex:1">
            <i class="fas fa-minus-circle"></i> خصم الرصيد
          </button>
          <button type="button" class="btn btn-outline" onclick="closeModal('deductBalanceModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
function filterBalances() {
  const search = document.getElementById('searchBalances').value.toLowerCase();
  const rows = document.querySelectorAll('#balancesTableBody tr[data-name]');
  let visibleCount = 0;

  rows.forEach(row => {
    const name = row.dataset.name || '';
    const phone = row.dataset.phone || '';
    const visible = name.includes(search) || phone.includes(search);
    row.style.display = visible ? '' : 'none';
    if (visible) visibleCount++;
  });

  const emptyRow = document.getElementById('emptyRow');
  if (emptyRow) emptyRow.style.display = visibleCount === 0 ? '' : 'none';
}

function openAddBalance(id, name, balance) {
  document.getElementById('addBalanceForm').action = '/admin/users/' + id + '/add-balance';
  document.getElementById('balanceUserName').value = name;
  document.getElementById('balanceCurrentAmount').value = CURRENCY.format(balance);
  openModal('addBalanceModal');
}

function openDeductBalance(id, name, balance) {
  document.getElementById('deductBalanceForm').action = '/admin/users/' + id + '/withdraw-balance';
  document.getElementById('deductUserName').value = name;
  document.getElementById('deductCurrentAmount').value = CURRENCY.format(balance);
  openModal('deductBalanceModal');
}
</script>
@endpush
