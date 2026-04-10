@extends('layouts.admin')

@section('title', $user->name . ' - بروفايل المستخدم')
@section('page-title', 'بروفايل المستخدم')

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <a href="{{ route('admin.users') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--text-muted);font-size:14px;margin-bottom:16px">
    <i class="fas fa-arrow-right"></i> العودة لقائمة المستخدمين
  </a>

  <!-- Profile Card -->
  <div class="profile-card">
    <div class="profile-top">
      <div class="profile-avatar-lg">{{ mb_substr($user->name, 0, 1) }}</div>
      <div class="profile-details">
        <h2>{{ $user->name }}</h2>
        <div class="profile-meta">
          <span><i class="fas fa-phone"></i> {{ $user->phone ?? 'لم يحدد' }}</span>
          <span><i class="fas fa-envelope"></i> {{ $user->email ?? 'لم يحدد' }}</span>
          <span><i class="fas fa-calendar"></i> انضم {{ $user->created_at->format('Y-m-d') }}</span>
          <span class="badge-status {{ $user->status === 'active' ? 'good' : 'low' }}">
            {{ $user->status === 'active' ? 'نشط' : 'غير نشط' }}
          </span>
        </div>
      </div>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-top:20px;padding-top:20px;border-top:1px solid var(--border)">
      <div style="font-size:14px;color:var(--text-muted)">
        الرصيد الحالي:
        <strong style="font-size:28px;color:{{ $user->balance > 0 ? '#10b981' : '#ef4444' }};margin-right:8px">
          {{ format_currency($user->balance) }}
        </strong>
      </div>
      <div class="profile-actions-bar">
        <button class="btn btn-primary" onclick="openBalanceModal('add')">
          <i class="fas fa-plus"></i> إضافة رصيد
        </button>
        <button class="btn btn-warning" onclick="openBalanceModal('withdraw')">
          <i class="fas fa-minus"></i> سحب رصيد
        </button>
        <form method="POST" action="{{ route('admin.users.update-settings', $user->id) }}" style="display:inline">
          @csrf
          @method('PUT')
          <input type="hidden" name="toggle_status" value="1">
          <button type="submit" class="btn {{ $user->status === 'active' ? 'btn-danger' : 'btn-primary' }}">
            <i class="fas {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }}"></i>
            {{ $user->status === 'active' ? 'تعطيل الحساب' : 'تنشيط الحساب' }}
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-info"><h3>إجمالي العمليات</h3><div class="amount">{{ $transactions->count() }}</div></div>
      <div class="stat-icon blue"><i class="fas fa-exchange-alt"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><h3>البطاقات المشتراة</h3><div class="amount">{{ $cards->count() }}</div></div>
      <div class="stat-icon green"><i class="fas fa-credit-card"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><h3>الفواتير</h3><div class="amount">{{ $invoices->count() }}</div></div>
      <div class="stat-icon purple"><i class="fas fa-file-invoice"></i></div>
    </div>
    <div class="stat-card">
      <div class="stat-info"><h3>طلبات الشحن</h3><div class="amount">{{ $rechargeRequests->count() }}</div></div>
      <div class="stat-icon orange"><i class="fas fa-wallet"></i></div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="profile-tabs">
    <button class="profile-tab active" data-tab="transactions"><i class="fas fa-exchange-alt"></i> النشاط</button>
    <button class="profile-tab" data-tab="cards"><i class="fas fa-credit-card"></i> البطاقات المشتراة</button>
    <button class="profile-tab" data-tab="invoices"><i class="fas fa-file-invoice"></i> الفواتير</button>
    <button class="profile-tab" data-tab="requests"><i class="fas fa-wallet"></i> طلبات الشحن</button>
    <button class="profile-tab" data-tab="settings"><i class="fas fa-cog"></i> إعدادات الحساب</button>
  </div>

  <!-- Tab: Transactions -->
  <div class="profile-tab-content active" id="tab-transactions">
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr><th>#</th><th>النوع</th><th>المبلغ</th><th>التاريخ</th><th>ملاحظات</th></tr>
        </thead>
        <tbody>
          @forelse($transactions as $i => $tx)
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
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state"><i class="fas fa-exchange-alt"></i><h3>لا يوجد عمليات</h3></div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tab: Cards -->
  <div class="profile-tab-content" id="tab-cards">
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr><th>#</th><th>اسم المستخدم</th><th>كلمة المرور</th><th>الباقة</th><th>تاريخ الشراء</th></tr>
        </thead>
        <tbody>
          @forelse($cards as $i => $card)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td><code style="background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:13px">{{ $card->username }}</code></td>
              <td><code style="background:#f1f5f9;padding:3px 8px;border-radius:4px;font-size:13px">{{ $card->password }}</code></td>
              <td>{{ $card->package->name ?? '-' }}</td>
              <td>{{ $card->sold_at ? $card->sold_at->format('Y-m-d') : '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state"><i class="fas fa-credit-card"></i><h3>لم يشتري بطاقات بعد</h3></div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tab: Invoices -->
  <div class="profile-tab-content" id="tab-invoices">
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr><th>رقم الفاتورة</th><th>الباقة</th><th>المبلغ</th><th>التاريخ</th><th>الحالة</th></tr>
        </thead>
        <tbody>
          @forelse($invoices as $invoice)
            <tr>
              <td><strong>#{{ $invoice->id }}</strong></td>
              <td>{{ $invoice->package->name ?? '-' }}</td>
              <td>{{ format_currency($invoice->amount) }}</td>
              <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
              <td>
                <span class="badge-status {{ $invoice->status === 'paid' ? 'good' : 'medium' }}">
                  {{ $invoice->status === 'paid' ? 'مدفوعة' : 'معلقة' }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state"><i class="fas fa-file-invoice"></i><h3>لا يوجد فواتير</h3></div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tab: Recharge Requests -->
  <div class="profile-tab-content" id="tab-requests">
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr><th>#</th><th>المبلغ</th><th>التاريخ</th><th>ملاحظات</th><th>الحالة</th></tr>
        </thead>
        <tbody>
          @forelse($rechargeRequests as $i => $req)
            @php
              $reqStatusMap = [
                'pending'  => ['label' => 'معلق',   'cls' => 'medium'],
                'approved' => ['label' => 'مقبول',  'cls' => 'good'],
                'rejected' => ['label' => 'مرفوض', 'cls' => 'low'],
              ];
              $rs = $reqStatusMap[$req->status] ?? $reqStatusMap['pending'];
            @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ format_currency($req->amount) }}</td>
              <td>{{ $req->created_at->format('Y-m-d') }}</td>
              <td>{{ $req->note ?? '-' }}</td>
              <td><span class="badge-status {{ $rs['cls'] }}">{{ $rs['label'] }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state"><i class="fas fa-wallet"></i><h3>لا يوجد طلبات شحن</h3></div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Tab: Settings -->
  <div class="profile-tab-content" id="tab-settings">
    <div class="profile-card">
      <h3 style="margin-bottom:20px"><i class="fas fa-cog"></i> إعدادات الحساب</h3>
      <form method="POST" action="{{ route('admin.users.update-settings', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group"><label>الاسم الكامل</label><input type="text" class="form-control" name="name" value="{{ $user->name }}" required></div>
        <div class="form-group"><label>رقم الهاتف</label><input type="tel" class="form-control" name="phone" value="{{ $user->phone ?? '' }}" required></div>
        <div class="form-group"><label>البريد الإلكتروني</label><input type="email" class="form-control" name="email" value="{{ $user->email ?? '' }}"></div>
        <div class="form-group"><label>كلمة المرور الجديدة (اتركها فارغة إذا لا تريد التغيير)</label><input type="password" class="form-control" name="password" placeholder="كلمة مرور جديدة"></div>
        <div class="form-group"><label>تأكيد كلمة المرور</label><input type="password" class="form-control" name="password_confirmation" placeholder="تأكيد كلمة المرور"></div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-save"></i> حفظ التغييرات</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@section('modals')
  <!-- Balance Modal -->
  <div class="modal-overlay" id="balanceModal">
    <div class="modal">
      <div class="modal-header">
        <h3 id="balModalTitle">إدارة الرصيد</h3>
        <button class="modal-close" onclick="closeModal('balanceModal')">&times;</button>
      </div>
      <form method="POST" id="balanceForm" action="">
        @csrf
        <div class="form-group"><label>المستخدم</label><input type="text" class="form-control" value="{{ $user->name }}" readonly></div>
        <div class="form-group"><label>الرصيد الحالي</label><input type="text" class="form-control" value="{{ format_currency($user->balance) }}" readonly></div>
        <div class="form-group">
          <label id="balAmountLabel">المبلغ ({{ currency_symbol() }})</label>
          <input type="number" class="form-control" name="amount" required min="0.01" step="0.01">
        </div>
        <div class="form-group">
          <label>ملاحظات</label>
          <input type="text" class="form-control" name="note" placeholder="سبب العملية (اختياري)">
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1" id="balSubmitBtn"><i class="fas fa-check"></i> تأكيد</button>
          <button type="button" class="btn btn-outline" onclick="closeModal('balanceModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.profile-tab').forEach(tab => {
  tab.addEventListener('click', function() {
    document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.profile-tab-content').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    document.getElementById('tab-' + this.dataset.tab).classList.add('active');
  });
});

