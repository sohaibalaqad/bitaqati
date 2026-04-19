@extends('layouts.superadmin')
@section('title', 'إدارة الشبكات')
@section('page-title', 'إدارة الشبكات')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <h1 style="font-size:22px;font-weight:800;color:var(--text-dark)">
    <i class="fas fa-network-wired" style="color:var(--accent);margin-left:8px"></i> الشبكات
  </h1>
  <a href="{{ route('superadmin.tenants.create') }}" class="btn btn-primary">
    <i class="fas fa-plus" style="margin-left:6px"></i> إضافة شبكة
  </a>
</div>

{{-- Pending banner --}}
@php $pendingCount = $tenants->where('status','pending')->count(); @endphp
@if($pendingCount > 0)
<div style="background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #f59e0b;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
  <div style="width:40px;height:40px;background:#f59e0b;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0">
    <i class="fas fa-hourglass-half" style="color:#fff;font-size:16px"></i>
  </div>
  <div>
    <div style="font-weight:800;color:#92400e;font-size:15px">{{ $pendingCount }} طلب تسجيل بانتظار مراجعتك</div>
    <div style="font-size:13px;color:#b45309;margin-top:2px">راجع الطلبات أدناه واتخذ قرارك بالقبول أو الرفض</div>
  </div>
</div>
@endif

<div class="table-container" style="padding:0">
  <table class="data-table">
    <thead>
      <tr>
        <th>#</th>
        <th>اسم الشبكة</th>
        <th>الـ Subdomain</th>
        <th>المالك</th>
        <th>الباقة</th>
        <th>الحالة</th>
        <th>تاريخ الطلب</th>
        <th>الإجراءات</th>
      </tr>
    </thead>
    <tbody>
      @forelse($tenants as $tenant)
        <tr style="{{ $tenant->status === 'pending' ? 'background:rgba(245,158,11,.04)' : '' }}">
          <td style="color:var(--text-muted);font-size:13px">{{ $tenant->id }}</td>
          <td>
            <strong style="color:var(--text-dark)">{{ $tenant->name }}</strong>
            @if($tenant->status === 'pending')
              <span style="display:inline-flex;align-items:center;gap:3px;background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;margin-right:6px">
                <i class="fas fa-clock" style="font-size:9px"></i> جديد
              </span>
            @endif
          </td>
          <td>
            <code style="background:var(--bg-main);padding:3px 10px;border-radius:6px;font-size:12px">
              {{ $tenant->subdomain }}
            </code>
          </td>
          <td>
            <div style="font-size:14px">{{ $tenant->owner?->name ?? '—' }}</div>
            @if($tenant->owner?->email)
              <div style="font-size:11px;color:var(--text-muted)">{{ $tenant->owner->email }}</div>
            @endif
          </td>
          <td style="font-size:13px">{{ $tenant->plan?->name ?? '—' }}</td>
          <td>
            @if($tenant->status === 'active')
              <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;background:rgba(16,185,129,.1);color:#059669">
                <i class="fas fa-check-circle" style="font-size:10px;margin-left:3px"></i>نشطة
              </span>
            @elseif($tenant->status === 'pending')
              <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;background:rgba(245,158,11,.12);color:#d97706">
                <i class="fas fa-hourglass-half" style="font-size:10px;margin-left:3px"></i>انتظار
              </span>
            @elseif($tenant->status === 'suspended')
              <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;background:rgba(239,68,68,.1);color:#dc2626">
                <i class="fas fa-pause-circle" style="font-size:10px;margin-left:3px"></i>معلّقة
              </span>
            @else
              <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;background:rgba(100,116,139,.1);color:var(--text-muted)">
                تجريبية
              </span>
            @endif
          </td>
          <td style="font-size:12px;color:var(--text-muted)">{{ $tenant->created_at->format('Y-m-d') }}</td>
          <td>
            @if($tenant->status === 'pending')
              <div style="display:flex;gap:6px;align-items:center">
                <form method="POST" action="{{ route('superadmin.tenants.approve', $tenant->id) }}">
                  @csrf
                  <button type="submit"
                    style="background:linear-gradient(135deg,#059669,#047857);color:#fff;border:none;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;gap:5px"
                    onclick="return confirm('قبول شبكة {{ addslashes($tenant->name) }} وتفعيل حساب المشرف؟')">
                    <i class="fas fa-check"></i> قبول
                  </button>
                </form>
                <form method="POST" action="{{ route('superadmin.tenants.reject', $tenant->id) }}">
                  @csrf
                  <button type="submit"
                    style="background:rgba(239,68,68,.08);color:#dc2626;border:1px solid rgba(239,68,68,.2);padding:7px 14px;border-radius:8px;font-size:12px;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;gap:5px"
                    onclick="return confirm('رفض طلب شبكة {{ addslashes($tenant->name) }}؟')">
                    <i class="fas fa-times"></i> رفض
                  </button>
                </form>
                <a href="{{ route('superadmin.tenants.edit', $tenant->id) }}"
                  style="background:var(--bg-main);border:1px solid var(--border);color:var(--text-muted);padding:7px 10px;border-radius:8px;font-size:12px;text-decoration:none">
                  <i class="fas fa-edit"></i>
                </a>
              </div>
            @else
              <div style="display:flex;gap:6px">
                <a href="{{ route('superadmin.tenants.edit', $tenant->id) }}" class="btn btn-outline" style="padding:5px 12px;font-size:12px">
                  <i class="fas fa-edit"></i>
                </a>
                <form method="POST" action="{{ route('superadmin.tenants.toggle', $tenant->id) }}" style="display:inline">
                  @csrf
                  <button type="submit"
                    class="btn {{ $tenant->status === 'active' ? 'btn-warning' : 'btn-success' }}"
                    style="padding:5px 12px;font-size:12px"
                    onclick="return confirm('{{ $tenant->status === 'active' ? 'تعليق الشبكة؟' : 'تفعيل الشبكة؟' }}')">
                    <i class="fas fa-{{ $tenant->status === 'active' ? 'pause' : 'play' }}"></i>
                  </button>
                </form>
              </div>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
            <i class="fas fa-network-wired" style="font-size:40px;opacity:.3;display:block;margin-bottom:12px"></i>
            لا توجد شبكات — <a href="{{ route('superadmin.tenants.create') }}">أضف الأولى</a>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  @if($tenants->hasPages())
    <div style="padding:16px 20px">{{ $tenants->links() }}</div>
  @endif
</div>

@endsection
