@extends('layouts.admin')
@section('title', 'طلبك قيد المراجعة')
@section('page-title', 'حالة الطلب')

@section('content')
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center">
  <div style="max-width:480px;width:100%;text-align:center">

    <div style="width:96px;height:96px;background:linear-gradient(135deg,#fef3c7,#fde68a);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 28px;font-size:40px;animation:pulse 2.5s ease-in-out infinite">
      ⏳
    </div>

    <h1 style="font-size:24px;font-weight:800;color:var(--text-dark);margin-bottom:12px">
      شبكتك قيد المراجعة
    </h1>
    <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:32px">
      تم استلام طلبك بنجاح. يراجع فريقنا الطلب وسيتم تفعيل لوحة التحكم خلال <strong>24 ساعة</strong>.
      <br>ستتلقى إشعاراً فور القبول.
    </p>

    @if(auth()->user()->tenant)
    <div style="background:var(--bg-main);border:1px solid var(--border);border-radius:12px;padding:16px 20px;margin-bottom:28px;text-align:right">
      <div style="display:flex;align-items:center;gap:10px;padding:6px 0;font-size:14px;color:var(--text-dark)">
        <i class="fas fa-network-wired" style="color:var(--accent);width:16px;text-align:center"></i>
        <span>اسم الشبكة: <strong>{{ auth()->user()->tenant->name }}</strong></span>
      </div>
      <div style="display:flex;align-items:center;gap:10px;padding:6px 0;font-size:14px;color:var(--text-dark)">
        <i class="fas fa-link" style="color:var(--accent);width:16px;text-align:center"></i>
        <span dir="ltr" style="font-family:monospace;font-size:13px">{{ auth()->user()->tenant->subdomain }}.{{ config('app.domain','بطاقتي.com') }}</span>
      </div>
      <div style="display:flex;align-items:center;gap:10px;padding:6px 0;font-size:14px;color:var(--text-dark)">
        <i class="fas fa-tag" style="color:var(--accent);width:16px;text-align:center"></i>
        <span>الباقة: <strong>{{ auth()->user()->tenant->plan?->name ?? '—' }}</strong></span>
      </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" style="background:rgba(100,116,139,.08);color:var(--text-muted);border:1px solid var(--border);padding:10px 24px;border-radius:10px;font-size:14px;font-family:inherit;cursor:pointer">
        <i class="fas fa-sign-out-alt" style="margin-left:6px"></i> تسجيل الخروج
      </button>
    </form>

  </div>
</div>

@push('styles')
<style>
@keyframes pulse {
  0%,100% { transform:scale(1); }
  50%      { transform:scale(1.06); }
}
</style>
@endpush
@endsection
