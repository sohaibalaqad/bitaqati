@extends('layouts.admin')

@section('title', 'باقات الانترنت')
@section('page-title', 'باقات الانترنت')

@section('content')
  @if(session('success'))
    <div class="alert alert-success" style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-error" style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:14px 18px;border-radius:var(--radius);margin-bottom:16px;display:flex;align-items:center;gap:10px">
      <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
  @endif

  <div class="page-header">
    <h1><i class="fas fa-wifi"></i> باقات الانترنت</h1>
    <button class="btn btn-primary" onclick="openModal('addPackageModal')"><i class="fas fa-plus"></i> إضافة باقة</button>
  </div>

  <!-- Packages Grid -->
  @php $colors = ['green', 'blue', 'orange', 'red']; @endphp
  <div class="quick-actions" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr))">
    @forelse($packages as $i => $package)
      <div class="action-card" onclick="window.location.href='{{ route('admin.cards-store') }}'" style="text-align:center;padding:24px;cursor:pointer">
        <div class="action-icon {{ $colors[$i % count($colors)] }}" style="margin:0 auto 12px">
          <i class="fas fa-wifi"></i>
        </div>
        <h4 style="margin-bottom:8px">{{ $package->name }}</h4>
        <p style="font-size:13px;color:var(--text-muted)">{{ $package->speed ?? '' }}{{ ($package->speed && $package->duration) ? ' • ' : '' }}{{ $package->duration ?? '' }}</p>
        <p style="font-size:22px;font-weight:800;margin:10px 0;color:var(--accent)">{{ format_currency($package->price) }}</p>
        <p style="font-size:13px;color:var(--text-muted)">{{ $package->available_count ?? 0 }} بطاقة متوفرة</p>
      </div>
    @empty
      <p style="color:var(--text-muted);padding:20px">لا يوجد باقات بعد</p>
    @endforelse
  </div>

  <!-- Packages Table -->
  <div class="table-container">
    <div class="table-header">
      <h3>تفاصيل الباقات</h3>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>اسم الباقة</th>
          <th>السرعة</th>
          <th>المدة</th>
          <th>سعر البيع</th>
          <th>سعر التكلفة</th>
          <th>الربح</th>
          <th>البطاقات المتوفرة</th>
          <th>إجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($packages as $i => $package)
          @php
            $available = $package->available_count ?? 0;
            $profit = $package->price - $package->cost;
            $availClass = $available > 5 ? 'good' : ($available > 0 ? 'medium' : 'low');
          @endphp
          <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $package->name }}</strong></td>
            <td>{{ $package->speed ?: '-' }}</td>
            <td>{{ $package->duration ?: '-' }}</td>
            <td>{{ format_currency($package->price) }}</td>
            <td>{{ format_currency($package->cost) }}</td>
            <td><strong style="color:#10b981">{{ format_currency($profit) }}</strong></td>
            <td>
              <span class="badge-status {{ $availClass }}">
                {{ $available }} كرت
              </span>
            </td>
            <td style="white-space:nowrap">
              <button class="btn btn-outline" style="padding:6px 10px;font-size:12px"
                onclick="openEditPackage({{ $package->id }}, '{{ addslashes($package->name) }}', '{{ addslashes($package->speed ?? '') }}', '{{ addslashes($package->duration ?? '') }}', {{ $package->price }}, {{ $package->cost }})"
                title="تعديل">
                <i class="fas fa-edit"></i>
              </button>
              <form method="POST" action="{{ route('admin.packages.destroy', $package->id) }}" style="display:inline"
                onsubmit="return confirm('هل أنت متأكد من حذف الباقة: {{ addslashes($package->name) }}؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="padding:6px 10px;font-size:12px" title="حذف">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9">
              <div class="empty-state">
                <i class="fas fa-wifi"></i>
                <h3>لا يوجد باقات</h3>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@section('modals')
  <!-- Add Package Modal -->
  <div class="modal-overlay" id="addPackageModal">
    <div class="modal">
      <div class="modal-header">
        <h3>إضافة باقة جديدة</h3>
        <button class="modal-close" onclick="closeModal('addPackageModal')">&times;</button>
      </div>
      <form method="POST" action="{{ route('admin.packages.store') }}">
        @csrf
        <div class="form-group"><label>اسم الباقة</label><input type="text" class="form-control" name="name" required placeholder="مثال: اشتراك يومي 10 ساعات"></div>
        <div class="form-group"><label>السرعة</label><input type="text" class="form-control" name="speed" placeholder="مثال: 3 ميجا"></div>
        <div class="form-group"><label>المدة</label><input type="text" class="form-control" name="duration" placeholder="مثال: 10 ساعات"></div>
        <div class="form-group"><label>سعر البيع ({{ currency_symbol() }})</label><input type="number" class="form-control" name="price" required min="0" step="0.01"></div>
        <div class="form-group"><label>سعر التكلفة ({{ currency_symbol() }})</label><input type="number" class="form-control" name="cost" required min="0" step="0.01"></div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-save"></i> حفظ</button>
          <button type="button" class="btn btn-outline" onclick="closeModal('addPackageModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Package Modal -->
  <div class="modal-overlay" id="editPackageModal">
    <div class="modal">
      <div class="modal-header">
        <h3>تعديل الباقة</h3>
        <button class="modal-close" onclick="closeModal('editPackageModal')">&times;</button>
      </div>
      <form method="POST" id="editPackageForm" action="">
        @csrf
        @method('PUT')
        <div class="form-group"><label>اسم الباقة</label><input type="text" class="form-control" name="name" id="editPkgName" required></div>
        <div class="form-group"><label>السرعة</label><input type="text" class="form-control" name="speed" id="editPkgSpeed"></div>
        <div class="form-group"><label>المدة</label><input type="text" class="form-control" name="duration" id="editPkgDuration"></div>
        <div class="form-group"><label>سعر البيع ({{ currency_symbol() }})</label><input type="number" class="form-control" name="price" id="editPkgPrice" required min="0" step="0.01"></div>
        <div class="form-group"><label>سعر التكلفة ({{ currency_symbol() }})</label><input type="number" class="form-control" name="cost" id="editPkgCost" required min="0" step="0.01"></div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary" style="flex:1"><i class="fas fa-save"></i> تحديث</button>
          <button type="button" class="btn btn-outline" onclick="closeModal('editPackageModal')">إلغاء</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
function openEditPackage(id, name, speed, duration, price, cost) {
  const baseUrl = '{{ url('admin/packages') }}';
  document.getElementById('editPackageForm').action = baseUrl + '/' + id;
  document.getElementById('editPkgName').value = name;
  document.getElementById('editPkgSpeed').value = speed;
  document.getElementById('editPkgDuration').value = duration;
  document.getElementById('editPkgPrice').value = price;
  document.getElementById('editPkgCost').value = cost;
  openModal('editPackageModal');
}
</script>
@endpush
