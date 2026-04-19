<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ config('app.name', 'بطاقتي') }} — منصة إدارة شبكات الإنترنت</title>
  <meta name="description" content="منصة SaaS متكاملة لمزودي خدمة الإنترنت — أنشئ بطاقات الإنترنت، راقب المستخدمين عبر MikroTik، وتابع أرباحك من لوحة تحكم واحدة.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --navy: #0F172A; --accent: #0EA5E9; --accent2: #38BDF8; --white: #FFFFFF;
      --gray-50: #F8FAFC; --gray-100: #F1F5F9; --gray-200: #E2E8F0;
      --gray-400: #94A3B8; --gray-600: #475569; --gray-700: #334155;
      --radius: 12px; --shadow: 0 4px 24px rgba(15,23,42,.08);
    }
    html { scroll-behavior: smooth; }
    body { font-family: 'Tajawal', sans-serif; color: var(--navy); background: var(--white); overflow-x: hidden; }
    a { text-decoration: none; color: inherit; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 13px 26px; border-radius: 10px; font-size: 15px; font-weight: 700; border: none; cursor: pointer; transition: all .2s; font-family: 'Tajawal', sans-serif; }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-primary:hover { background: #0284C7; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(14,165,233,.35); }
    .btn-outline { background: transparent; border: 2px solid var(--gray-200); color: var(--navy); }
    .btn-outline:hover { border-color: var(--accent); color: var(--accent); }
    .section-tag { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; background: rgba(14,165,233,.08); color: var(--accent); border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 14px; }
    .section-title { font-size: clamp(26px,4vw,42px); font-weight: 900; color: var(--navy); line-height: 1.2; margin-bottom: 14px; }
    .section-sub { font-size: 17px; color: var(--gray-600); line-height: 1.7; max-width: 600px; }

    /* ── Navbar ── */
    .navbar { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,.92); backdrop-filter: blur(12px); border-bottom: 1px solid var(--gray-200); }
    .navbar-inner { display: flex; align-items: center; justify-content: space-between; height: 68px; }
    .navbar-logo { font-size: 22px; font-weight: 900; color: var(--navy); display: flex; align-items: center; gap: 8px; }
    .navbar-logo span { color: var(--accent); }
    .navbar-links { display: flex; align-items: center; gap: 28px; }
    .navbar-links a { font-size: 15px; font-weight: 600; color: var(--gray-600); transition: color .15s; }
    .navbar-links a:hover { color: var(--accent); }
    .navbar-actions { display: flex; align-items: center; gap: 10px; }

    /* ── Hero ── */
    .hero { padding: 90px 0 80px; background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%); overflow: hidden; position: relative; }
    .hero::after { content:''; position:absolute; top:-100px; left:-100px; width:500px; height:500px; background:radial-gradient(circle, rgba(14,165,233,.08), transparent 70%); pointer-events:none; }
    .hero-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
    .hero-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; background: rgba(14,165,233,.1); color: var(--accent); border-radius: 20px; font-size: 13px; font-weight: 700; margin-bottom: 20px; border: 1px solid rgba(14,165,233,.2); }
    .hero-title { font-size: clamp(32px,4.5vw,52px); font-weight: 900; color: var(--navy); line-height: 1.18; margin-bottom: 20px; white-space: pre-line; }
    .hero-title .accent { color: var(--accent); }
    .hero-sub { font-size: 17px; color: var(--gray-600); line-height: 1.75; margin-bottom: 32px; }
    .hero-actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .hero-trust { display: flex; align-items: center; gap: 16px; margin-top: 28px; flex-wrap: wrap; }
    .hero-trust-item { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--gray-600); font-weight: 600; }
    .hero-trust-item::before { content:'✓'; color: #10B981; font-weight: 900; }
    .hero-visual { display: flex; align-items: center; justify-content: center; }
    .dashboard-mock { background: var(--white); border-radius: 16px; box-shadow: 0 20px 60px rgba(15,23,42,.14); border: 1px solid var(--gray-200); overflow: hidden; width: 100%; max-width: 460px; }
    .dash-header { background: var(--navy); padding: 14px 18px; display: flex; align-items: center; gap: 8px; }
    .dash-dot { width: 10px; height: 10px; border-radius: 50%; }
    .dash-body { padding: 20px; }
    .dash-stat-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 16px; }
    .dash-stat { background: var(--gray-50); border-radius: 10px; padding: 14px 12px; text-align: center; }
    .dash-stat-v { font-size: 22px; font-weight: 900; color: var(--navy); }
    .dash-stat-l { font-size: 11px; color: var(--gray-400); margin-top: 4px; }
    .dash-chart { background: var(--gray-50); border-radius: 10px; padding: 14px; }
    .dash-bars { display: flex; align-items: flex-end; gap: 6px; height: 70px; }
    .dash-bar { flex: 1; border-radius: 4px 4px 0 0; background: var(--accent); opacity: .7; }

    /* ── Features ── */
    .features-section { padding: 90px 0; }
    .features-head { text-align: center; margin-bottom: 56px; }
    .features-head .section-sub { margin: 0 auto; }
    .features-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; }
    .feature-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 28px 24px; transition: all .2s; }
    .feature-card:hover { box-shadow: var(--shadow); transform: translateY(-3px); border-color: rgba(14,165,233,.2); }
    .feature-icon { width: 48px; height: 48px; background: rgba(14,165,233,.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
    .feature-icon svg { color: var(--accent); }
    .feature-title { font-size: 16px; font-weight: 800; color: var(--navy); margin-bottom: 8px; }
    .feature-desc { font-size: 14px; color: var(--gray-600); line-height: 1.65; }

    /* ── Pricing ── */
    .pricing-section { padding: 90px 0; background: var(--gray-50); }
    .pricing-head { text-align: center; margin-bottom: 52px; }
    .pricing-head .section-sub { margin: 0 auto; }
    .pricing-grid { display: grid; gap: 24px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); max-width: 1000px; margin: 0 auto; }
    .plan-card { background: var(--white); border: 2px solid var(--gray-200); border-radius: 16px; padding: 32px 28px; position: relative; transition: all .2s; }
    .plan-card:hover { box-shadow: 0 12px 40px rgba(15,23,42,.1); }
    .plan-card.popular { border-color: var(--accent); box-shadow: 0 0 0 4px rgba(14,165,233,.08); }
    .popular-tag { position: absolute; top: -14px; right: 50%; transform: translateX(50%); background: var(--accent); color: #fff; padding: 4px 16px; border-radius: 20px; font-size: 12px; font-weight: 800; white-space: nowrap; }
    .plan-name { font-size: 18px; font-weight: 800; margin-bottom: 8px; }
    .plan-price { font-size: 38px; font-weight: 900; color: var(--navy); line-height: 1; margin-bottom: 6px; }
    .plan-price sup { font-size: 18px; vertical-align: super; font-weight: 700; }
    .plan-price sub { font-size: 14px; color: var(--gray-400); font-weight: 500; }
    .plan-features { margin: 24px 0; display: flex; flex-direction: column; gap: 10px; }
    .plan-feat { display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--gray-700); }
    .plan-feat::before { content: '✓'; color: #10B981; font-weight: 900; flex-shrink: 0; }
    .plan-meta { font-size: 12px; color: var(--gray-400); margin-bottom: 20px; }
    .pricing-note { text-align: center; margin-top: 32px; font-size: 14px; color: var(--gray-600); font-weight: 500; }

    /* ── About / Stats ── */
    .about-section { padding: 90px 0; }
    .about-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
    .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 32px; }
    .stat-box { background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 12px; padding: 24px; text-align: center; }
    .stat-val { font-size: 32px; font-weight: 900; color: var(--accent); }
    .stat-lbl { font-size: 13px; color: var(--gray-600); margin-top: 4px; font-weight: 600; }
    .about-visual { display: flex; align-items: center; justify-content: center; }
    .network-diagram { width: 100%; max-width: 400px; padding: 40px; background: linear-gradient(135deg, #0F172A, #1E3A5F); border-radius: 20px; color: #fff; text-align: center; }

    /* ── Testimonials ── */
    .testimonials-section { padding: 90px 0; background: var(--gray-50); }
    .testimonials-head { text-align: center; margin-bottom: 52px; }
    .testimonials-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; }
    .testimonial-card { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius); padding: 28px 24px; }
    .stars { color: #F59E0B; font-size: 16px; margin-bottom: 12px; }
    .testimonial-text { font-size: 14px; color: var(--gray-700); line-height: 1.7; margin-bottom: 20px; font-style: italic; }
    .testimonial-author { display: flex; align-items: center; gap: 10px; }
    .author-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #0284C7); display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 800; color: #fff; flex-shrink: 0; }
    .author-name { font-size: 14px; font-weight: 800; color: var(--navy); }
    .author-role { font-size: 12px; color: var(--gray-400); margin-top: 2px; }

    /* ── FAQ ── */
    .faq-section { padding: 90px 0; }
    .faq-head { text-align: center; margin-bottom: 52px; }
    .faq-list { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }
    .faq-item { border: 1px solid var(--gray-200); border-radius: 12px; overflow: hidden; }
    .faq-q { padding: 18px 22px; font-size: 15px; font-weight: 700; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
    .faq-q:hover { background: var(--gray-50); }
    .faq-icon { color: var(--accent); transition: transform .2s; flex-shrink: 0; }
    .faq-a { display: none; padding: 0 22px 18px; font-size: 14px; color: var(--gray-600); line-height: 1.75; border-top: 1px solid var(--gray-100); padding-top: 14px; }
    .faq-item.open .faq-a { display: block; }
    .faq-item.open .faq-icon { transform: rotate(45deg); }

    /* ── CTA ── */
    .cta-section { padding: 90px 0; background: var(--navy); text-align: center; position: relative; overflow: hidden; }
    .cta-section::before { content:''; position:absolute; inset:0; background: radial-gradient(ellipse at 50% -20%, rgba(14,165,233,.25), transparent 70%); pointer-events:none; }
    .cta-section .section-title { color: #fff; }
    .cta-section .section-sub { color: rgba(255,255,255,.65); margin: 0 auto 36px; }
    .cta-actions { display: flex; align-items: center; justify-content: center; gap: 14px; flex-wrap: wrap; }
    .cta-note { margin-top: 24px; font-size: 13px; color: rgba(255,255,255,.45); font-weight: 500; }
    .btn-white { background: #fff; color: var(--navy); }
    .btn-white:hover { background: var(--gray-100); transform: translateY(-1px); }
    .btn-ghost { background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.2); }
    .btn-ghost:hover { background: rgba(255,255,255,.15); }

    /* ── Footer ── */
    .footer { background: #060D1A; color: rgba(255,255,255,.5); padding: 32px 0; text-align: center; font-size: 14px; }
    .footer a { color: rgba(255,255,255,.7); margin: 0 12px; font-weight: 600; }
    .footer a:hover { color: var(--accent); }

    /* ── Responsive ── */
    @media (max-width: 900px) {
      .hero-inner { grid-template-columns: 1fr; }
      .hero-visual { display: none; }
      .features-grid { grid-template-columns: 1fr 1fr; }
      .about-inner { grid-template-columns: 1fr; }
      .about-visual { display: none; }
      .testimonials-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
      .navbar-links { display: none; }
      .features-grid { grid-template-columns: 1fr; }
      .pricing-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

{{-- ── NAVBAR ── --}}
<nav class="navbar">
  <div class="container navbar-inner">
    <div class="navbar-logo">
      <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--accent)"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/></svg>
      {{ config('app.name', 'بطاقتي') }}
    </div>
    <div class="navbar-links">
      <a href="#features">المميزات</a>
      <a href="#pricing">الأسعار</a>
      <a href="#about">من نحن</a>
      <a href="#faq">الأسئلة</a>
    </div>
    <div class="navbar-actions">
      <button onclick="document.getElementById('login-modal').style.display='flex'" class="btn btn-outline" style="padding:9px 18px;font-size:14px">تسجيل الدخول</button>
    </div>
  </div>
</nav>

{{-- ── Login Modal — asks for subdomain then redirects to tenant login ── --}}
<div id="login-modal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);z-index:9999;align-items:center;justify-content:center;padding:20px">
  <div style="background:#fff;border-radius:16px;padding:36px 32px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2);position:relative">
    <button onclick="document.getElementById('login-modal').style.display='none'" style="position:absolute;top:14px;left:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#94a3b8;line-height:1">×</button>
    <div style="text-align:center;margin-bottom:24px">
      <div style="width:52px;height:52px;background:#eff6ff;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
        <svg width="24" height="24" fill="none" stroke="#0ea5e9" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
      </div>
      <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:6px">تسجيل الدخول</h2>
      <p style="font-size:14px;color:#64748b">أدخل رابط شبكتك للمتابعة</p>
    </div>
    <div style="display:flex;align-items:center;border:2px solid #e2e8f0;border-radius:10px;overflow:hidden;transition:.2s" id="subdomain-wrap">
      <input id="subdomain-input" type="text" placeholder="اسم-شبكتك"
        style="flex:1;border:none;outline:none;padding:12px 14px;font-family:inherit;font-size:15px;direction:ltr;text-align:left;background:transparent"
        oninput="this.value=this.value.replace(/[^a-z0-9\-]/g,'')"
        onkeydown="if(event.key==='Enter')goToLogin()">
      <span style="padding:12px 14px;color:#94a3b8;font-size:13px;white-space:nowrap;border-right:2px solid #e2e8f0;direction:ltr">.{{ config('app.domain','xnet-wifi.store') }}</span>
    </div>
    <p id="subdomain-error" style="color:#ef4444;font-size:13px;margin-top:8px;display:none">الرجاء إدخال رابط الشبكة</p>
    <button onclick="goToLogin()" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:16px;padding:14px">
      الدخول إلى لوحة التحكم
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </button>
    <p style="text-align:center;margin-top:16px;font-size:13px;color:#64748b">
      شبكة جديدة؟ <a href="{{ route('register') }}" style="color:#0ea5e9;font-weight:700">سجّل مجاناً</a>
    </p>
  </div>
</div>
<script>
function goToLogin() {
  var sub = document.getElementById('subdomain-input').value.trim();
  var err = document.getElementById('subdomain-error');
  if (!sub) { err.style.display='block'; return; }
  err.style.display='none';
  var domain = '{{ config('app.domain','xnet-wifi.store') }}';
  window.location.href = 'https://' + sub + '.' + domain + '/admin/login';
}
document.getElementById('login-modal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
</script>

{{-- ── DYNAMIC SECTIONS ── --}}
@foreach($sections as $section)
  @php $c = $section->content ?? []; @endphp

  {{-- HERO --}}
  @if($section->type === 'hero')
  <section class="hero" id="hero">
    <div class="container hero-inner">
      <div>
        @if(!empty($c['badge']))
        <div class="hero-badge">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          {{ $c['badge'] }}
        </div>
        @endif
        @if(!empty($c['title']))
        <h1 class="hero-title">{{ $c['title'] }}</h1>
        @endif
        @if(!empty($c['subtitle']))
        <p class="hero-sub">{{ $c['subtitle'] }}</p>
        @endif
        <div class="hero-actions">
          @if(!empty($c['cta_primary']['text']))
          <a href="{{ $c['cta_primary']['url'] ?? '#' }}" class="btn btn-primary">
            {{ $c['cta_primary']['text'] }}
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
          @endif
          @if(!empty($c['cta_secondary']['text']))
          <a href="{{ $c['cta_secondary']['url'] ?? '#' }}" class="btn btn-outline">{{ $c['cta_secondary']['text'] }}</a>
          @endif
        </div>
        @if(!empty($c['trust_items']))
        <div class="hero-trust">
          @foreach($c['trust_items'] as $item)
          <span class="hero-trust-item">{{ $item }}</span>
          @endforeach
        </div>
        @endif
      </div>
      <div class="hero-visual">
        <div class="dashboard-mock">
          <div class="dash-header">
            <div class="dash-dot" style="background:#EF4444"></div>
            <div class="dash-dot" style="background:#F59E0B"></div>
            <div class="dash-dot" style="background:#10B981"></div>
            <span style="color:rgba(255,255,255,.4);font-size:12px;margin-right:auto">بطاقتي Dashboard</span>
          </div>
          <div class="dash-body">
            <div class="dash-stat-row">
              <div class="dash-stat"><div class="dash-stat-v" style="color:var(--accent)">1,240</div><div class="dash-stat-l">بطاقة مباعة</div></div>
              <div class="dash-stat"><div class="dash-stat-v" style="color:#10B981">98%</div><div class="dash-stat-l">نشاط الشبكة</div></div>
              <div class="dash-stat"><div class="dash-stat-v" style="color:#F59E0B">4,890</div><div class="dash-stat-l">ريال أرباح</div></div>
            </div>
            <div class="dash-chart">
              <div style="font-size:11px;color:var(--gray-400);margin-bottom:10px;font-weight:700">المبيعات - آخر 7 أيام</div>
              <div class="dash-bars">
                @foreach([40,65,45,80,55,90,75] as $h)
                <div class="dash-bar" style="height:{{ $h }}%;opacity:{{ $loop->last ? '1' : '0.5' }}"></div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- FEATURES --}}
  @elseif($section->type === 'features')
  <section class="features-section" id="features">
    <div class="container">
      <div class="features-head">
        <div class="section-tag">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          المميزات
        </div>
        @if(!empty($c['title']))<h2 class="section-title">{{ $c['title'] }}</h2>@endif
        @if(!empty($c['subtitle']))<p class="section-sub">{{ $c['subtitle'] }}</p>@endif
      </div>
      @if(!empty($c['items']))
      <div class="features-grid">
        @foreach($c['items'] as $item)
        @php
          $icons = [
            'bolt'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
            'shield' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
            'chart'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
            'users'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
            'card'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
            'cog'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
          ];
          $iconPath = $icons[$item['icon'] ?? 'bolt'] ?? $icons['bolt'];
        @endphp
        <div class="feature-card">
          <div class="feature-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $iconPath !!}</svg>
          </div>
          <div class="feature-title">{{ $item['title'] ?? '' }}</div>
          <div class="feature-desc">{{ $item['desc'] ?? '' }}</div>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </section>

  {{-- PRICING --}}
  @elseif($section->type === 'pricing')
  <section class="pricing-section" id="pricing">
    <div class="container">
      <div class="pricing-head">
        <div class="section-tag">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          خطط الاشتراك
        </div>
        @if(!empty($c['title']))<h2 class="section-title">{{ $c['title'] }}</h2>@endif
        @if(!empty($c['subtitle']))<p class="section-sub">{{ $c['subtitle'] }}</p>@endif
      </div>
      <div class="pricing-grid">
        @foreach($plans as $plan)
        <div class="plan-card {{ $plan->is_popular ? 'popular' : '' }}">
          @if($plan->is_popular)
          <div class="popular-tag">⭐ الأكثر طلباً</div>
          @endif
          <div class="plan-name" style="color:{{ $plan->is_popular ? 'var(--accent)' : 'var(--navy)' }}">{{ $plan->name }}</div>
          <div class="plan-price">
            @if($plan->price == 0)
              <span style="font-size:28px">مجاناً</span>
            @else
              <sup>ر.س</sup>{{ number_format($plan->price, 0) }}<sub>/شهر</sub>
            @endif
          </div>
          @if($plan->max_cards > 0 || $plan->max_users > 0)
          <div class="plan-meta">
            @if($plan->max_cards > 0)حتى {{ number_format($plan->max_cards) }} بطاقة @else بطاقات غير محدودة @endif
            @if($plan->max_users > 0) · {{ number_format($plan->max_users) }} مستخدم @endif
          </div>
          @else
          <div class="plan-meta">بطاقات ومستخدمون غير محدودون</div>
          @endif
          @if(!empty($plan->features))
          <div class="plan-features">
            @foreach($plan->features as $feat)
            <div class="plan-feat">{{ $feat }}</div>
            @endforeach
          </div>
          @endif
          <a href="{{ route('register') }}" class="btn {{ $plan->is_popular ? 'btn-primary' : 'btn-outline' }}" style="width:100%;justify-content:center">
            ابدأ الآن
          </a>
        </div>
        @endforeach
      </div>
      @if(!empty($c['note']))
      <p class="pricing-note">{{ $c['note'] }}</p>
      @endif
    </div>
  </section>

  {{-- ABOUT --}}
  @elseif($section->type === 'about')
  <section class="about-section" id="about">
    <div class="container about-inner">
      <div>
        <div class="section-tag">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          من نحن
        </div>
        @if(!empty($c['title']))<h2 class="section-title">{{ $c['title'] }}</h2>@endif
        @if(!empty($c['subtitle']))<p class="section-sub">{{ $c['subtitle'] }}</p>@endif
        @if(!empty($c['stats']))
        <div class="stats-grid">
          @foreach($c['stats'] as $stat)
          <div class="stat-box">
            <div class="stat-val">{{ $stat['value'] }}</div>
            <div class="stat-lbl">{{ $stat['label'] }}</div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
      <div class="about-visual">
        <div class="network-diagram">
          <svg width="64" height="64" fill="none" stroke="white" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:.7;margin:0 auto 16px"><path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
          <div style="font-size:20px;font-weight:900;margin-bottom:6px">بطاقتي</div>
          <div style="font-size:13px;opacity:.6">منصة إدارة شبكات الإنترنت</div>
          <div style="margin-top:24px;display:flex;justify-content:center;gap:20px">
            <div style="text-align:center"><div style="font-size:24px;font-weight:900;color:var(--accent2)">v2</div><div style="font-size:11px;opacity:.5">الإصدار</div></div>
            <div style="text-align:center"><div style="font-size:24px;font-weight:900;color:#34D399">99.9%</div><div style="font-size:11px;opacity:.5">وقت التشغيل</div></div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- TESTIMONIALS --}}
  @elseif($section->type === 'testimonials')
  <section class="testimonials-section" id="testimonials">
    <div class="container">
      <div class="testimonials-head">
        <div class="section-tag">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          آراء العملاء
        </div>
        @if(!empty($c['title']))<h2 class="section-title">{{ $c['title'] }}</h2>@endif
        @if(!empty($c['subtitle']))<p class="section-sub" style="margin:0 auto">{{ $c['subtitle'] }}</p>@endif
      </div>
      @if(!empty($c['items']))
      <div class="testimonials-grid">
        @foreach($c['items'] as $item)
        <div class="testimonial-card">
          <div class="stars">{{ str_repeat('★', intval($item['stars'] ?? 5)) }}</div>
          <p class="testimonial-text">"{{ $item['text'] ?? '' }}"</p>
          <div class="testimonial-author">
            <div class="author-avatar">{{ mb_substr($item['name'] ?? 'م', 0, 1) }}</div>
            <div>
              <div class="author-name">{{ $item['name'] ?? '' }}</div>
              <div class="author-role">{{ $item['role'] ?? '' }}</div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </section>

  {{-- FAQ --}}
  @elseif($section->type === 'faq')
  <section class="faq-section" id="faq">
    <div class="container">
      <div class="faq-head">
        <div class="section-tag">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          الأسئلة الشائعة
        </div>
        @if(!empty($c['title']))<h2 class="section-title">{{ $c['title'] }}</h2>@endif
        @if(!empty($c['subtitle']))<p class="section-sub" style="margin:0 auto">{{ $c['subtitle'] }}</p>@endif
      </div>
      @if(!empty($c['items']))
      <div class="faq-list">
        @foreach($c['items'] as $i => $item)
        <div class="faq-item {{ $i === 0 ? 'open' : '' }}">
          <div class="faq-q" onclick="this.closest('.faq-item').classList.toggle('open')">
            {{ $item['q'] ?? '' }}
            <svg class="faq-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
          </div>
          <div class="faq-a">{{ $item['a'] ?? '' }}</div>
        </div>
        @endforeach
      </div>
      @endif
    </div>
  </section>

  {{-- CTA --}}
  @elseif($section->type === 'cta')
  <section class="cta-section" id="cta">
    <div class="container">
      @if(!empty($c['title']))<h2 class="section-title" style="margin-bottom:14px">{{ $c['title'] }}</h2>@endif
      @if(!empty($c['subtitle']))<p class="section-sub">{{ $c['subtitle'] }}</p>@endif
      <div class="cta-actions">
        @if(!empty($c['cta_primary']['text']))
        <a href="{{ $c['cta_primary']['url'] ?? '#' }}" class="btn btn-white">
          {{ $c['cta_primary']['text'] }}
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        @endif
        @if(!empty($c['cta_secondary']['text']))
        <a href="{{ $c['cta_secondary']['url'] ?? '#' }}" class="btn btn-ghost">{{ $c['cta_secondary']['text'] }}</a>
        @endif
      </div>
      @if(!empty($c['note']))<p class="cta-note">{{ $c['note'] }}</p>@endif
    </div>
  </section>

  @endif
@endforeach

{{-- ── FOOTER ── --}}
<footer class="footer">
  <div class="container">
    <p style="margin-bottom:10px">
      <a href="#features">المميزات</a>
      <a href="#pricing">الأسعار</a>
      <a href="#about">من نحن</a>
      <a href="#faq">الأسئلة</a>
    </p>
    <p style="margin-top:8px">© {{ date('Y') }} {{ config('app.name', 'بطاقتي') }} — جميع الحقوق محفوظة</p>
  </div>
</footer>

</body>
</html>
