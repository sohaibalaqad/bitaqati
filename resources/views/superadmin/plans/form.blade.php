@extends('layouts.superadmin')
@section('title', isset($plan) ? 'تعديل الباقة' : 'باقة جديدة')
@section('page-title', isset($plan) ? 'تعديل الباقة' : 'باقة جديدة')

@section('content')

<div style="max-width:520px">
  <form method="POST" action="{{ isset($plan) ? route('superadmin.plans.update', $plan->id) : route('superadmin.plans.store') }}">
    @csrf
    @if(isset($plan)) @method('PUT') @endif

    <div class="table-container" style="padding:0;margin-bottom:20px">
      <div class="table-header" style="padding:18px 20px">
        <h3 style="margin:0"><i class="fas fa-tag" style="color:var(--accent);margin-left:8px"></i> تفاصيل الباقة</h3>
      </div>
      <div style="padding:24px;display:grid;gap:18px">

        <div>
          <label class="settings-label">اسم الباقة <span style="color:#ef4444">*</span></label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name ?? '') }}" required placeholder="مثال: Starter">
        </div>

        <div>
          <label class="settings-label">السعر الشهري</label>
          <p style="font-size:13px;color:var(--text-muted);margin:4px 0 8px">اتركه 0 للباقات المجانية</p>
          <input type="number" name="price" class="form-control" dir="ltr" step="0.01" min="0"
            value="{{ old('price', $plan->price ?? 0) }}" required placeholder="0.00">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
          <div>
            <label class="settings-label" style="font-size:13px">حد البطاقات</label>
            <input type="number" name="max_cards" class="form-control" dir="ltr" min="0"
              value="{{ old('max_cards', $plan->max_cards ?? 0) }}" required>
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px">0 = غير محدود</p>
          </div>
          <div>
            <label class="settings-label" style="font-size:13px">حد المستخدمين</label>
            <input type="number" name="max_users" class="form-control" dir="ltr" min="0"
              value="{{ old('max_users', $plan->max_users ?? 0) }}" required>
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px">0 = غير محدود</p>
          </div>
          <div>
            <label class="settings-label" style="font-size:13px">حد الباقات</label>
            <input type="number" name="max_packages" class="form-control" dir="ltr" min="0"
              value="{{ old('max_packages', $plan->max_packages ?? 0) }}" required>
            <p style="font-size:11px;color:var(--text-muted);margin-top:4px">0 = غير محدود</p>
          </div>
        </div>

      </div>
    </div>

    <div style="display:flex;gap:12px">
      <button type="submit" class="btn btn-primary" style="padding:12px 28px">
        <i class="fas fa-save"></i> {{ isset($plan) ? 'حفظ التعديلات' : 'إنشاء الباقة' }}
      </button>
      <a href="{{ route('superadmin.plans') }}" class="btn btn-outline" style="padding:12px 20px">إلغاء</a>
    </div>

  </form>
</div>

@push('styles')
<style>
  .form-control { width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:14px;font-family:'Tajawal',sans-serif;background:var(--bg-main);color:var(--text-dark);box-sizing:border-box }
  .form-control:focus { outline:none;border-color:var(--accent) }
  .settings-label { font-weight:700;font-size:14px;color:var(--text-dark);display:block;margin-bottom:6px }
</style>
@endpush

@endsection
