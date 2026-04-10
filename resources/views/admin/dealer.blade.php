@extends('layouts.admin')
@section('title', 'صفحة الديلر')
@section('page-title', 'الديلر')

@section('content')
<div class="page-header">
  <h1><i class="fas fa-store"></i> صفحة الديلر</h1>
  <div style="display:flex;gap:8px">
    @if(!empty($url) && $url !== 'https://example.com')
      <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="font-size:13px">
        <i class="fas fa-external-link-alt"></i> فتح في تبويب جديد
      </a>
    @endif
    <a href="{{ route('admin.settings') }}" class="btn btn-outline" style="font-size:13px">
      <i class="fas fa-cog"></i> تغيير الرابط
    </a>
  </div>
</div>

@if(!empty($url) && $url !== 'https://example.com')
  <div style="background:var(--bg-card);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);position:relative;min-height:400px" id="dealerWrapper">

    {{-- Loading spinner --}}
    <div id="dealerLoading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:var(--bg-main);z-index:2">
      <div style="text-align:center;color:var(--text-muted)">
        <i class="fas fa-spinner fa-spin" style="font-size:32px;display:block;margin-bottom:12px"></i>
        <p>جارٍ تحميل صفحة الديلر...</p>
      </div>
    </div>

    {{-- Fallback message (hidden initially) --}}
    <div id="dealerFallback" style="display:none;text-align:center;padding:70px 30px">
      <div style="width:72px;height:72px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
        <i class="fas fa-exclamation-triangle" style="font-size:30px;color:#d97706"></i>
      </div>
      <h3 style="margin-bottom:10px;font-size:18px">تعذّر تحميل الصفحة داخل الإطار</h3>
      <p style="color:var(--text-muted);margin-bottom:24px;font-size:14px;max-width:400px;margin-left:auto;margin-right:auto">
        الموقع يمنع العرض داخل iframe بسبب إعدادات الأمان (X-Frame-Options).
        يمكنك فتحه مباشرةً في نافذة جديدة.
      </p>
      <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
        class="btn btn-primary" style="font-size:15px;padding:14px 32px">
        <i class="fas fa-external-link-alt"></i> فتح في نافذة جديدة
      </a>
      <p style="color:var(--text-muted);margin-top:16px;font-size:12px;word-break:break-all;max-width:500px;margin-inline:auto">
        <i class="fas fa-link"></i> {{ $url }}
      </p>
    </div>

    <iframe id="dealerFrame"
      src="{{ $url }}"
      style="width:100%;height:calc(100vh - 200px);border:none;display:block;position:relative;z-index:1;min-height:400px"
      sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-top-navigation"
      referrerpolicy="no-referrer"
      onload="handleDealerLoad()"
      onerror="handleDealerError()">
    </iframe>
  </div>

@else
  <div class="empty-state" style="padding:80px 20px;text-align:center">
    <div style="width:80px;height:80px;border-radius:50%;background:var(--bg-main);display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
      <i class="fas fa-store" style="font-size:36px;color:var(--text-muted)"></i>
    </div>
    <h3 style="margin-bottom:10px;font-size:18px">لم يتم تعيين رابط الديلر</h3>
    <p style="color:var(--text-muted);margin-bottom:20px">قم بتعيين رابط الديلر من صفحة الإعدادات</p>
    <a href="{{ route('admin.settings') }}" class="btn btn-primary"><i class="fas fa-cog"></i> الإعدادات</a>
  </div>
@endif
@endsection

@push('scripts')
<script>
(function() {
  var _loaded = false;

  window.handleDealerLoad = function() {
    _loaded = true;
    document.getElementById('dealerLoading').style.display = 'none';
    // Check if the frame actually loaded content (cross-origin detection)
    try {
      var frame = document.getElementById('dealerFrame');
      var loc   = frame.contentWindow.location.href;
      if (!loc || loc === 'about:blank') { showFallback(); return; }
      // Success: frame loaded
    } catch(e) {
      // Cross-origin — means frame DID load external content (that's fine)
      // The error is expected for cross-origin iframes
    }
  };

  window.handleDealerError = function() { showFallback(); };

  function showFallback() {
    document.getElementById('dealerLoading').style.display = 'none';
    document.getElementById('dealerFrame').style.display   = 'none';
    document.getElementById('dealerFallback').style.display = 'block';
  }

  // Safety timeout: 12 seconds
  setTimeout(function() {
    if (!_loaded) showFallback();
  }, 12000);
})();
</script>
@endpush
