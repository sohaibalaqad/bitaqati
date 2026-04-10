@extends('layouts.admin')

@section('title', 'الإعدادات العامة')
@section('page-title', 'الإعدادات العامة')

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
    <h1><i class="fas fa-cog"></i> الإعدادات العامة</h1>
  </div>

  <form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="table-container" style="padding:0;overflow:visible">
      <div class="table-header" style="padding:20px 24px">
        <h3><i class="fas fa-network-wired" style="color:var(--accent);margin-left:8px"></i> إعدادات الشبكة</h3>
      </div>
      <div style="padding:24px;display:grid;gap:20px">

        <div class="form-group-settings">
          <label class="settings-label">اسم الشبكة</label>
          <p class="settings-hint">الاسم الذي يظهر للعملاء في لوحة التحكم</p>
          <input type="text"
            name="network_name"
            class="form-control"
            value="{{ old('network_name', $settings->get('network_name')?->value ?? '') }}"
            placeholder="مثال: XNET WiFi">
          @error('network_name')
            <div style="color:#dc2626;font-size:13px;margin-top:4px"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

        <div class="form-group-settings">
          <label class="settings-label">رابط صفحة تسجيل الدخول MikroTik</label>
          <p class="settings-hint">الرابط الذي يفتح عند النقر على زر "اتصال" في بطاقة المستخدم</p>
          <input type="url"
            name="mikrotik_url"
            class="form-control"
            value="{{ old('mikrotik_url', $settings->get('mikrotik_url')?->value ?? '') }}"
            placeholder="https://192.168.1.1/login"
            dir="ltr">
          @error('mikrotik_url')
            <div style="color:#dc2626;font-size:13px;margin-top:4px"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

        <div class="form-group-settings">
          <label class="settings-label">رابط الديلر</label>
          <p class="settings-hint">الرابط الذي يُعرض في صفحة الديلر</p>
          <input type="url"
            name="dealer_url"
            class="form-control"
            value="{{ old('dealer_url', $settings->get('dealer_url')?->value ?? '') }}"
            placeholder="https://dealer.example.com"
            dir="ltr">
          @error('dealer_url')
            <div style="color:#dc2626;font-size:13px;margin-top:4px"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

      </div>
    </div>

    <div class="table-container" style="padding:0;overflow:visible;margin-top:20px">
      <div class="table-header" style="padding:20px 24px">
        <h3><i class="fas fa-headset" style="color:var(--accent);margin-left:8px"></i> إعدادات الدعم والمالية</h3>
      </div>
      <div style="padding:24px;display:grid;gap:20px">

        <div class="form-group-settings">
          <label class="settings-label">رقم الدعم الفني (واتساب / هاتف)</label>
          <p class="settings-hint">يُعرض للعملاء في قسم المساعدة</p>
          <input type="text"
            name="support_phone"
            class="form-control"
            value="{{ old('support_phone', $settings->get('support_phone')?->value ?? '') }}"
            placeholder="+972501234567"
            dir="ltr">
          @error('support_phone')
            <div style="color:#dc2626;font-size:13px;margin-top:4px"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

        <div class="form-group-settings">
          <label class="settings-label">العملة الأساسية</label>
          <p class="settings-hint">تُطبَّق على جميع الأسعار والفواتير في كامل الموقع تلقائياً</p>
          <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
            <select name="currency" class="form-control" style="max-width:320px" id="currencySelect" onchange="updateCurrencyPreview(this.value)">
              @php
                $activeCurrency = old('currency', $settings->get('currency')?->value ?? 'ILS');
              @endphp
              @foreach(\App\Helpers\CurrencyHelper::CURRENCIES as $code => $meta)
                <option value="{{ $code }}" {{ $activeCurrency === $code ? 'selected' : '' }}>
                  {{ $meta['symbol'] }} — {{ $meta['name'] }} ({{ $code }})
                </option>
              @endforeach
            </select>
            <div id="currencyPreview" style="background:var(--bg-main);border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:8px 16px;font-size:15px;font-weight:700;color:var(--accent);min-width:90px;text-align:center">
              {{ format_currency(99.50) }}
            </div>
          </div>
          <p style="font-size:12px;color:var(--text-muted);margin-top:6px"><i class="fas fa-info-circle"></i> المعاينة: كيف ستظهر الأسعار بعد الحفظ</p>
          @error('currency')
            <div style="color:#dc2626;font-size:13px;margin-top:4px"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
          @enderror
        </div>

      </div>
    </div>

    <div style="margin-top:24px;display:flex;gap:12px">
      <button type="submit" class="btn btn-primary" style="padding:12px 28px;font-size:15px">
        <i class="fas fa-save"></i> حفظ الإعدادات
      </button>
    </div>

  </form>

  {{-- ===== Platform Info (read-only) ===== --}}
  <div class="table-container" style="padding:0;overflow:visible;margin-top:28px">
    <div class="table-header" style="padding:20px 24px">
      <h3><i class="fas fa-code-branch" style="color:var(--accent);margin-left:8px"></i> معلومات المنصة</h3>
    </div>
    <div style="padding:24px;display:grid;gap:16px">

      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
          <div style="font-weight:700;font-size:14px;color:var(--text-dark)">إصدار المنصة</div>
          <div style="font-size:13px;color:var(--text-muted)">الإصدار الحالي من النظام</div>
        </div>
        <div style="background:var(--accent);color:#fff;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:700;font-family:monospace">
          v{{ config('platform.version') }}
        </div>
      </div>

      <div style="border-top:1px solid var(--border);padding-top:16px">
        <div style="font-weight:700;font-size:14px;color:var(--text-dark);margin-bottom:12px">
          <i class="fas fa-toggle-on" style="color:var(--accent);margin-left:6px"></i> حالة الميزات
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:8px">
          @foreach(config('platform.features') as $flag => $enabled)
            @php
              $labels = [
                'packages'          => 'الباقات',
                'card_purchase'     => 'شراء البطاقات',
                'recharge_requests' => 'طلبات الشحن',
                'notifications'     => 'الإشعارات',
                'support_tickets'   => 'الدعم الفني',
                'chat'              => 'الدردشة',
                'dealer_portal'     => 'بوابة الديلر',
                'advanced_reports'  => 'تقارير متقدمة (v2)',
                'multi_currency'    => 'عملات متعددة (v2)',
                'sms_notifications' => 'إشعارات SMS (v2)',
                'multi_tenant'      => 'SaaS متعدد (v3)',
                'api_access'        => 'API عام (v3)',
                'white_label'       => 'White Label (v3)',
              ];
            @endphp
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:var(--bg-main);border-radius:var(--radius-sm)">
              <span style="width:8px;height:8px;border-radius:50%;background:{{ $enabled ? '#10b981' : '#cbd5e1' }};flex-shrink:0"></span>
              <span style="font-size:13px;color:{{ $enabled ? 'var(--text-dark)' : 'var(--text-muted)' }}">
                {{ $labels[$flag] ?? $flag }}
              </span>
            </div>
          @endforeach
        </div>
        <p style="font-size:12px;color:var(--text-muted);margin-top:10px">
          <i class="fas fa-info-circle"></i> لتفعيل ميزة أو تعطيلها، عدّل ملف <code style="background:var(--bg-main);padding:2px 6px;border-radius:4px">.env</code> ثم أعد تشغيل الخادم
        </p>
      </div>

    </div>
  </div>

  </div>

@endsection

@push('scripts')
<script>
var currencyMap = @json(\App\Helpers\CurrencyHelper::CURRENCIES);

function updateCurrencyPreview(code) {
  var meta = currencyMap[code] || { symbol: code, position: 'after' };
  var amt  = '99.50';
  var label = meta.position === 'before' ? meta.symbol + amt : amt + ' ' + meta.symbol;
  document.getElementById('currencyPreview').textContent = label;
}
</script>
@endpush

@push('styles')
<style>
  .form-group-settings { display:flex; flex-direction:column; gap:4px; }
  .settings-label { font-weight:700; font-size:14px; color:var(--text-dark); }
  .settings-hint { font-size:13px; color:var(--text-muted); margin-bottom:6px; }
  .form-control {
    width:100%; padding:10px 14px; border:1.5px solid var(--border);
    border-radius:var(--radius-sm); font-size:14px; font-family:'Tajawal',sans-serif;
    background:var(--bg-main); color:var(--text-dark); transition:border-color 0.2s;
  }
  .form-control:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(16,185,129,0.1); }
</style>
@endpush
