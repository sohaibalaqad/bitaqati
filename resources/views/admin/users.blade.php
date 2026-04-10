@extends('layouts.admin')

@section('title', 'قائمة المستخدمين')
@section('page-title', 'قائمة المستخدمين')

@push('styles')
<style>
  .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
  .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #cbd5e1; border-radius: 24px; transition: 0.3s; }
  .toggle-slider:before { content: ""; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: 0.3s; }
  .toggle-switch input:checked + .toggle-slider { background: #10b981; }
  .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
</style>
@endpush

@section('content')
  <div class="page-header">
    <h1><i class="fas fa-users"></i> قائمة المستخدمين</h1>
    <button class="btn btn-primary" onclick="openModal('addUserModal')"><i class="fas fa-plus"></i> إضافة مستخدم</button>
  </div>

  @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-weight:600">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif

  <div class="filters-bar">
    <input type="text" id="searchUsers" placeholder="بحث بالاسم أو رقم الهاتف..." oninput="filterTable()">
    <select id="filterStatus" onchange="filterTable()">
      <option value="">كل الحالات</option>
      <option value="active">نشط</option>
      <option value="inactive">غير نشط</option>
    </select>
  </div>

  <div class="table-container">
    <table class="data-table" id="usersTable">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>رقم الهاتف</th>
          <th>الرصيد</th>
          <th>الحالة</th>
          <th>تاريخ الإنشاء</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $i => $u)
          <tr data-name="{{ $u->name }}" data-phone="{{ $u->phone }}" data-status="{{ $u->status }}">
            <td>{{ $i + 1 }}</td>
            <td><a href="{{ route('admin.users.profile', $u->id) }}" style="color:var(--accent);font-weight:700;text-decoration:none">{{ $u->name }}</a></td>
            <td>{{ $u->phone }}</td>
            <td><strong style="color:{{ $u->balance > 0 ? '#10b981' : ($u->balance < 0 ? '#ef4444' : 'var(--text-muted)') }}">{{ format_currency($u->balance) }}</strong></td>
            <td>
              <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}" style="display:inline">
                @csrf
                <label class="toggle-switch">
                  <input type="checkbox" {{ $u->status === 'active' ? 'checked' : '' }} onchange="this.closest('form').submit()">
                  <span class="toggle-slider"></span>
                </label>
              </form>
            </td>
            <td>{{ $u->created_at->format('Y-m-d') }}</td>
            <td style="white-space:nowrap">
              <button class="btn btn-primary" style="padding:6px 10px;font-size:12px;background:#10b981;border-color:#10b981"
                onclick="openBalance({{ $u->id }},'{{ e($u->name) }}',{{ $u->balance }},'add')" title="إضافة رصيد">
                <i class="fas fa-plus"></i> إضافة رصيد
              </button>
              <button class="btn btn-outline" style="padding:6px 10px;font-size:12px;color:#f59e0b;border-color:#f59e0b"
                onclick="openBalance({{ $u->id }},'{{ e($u->name) }}',{{ $u->balance }},'deduct')" title="سحب رصيد">
                <i class="fas fa-minus"></i> سحب رصيد
              </button>
              <a href="{{ route('admin.users.profile', $u->id) }}" class="btn btn-outline" style="padding:6px 10px;font-size:12px" title="عرض البروفايل">
                <i class="fas fa-eye"></i> عرض البروفايل
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="7"><div class="empty-state"><i class="fas fa-users-slash"></i><h3>لا يوجد مستخدمين</h3></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@section('modals')
<!-- Add User Modal -->
<div class="modal-overlay" id="addUserModal">
  <div class="modal">
    <div class="modal-header"><h3>إضافة مستخدم جديد</h3><button class="modal-close" onclick="closeModal('addUserModal')">&times;</button></div>
    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="form-group"><label>الاسم الكامل</label><input type="text" class="form-control" name="name" required placeholder="أدخل اسم المستخدم"></div>
      <div class="form-group"><label>رقم الهاتف</label><input type="tel" class="form-control" name="phone" required placeholder="05XXXXXXXX"></div>
      <div class="form-group"><label>كلمة المرور</label><input type="text" class="form-control" name="password" required placeholder="كلمة مرور الحساب"></div>
      <div class="form-group"><label>الرصيد الابتدائي ({{ currency_symbol() }})</label><input type="number" class="form-control" name="balance" value="0" min="0" step="0.01"></div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-save"></i> حفظ</button>
        <button type="button" class="btn btn-outline" onclick="closeModal('addUserModal')">إلغاء</button>
      </div>
    </form>
  </div>
</div>

<!-- Balance Modal -->
<div class="modal-overlay" id="balanceModal">
  <div class="modal">
    <div class="modal-header"><h3 id="balanceModalTitle">إدارة الرصيد</h3><button class="modal-close" onclick="closeModal('balanceModal')">&times;</button></div>
    <form id="balanceForm" method="POST" action="">
      @csrf
      <div class="form-group"><label>المستخدم</label><input type="text" class="form-control" id="balUserName" readonly></div>
      <div class="form-group"><label>الرصيد الحالي</label><input type="text" class="form-control" id="balCurrentAmount" readonly></div>
      <div class="form-group"><label id="balAmountLabel">المبلغ ({{ currency_symbol() }})</label><input type="number" class="form-control" name="amount" id="balAmount" required min="0.01" step="0.01" placeholder="أدخل المبلغ"></div>
      <div class="form-group"><label>ملاحظة (اختياري)</label><input type="text" class="form-control" name="note" id="balNote" placeholder="سبب العملية"></div>
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
function filterTable() {
  const search = document.getElementById('searchUsers').value.toLowerCase();
  const statusFilter = document.getElementById('filterStatus').value;
  document.querySelectorAll('#usersTable tbody tr').forEach(tr => {
    const name = (tr.dataset.name || '').toLowerCase();
    const phone = tr.dataset.phone || '';
    const status = tr.dataset.status || '';
    const matchSearch = name.includes(search) || phone.includes(search);
    const matchStatus = !statusFilter || status === statusFilter;
    tr.style.display = matchSearch && matchStatus ? '' : 'none';
  });
}

function openBalance(userId, userName, currentBalance, action) {
  document.getElementById('balUserName').value = userName;
  document.getElementById('balCurrentAmount').value = CURRENCY.format(currentBalance);
  document.getElementById('balAmount').value = '';
  document.getElementById('balNote').value = '';

  const form = document.getElementById('balanceForm');
  if (action === 'add') {
    form.action = '/admin/users/' + userId + '/add-balance';
    document.getElementById('balanceModalTitle').textContent = 'إضافة رصيد';
    document.getElementById('balAmountLabel').textContent = 'المبلغ المراد إضافته (' + CURRENCY.symbol + ')';
    document.getElementById('balSubmitBtn').innerHTML = '<i class="fas fa-plus-circle"></i> إضافة';
    document.getElementById('balSubmitBtn').className = 'btn btn-primary';
  } else {
    form.action = '/admin/users/' + userId + '/withdraw-balance';
    document.getElementById('balanceModalTitle').textContent = 'سحب رصيد';
    document.getElementById('balAmountLabel').textContent = 'المبلغ المراد سحبه (' + CURRENCY.symbol + ')';
    document.getElementById('balSubmitBtn').innerHTML = '<i class="fas fa-minus-circle"></i> سحب';
    document.getElementById('balSubmitBtn').className = 'btn btn-danger';
  }
  openModal('balanceModal');
}
</script>
@endpush
