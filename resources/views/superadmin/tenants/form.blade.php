@extends('layouts.superadmin')
@section('title', isset($tenant) ? 'تعديل الشبكة' : 'إضافة شبكة جديدة')
@section('page-title', isset($tenant) ? 'تعديل الشبكة' : 'إضافة شبكة جديدة')

@section('content')

<div style="max-width:680px">
  <form method="POST" action="{{ isset($tenant) ? route('superadmin.tenants.update', $tenant->id) : route('superadmin.tenants.store') }}">
    @csrf
    @if(isset($tenant)) @method('PUT') @endif

    <div class="table-container" style="padding:0;margin-bottom:20px">
      <div class="table-header" style="padding:18px 20px">
        <h3 style="margin:0"><i class="fas fa-network-wired" style="color:var(--accent);margin-left:8px"></i> معلومات الشبكة</h3>
      </div>
      <div style="padding:24px;display:grid;gap:18px">

        <div>
          <label class="settings-label">اسم الشبكة <span style="color:#ef4444">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name ?? '') }}" required placeholder="مثال: شبكة النور">
        </div>

        <div>
          <label class="settings-label">الـ Subdomain <span style="color:#ef4444">*</span></label>
          <p style="font-size:13px;color:var(--text-muted);margin:4px 0 8px">أحرف صغيرة، أرقام، وشرطة فقط — مثال: <code>alnour</code></p>
          <input type="text" name="subdomain" class="form-control" dir="ltr"
            value="{{ old('subdomain', $tenant->subdomain ?? '') }}"
            required placeholder="alnour" pattern="[a-z0-9\-]+" @if(isset($tenant)) readonly @endif
            style="@if(isset($tenant)) background:var(--bg-main);cursor:not-allowed; @endif">
          @if(isset($tenant))
            <p style="font-size:12px;color:var(--text-muted);margin-top:4px"><i class="fas fa-info-circle"></i> الـ Subdomain لا يمكن تغييره بعد الإنشاء</p>
          @endif
        </div>

        <div>
          <label class="settings-label">باقة الاشتراك <span style="color:#ef4444">*</span></label>
          <select name="plan_id" class="form-control" required>
            <option value="">— اختر الباقة —</option>
            @foreach($plans as $plan)
              <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                {{ $plan->name }} ({{ $plan->price > 0 ? format_currency($plan->price) . '/شهر' : 'مجاني' }})
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="settings-label">الحالة</label>
          <select name="status" class="form-control">
            @foreach(['active' => 'نشطة', 'trial' => 'تجريبية', 'suspended' => 'معلّقة'] as $val => $label)
              <option value="{{ $val }}" {{ old('status', $tenant->status ?? 'active') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>

      </div>
    </div>

    @if(!isset($tenant))
    <div class="table-container" style="padding:0;margin-bottom:20px">
      <div class="table-header" style="padding:18px 20px">
        <h3 style="margin:0"><i class="fas fa-user-tie" style="color:var(--accent);margin-left:8px"></i> بيانات المشرف</h3>
      </div>
      <div style="padding:24px;display:grid;gap:18px">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          <div>
            <label class="settings-label">الاسم <span style="color:#ef4444">*</span></label>
            <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}" required placeholder="أحمد محمد">
          </div>
          <div>
            <label class="settings-label">رقم الهاتف <span style="color:#ef4444">*</span></label>
            <input type="text" name="admin_phone" class="form-control" dir="ltr" value="{{ old('admin_phone') }}" required placeholder="05xxxxxxxx">
          </div>
        </div>

        <div>
          <label class="settings-label">البريد الإلكتروني <span style="color:#ef4444">*</span></label>
          <input type="email" name="admin_email" class="form-control" dir="ltr" value="{{ old('admin_email') }}" required placeholder="admin@network.com">
        </div>

        <div>
          <label class="settings-label">كلمة المرور <span style="color:#ef4444">*</span></label>
          <input type="password" name="admin_password" class="form-control" required minlength="8" placeholder="8 أحرف على الأقل">
        </div>

      </div>
    </div>
    @endif

    <div style="display:flex;gap:12px">
      <button type="submit" class="btn btn-primary" style="padding:12px 28px">
        <i class="fas fa-save"></i> {{ isset($tenant) ? 'حفظ التعديلات' : 'إنشاء الشبكة' }}
      </button>
      <a href="{{ route('superadmin.tenants') }}" class="btn btn-outline" style="padding:12px 20px">إلغاء</a>
    </div>

  </form>
</div>

@push('styles')
<style>
  .form-control { width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'Tajawal',sans-serif;background:var(--bg-main);color:var(--text-dark) }
  .form-control:focus { outline:none;border-color:var(--accent) }
  .settings-label { font-weight:700;font-size:14px;color:var(--text-dark);display:block;margin-bottom:6px }
</style>
@endpush

@endsection
