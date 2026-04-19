@extends('layouts.superadmin')
@section('title', 'باقات الاشتراك')
@section('page-title', 'باقات الاشتراك')

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
  <h1><i class="fas fa-tags"></i> باقات الاشتراك</h1>
  <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i> باقة جديدة
  </a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
  @forelse($plans as $plan)
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:24px;position:relative">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px">
        <div>
          <h3 style="margin:0 0 4px;font-size:18px">{{ $plan->name }}</h3>
          <div style="font-size:22px;font-weight:800;color:var(--accent)">
            {{ $plan->price > 0 ? format_currency($plan->price) : 'مجاني' }}
            @if($plan->price > 0) <span style="font-size:13px;font-weight:400;color:var(--text-muted)">/ شهر</span> @endif
          </div>
        </div>
        <span style="background:var(--accent-light);color:var(--accent);font-size:13px;font-weight:700;padding:4px 12px;border-radius:20px">
          {{ $plan->tenants_count }} شبكة
        </span>
      </div>

      <div style="display:grid;gap:8px;margin-bottom:20px">
        <div style="display:flex;justify-content:space-between;font-size:14px">
          <span style="color:var(--text-muted)">الحد الأقصى للبطاقات</span>
          <strong>{{ $plan->max_cards == 0 ? 'غير محدود' : number_format($plan->max_cards) }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px">
          <span style="color:var(--text-muted)">الحد الأقصى للمستخدمين</span>
          <strong>{{ $plan->max_users == 0 ? 'غير محدود' : number_format($plan->max_users) }}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:14px">
          <span style="color:var(--text-muted)">الحد الأقصى للباقات</span>
          <strong>{{ $plan->max_packages == 0 ? 'غير محدود' : number_format($plan->max_packages) }}</strong>
        </div>
      </div>

      <div style="display:flex;gap:8px">
        <a href="{{ route('superadmin.plans.edit', $plan->id) }}" class="btn btn-outline" style="flex:1;text-align:center">
          <i class="fas fa-edit"></i> تعديل
        </a>
        @if($plan->tenants_count == 0)
          <form method="POST" action="{{ route('superadmin.plans.destroy', $plan->id) }}" style="flex:0">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('حذف الباقة؟')" style="padding:10px 14px">
              <i class="fas fa-trash"></i>
            </button>
          </form>
        @endif
      </div>
    </div>
  @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-muted)">
      <i class="fas fa-tags" style="font-size:48px;opacity:.3;display:block;margin-bottom:16px"></i>
      لا توجد باقات — <a href="{{ route('superadmin.plans.create') }}">أضف الأولى</a>
    </div>
  @endforelse
</div>

@endsection
