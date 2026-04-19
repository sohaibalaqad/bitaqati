@extends('layouts.superadmin')

@section('title', 'إدارة خطط الاشتراك')
@section('page-title', 'إدارة خطط الاشتراك')

@push('styles')
<style>
.plan-table { width:100%; border-collapse:collapse; font-size:14px; }
.plan-table th {
    text-align:right; padding:12px 16px; font-size:12px; font-weight:700;
    color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px;
    border-bottom:2px solid var(--border); background:var(--bg-main);
}
.plan-table td {
    padding:14px 16px; border-bottom:1px solid var(--border); vertical-align:middle;
}
.plan-table tr:last-child td { border-bottom:none; }
.plan-table tr:hover td { background:rgba(124,58,237,.02); }
.popular-badge {
    display:inline-flex; align-items:center; gap:4px; padding:3px 10px;
    background:rgba(251,146,60,.12); color:#ea580c; border-radius:20px;
    font-size:11px; font-weight:700;
}
.form-panel {
    background:#fff; border:1px solid var(--border); border-radius:var(--radius);
    padding:24px; margin-bottom:24px;
}
.form-panel h3 { font-size:16px; font-weight:800; color:var(--text-dark); margin-bottom:20px; }
.field-row   { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:14px; }
.field-row-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:14px; margin-bottom:14px; }
.field-group label { display:block; font-size:12px; font-weight:700; color:var(--text-dark); margin-bottom:5px; }
.field-group input, .field-group textarea {
    width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:8px;
    font-size:13px; color:var(--text-dark); background:#fff; font-family:inherit;
}
.field-group input:focus, .field-group textarea:focus {
    outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(124,58,237,.1);
}
.switch-wrap { display:flex; align-items:center; gap:8px; cursor:pointer; padding-top:22px; }
.switch-wrap input[type=checkbox] { width:16px; height:16px; accent-color:var(--accent); }
.edit-modal {
    display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
    z-index:1000; align-items:center; justify-content:center;
}
.edit-modal.open { display:flex; }
.modal-box {
    background:#fff; border-radius:var(--radius); padding:28px;
    width:100%; max-width:640px; max-height:90vh; overflow-y:auto;
}
.modal-box h3 { font-size:16px; font-weight:800; margin-bottom:20px; color:var(--text-dark); }
</style>
@endpush

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
  <div style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('superadmin.homepage.index') }}" style="color:var(--text-muted);text-decoration:none;font-size:14px;display:flex;align-items:center;gap:4px">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      الصفحة الرئيسية
    </a>
    <span style="color:var(--border)">/</span>
    <h1 style="font-size:20px;font-weight:800;color:var(--text-dark)">إدارة خطط الاشتراك</h1>
  </div>
</div>

{{-- ─── Add Plan Form ─── --}}
<div class="form-panel">
  <h3>
    <svg width="16" height="16" style="vertical-align:-3px;margin-left:6px;color:var(--accent)" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    إضافة خطة جديدة
  </h3>
  <form action="{{ route('superadmin.homepage.plans.store') }}" method="POST">
    @csrf
    <div class="field-row-4">
      <div class="field-group">
        <label>اسم الخطة <span style="color:#dc2626">*</span></label>
        <input type="text" name="name" required placeholder="مثال: الخطة المجانية" value="{{ old('name') }}">
      </div>
      <div class="field-group">
        <label>السعر (شهري)</label>
        <input type="number" name="price" min="0" step="0.01" placeholder="0" value="{{ old('price', 0) }}">
      </div>
      <div class="field-group">
        <label>أقصى عدد بطاقات (0 = غير محدود)</label>
        <input type="number" name="max_cards" min="0" placeholder="0" value="{{ old('max_cards', 0) }}">
      </div>
      <div class="field-group">
        <label>أقصى عدد مستخدمين</label>
        <input type="number" name="max_users" min="0" placeholder="0" value="{{ old('max_users', 0) }}">
      </div>
    </div>
    <div class="field-row" style="align-items:start">
      <div class="field-group" style="grid-column:span 2">
        <label>المميزات (سطر لكل ميزة)</label>
        <textarea name="features" rows="4" placeholder="بطاقات غير محدودة&#10;دعم 24/7&#10;لوحة تحكم متقدمة">{{ old('features') }}</textarea>
      </div>
      <div style="display:flex;flex-direction:column;gap:10px;padding-top:20px">
        <label class="switch-wrap">
          <input type="hidden" name="is_popular" value="0">
          <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}>
          <span style="font-size:13px;font-weight:600;color:var(--text-dark)">خطة مميزة (Popular)</span>
        </label>
      </div>
    </div>
    <button type="submit" class="btn btn-primary" style="padding:10px 22px;font-size:14px">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      إضافة الخطة
    </button>
  </form>
</div>