// Balance modal
const addBalanceUrl = '{{ route('admin.users.add-balance', $user->id) }}';
const withdrawBalanceUrl = '{{ route('admin.users.withdraw-balance', $user->id) }}';

function openBalanceModal(action) {
  if (action === 'add') {
    document.getElementById('balModalTitle').textContent = 'إضافة رصيد';
    document.getElementById('balAmountLabel').textContent = 'المبلغ المراد إضافته (' + CURRENCY.symbol + ')';
    document.getElementById('balSubmitBtn').className = 'btn btn-primary';
    document.getElementById('balSubmitBtn').innerHTML = '<i class="fas fa-plus-circle"></i> إضافة';
    document.getElementById('balanceForm').action = addBalanceUrl;
  } else {
    document.getElementById('balModalTitle').textContent = 'سحب رصيد';
    document.getElementById('balAmountLabel').textContent = 'المبلغ المراد سحبه (' + CURRENCY.symbol + ')';
    document.getElementById('balSubmitBtn').className = 'btn btn-danger';
    document.getElementById('balSubmitBtn').innerHTML = '<i class="fas fa-minus-circle"></i> سحب';
    document.getElementById('balanceForm').action = withdrawBalanceUrl;
  }

  // Add hidden user_id field if not present
  let hiddenInput = document.getElementById('balanceFormUserId');
  if (!hiddenInput) {
    hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'user_id';
    hiddenInput.id = 'balanceFormUserId';
    hiddenInput.value = {{ $user->id }};
    document.getElementById('balanceForm').appendChild(hiddenInput);
  }

  openModal('balanceModal');
}
</script>
@endpush
