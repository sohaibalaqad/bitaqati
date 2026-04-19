@extends('layouts.superadmin')
@section('title', 'لوحة التحكم العليا')
@section('page-title', 'لوحة التحكم')

@section('content')

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:24px">

  <div class="stat-card-sa">
    <div style="font-size:28px;font-weight:800">{{ $stats['total_tenants'] }}</div>
    <div style="font-size:14px;opacity:.85;margin-top:4px"><i class="fas fa-network-wired" style="margin-left:6px"></i>إجمالي الشبكات</div>
  </div>

  <div style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border-radius:12px;padding:20px">
    <div style="font-size:28px;font-weight:800">{{ $stats['active_tenants'] }}</div>
    <div style="font-size:14px;opacity:.85;margin-top:4px"><i class="fas fa-check-circle" style="margin-left:6px"></i>شبكات نشطة</div>
  </div>

  <div style="background:linear-gradient(135deg,#d97706,#b45309);color:#fff;border-radius:12px;padding:20px">
    <div style="font-size:28px;font-weight:800">{{ $stats['suspended_tenants'] }}</div>
    <div style="font-size:14px;opacity:.85;margin-top:4px"><i class="fas fa-pause-circle" style="margin-left:6px"></i>شبكات معلّقة</div>
  </div>

  <div style="background:linear-gradient(135deg,#0284c7,#0369a1);color:#fff;border-radius:12px;padding:20px">
    <div style="font-size:28px;font-weight:800">{{ $stats['total_plans'] }}</div>
    <div style="font-size:14px;opacity:.85;margin-top:4px"><i class="fas fa-tags" style="margin-left:6px"></i>باقات الاشتراك</div>
  </div>

  <div style="background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border-radius:12px;padding:20px">
    <div style="font-size:28px;font-weight:800">{{ $stats['total_users'] }}</div>
    <div style="font-size:14px;opacity:.85;margin-top:4px"><i class="fas fa-users" style="margin-left:6px"></i>إجمالي المستخدمين</div>
  </div>

  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:20px">
    <div style="font-size:20px;font-weight:800;color:var(--accent);font-family:monospace">{{ $stats['platform_version'] }}</div>
    <div style="font-size:14px;color:var(--text-muted);margin-top:4px"><i class="fas fa-code-branch" style="margin-left:6px"></i>إصدار المنصة</div>
  </div>

</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;flex-wrap:wrap">

  {{-- Recent Tenants --}}
  <div class="table-container" style="padding:0">
    <div class="table-header" style="padding:18px 20px;display:flex;justify-content:space-between;align-items:center">
      <h3 style="margin:0"><i class="fas fa-network-wired" style="color:var(--accent);margin-left:8px"></i> أحدث الشبكات</h3>
      <a href="{{ route('superadmin.tenants') }}" class="btn btn-outline" style="font-size:13px;padding:6px 14px">عرض الكل</a>
    </div>
    <table class="data-table">
      <thead>
        <tr>
          <th>الشبكة</th>
          <th>الـ Subdomain</th>
          <th>الباقة</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentTenants as $tenant)
          <tr>
            <td><strong>{{ $tenant->name }}</strong></td>
            <td><code style="background:var(--bg-main);padding:2px 8px;border-radius:4px;font-size:12px">{{ $tenant->subdomain }}</code></td>
            <td>{{ $tenant->plan?->name ?? '—' }}</td>
            <td>
              @if($tenant->status === 'active')
                <span class="badge badge-success">نشطة</span>
              @elseif($tenant->status === 'suspended')
                <span class="badge badge-danger">معلّقة</span>
              @else
                <span class="badge badge-warning">تجريبية</span>
              @endif
            </td>
            <td>
              <a href="{{ route('superadmin.tenants.edit', $tenant->id) }}" class="btn btn-outline" style="padding:4px 12px;font-size:12px">تعديل</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px">لا توجد شبكات بعد</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Plans Summary --}}
  <div class="table-container" style="padding:0">
    <div class="table-header" style="padding:18px 20px">
      <h3 style="margin:0"><i class="fas fa-tags" style="color:var(--accent);margin-left:8px"></i> الباقات</h3>
    </div>
    <div style="padding:16px;display:grid;gap:12px">
      @forelse($plans as $plan)
        <div style="background:var(--bg-main);border-radius:8px;padding:14px;border:1px solid var(--border)">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <strong style="font-size:15px">{{ $plan->name }}</strong>
            <span style="background:var(--accent-light);color:var(--accent);font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px">
              {{ $plan->tenants_count }} شبكة
            </span>
          </div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:6px">
            {{ $plan->price > 0 ? format_currency($plan->price) . ' / شهر' : 'مجاني' }}
            •
            {{ $plan->max_cards == 0 ? 'بطاقات غير محدودة' : $plan->max_cards . ' بطاقة' }}
          </div>
        </div>
      @empty
        <p style="text-align:center;color:var(--text-muted);padding:20px">لا توجد باقات</p>
      @endforelse
      <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary" style="text-align:center">
        <i class="fas fa-plus"></i> باقة جديدة
      </a>
    </div>
  </div>

</div>

@endsection