{{-- ─── Plans Table ─── --}}
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden">
  <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <h2 style="font-size:15px;font-weight:800;color:var(--text-dark)">الخطط الحالية ({{ $plans->count() }})</h2>
  </div>
  @if($plans->isEmpty())
    <div style="padding:40px;text-align:center;color:var(--text-muted);font-size:14px">
      لا توجد خطط بعد. أضف أول خطة من النموذج أعلاه.
    </div>
  @else
  <div style="overflow-x:auto">
    <table class="plan-table">
      <thead>
        <tr>
          <th>الاسم</th>
          <th>السعر</th>
          <th>البطاقات</th>
          <th>المستخدمين</th>
          <th>المميزات</th>
          <th>الحالة</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($plans as $plan)
        <tr>
          <td>
            <div style="font-weight:700;color:var(--text-dark)">{{ $plan->name }}</div>
            @if($plan->is_popular)
              <span class="popular-badge" style="margin-top:4px">⭐ مميزة</span>
            @endif
          </td>
          <td style="font-weight:700;color:var(--accent)">
            {{ $plan->price == 0 ? 'مجاناً' : number_format($plan->price, 2) . ' ر.س' }}
          </td>
          <td>{{ $plan->max_cards == 0 ? 'غير محدود' : number_format($plan->max_cards) }}</td>
          <td>{{ $plan->max_users == 0 ? 'غير محدود' : number_format($plan->max_users) }}</td>
          <td style="max-width:200px">
            @if(!empty($plan->features))
              <ul style="margin:0;padding-right:16px;font-size:12px;color:var(--text-muted);line-height:1.7">
                @foreach(array_slice($plan->features, 0, 3) as $f)
                  <li>{{ $f }}</li>
                @endforeach
                @if(count($plan->features) > 3)
                  <li style="color:var(--accent)">+{{ count($plan->features) - 3 }} أخرى</li>
                @endif
              </ul>
            @else
              <span style="color:var(--text-muted);font-size:12px">—</span>
            @endif
          </td>
          <td>
            @if($plan->is_active)
              <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(16,185,129,.1);color:#059669">مفعّل</span>
            @else
              <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:rgba(100,116,139,.1);color:var(--text-muted)">معطّل</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:8px;justify-content:flex-end">
              <button onclick="openEdit({{ $plan->id }})" class="btn btn-outline" style="font-size:12px;padding:6px 12px">تعديل</button>
              <form action="{{ route('superadmin.homepage.plans.destroy', $plan) }}" method="POST"
                onsubmit="return confirm('حذف خطة {{ $plan->name }} نهائياً؟')">
                @csrf @method('DELETE')
                <button class="btn" style="font-size:12px;padding:6px 12px;background:rgba(220,38,38,.07);color:#dc2626;border:1px solid rgba(220,38,38,.2)">حذف</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- ─── Edit Modals ─── --}}
@foreach($plans as $plan)
<div class="edit-modal" id="modal-{{ $plan->id }}">
  <div class="modal-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <h3 style="margin:0">تعديل: {{ $plan->name }}</h3>
      <button onclick="closeEdit({{ $plan->id }})" style="border:none;background:none;font-size:22px;cursor:pointer;color:var(--text-muted)">×</button>
    </div>
    <form action="{{ route('superadmin.homepage.plans.update', $plan) }}" method="POST">
      @csrf @method('PUT')
      <div class="field-row">
        <div class="field-group">
          <label>اسم الخطة <span style="color:#dc2626">*</span></label>
          <input type="text" name="name" required value="{{ $plan->name }}">
        </div>
        <div class="field-group">
          <label>السعر</label>
          <input type="number" name="price" min="0" step="0.01" value="{{ $plan->price }}">
        </div>
        <div class="field-group">
          <label>أقصى بطاقات</label>
          <input type="number" name="max_cards" min="0" value="{{ $plan->max_cards }}">
        </div>
      </div>
      <div class="field-row">
        <div class="field-group">
          <label>أقصى مستخدمين</label>
          <input type="number" name="max_users" min="0" value="{{ $plan->max_users }}">
        </div>
        <div class="field-group" style="grid-column:span 2">
          <label>المميزات (سطر لكل ميزة)</label>
          <textarea name="features" rows="4">{{ implode("\n", $plan->features ?? []) }}</textarea>
        </div>
      </div>
      <div style="display:flex;gap:20px;margin-bottom:16px">
        <label class="switch-wrap" style="padding-top:0">
          <input type="hidden" name="is_popular" value="0">
          <input type="checkbox" name="is_popular" value="1" {{ $plan->is_popular ? 'checked' : '' }}>
          <span style="font-size:13px;font-weight:600">خطة مميزة</span>
        </label>
        <label class="switch-wrap" style="padding-top:0">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}>
          <span style="font-size:13px;font-weight:600">مفعّل</span>
        </label>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary" style="padding:10px 22px;font-size:14px">حفظ التغييرات</button>
        <button type="button" onclick="closeEdit({{ $plan->id }})" class="btn btn-outline" style="padding:10px 22px;font-size:14px">إلغاء</button>
      </div>
    </form>
  </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
function openEdit(id)  { document.getElementById('modal-' + id).classList.add('open'); }
function closeEdit(id) { document.getElementById('modal-' + id).classList.remove('open'); }
document.querySelectorAll('.edit-modal').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
</script>
@endpush
