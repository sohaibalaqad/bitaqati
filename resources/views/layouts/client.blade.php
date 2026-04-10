<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'لوحة العميل') - بطاقتي</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    :root {
      --accent: #10b981;
      --accent-dark: #059669;
      --bg-main: #f0f2f5;
      --bg-card: #ffffff;
      --text-main: #1e293b;
      --text-secondary: #475569;
      --text-muted: #94a3b8;
      --border: #e2e8f0;
      --radius: 12px;
      --radius-lg: 16px;
      --shadow: 0 1px 3px rgba(0,0,0,0.06);
      --shadow-lg: 0 8px 24px rgba(0,0,0,0.1);
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Tajawal', sans-serif;
      background: var(--bg-main);
      color: var(--text-main);
      min-height: 100vh;
      padding-bottom: 80px;
    }

    /* ===== Top Header ===== */
    .top-header {
      background: var(--bg-card);
      padding: 14px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .top-header .logo { font-size: 22px; font-weight: 800; color: var(--text-main); text-decoration: none; }
    .top-header .logo span { color: var(--accent); }
    .top-header .user-btn {
      display: flex; align-items: center; gap: 10px;
      background: none; border: none; cursor: pointer; font-family: 'Tajawal', sans-serif;
    }
    .top-header .user-avatar {
      width: 38px; height: 38px; border-radius: 50%;
      background: linear-gradient(135deg, var(--accent), var(--accent-dark));
      color: white; display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 16px;
    }
    .top-header .user-name { font-size: 14px; font-weight: 600; color: var(--text-main); }
    .top-header .logout-btn {
      background: none; border: none; color: var(--text-muted); font-size: 18px; cursor: pointer; padding: 8px;
    }
    .top-header .logout-btn:hover { color: #ef4444; }

    /* ===== Page Content ===== */
    .page-content { padding: 16px; max-width: 800px; margin: 0 auto; }

    /* ===== Welcome Card ===== */
    .welcome-card {
      background: linear-gradient(135deg, #10b981, #047857);
      color: white; border-radius: var(--radius-lg); padding: 24px;
      margin-bottom: 20px; position: relative; overflow: hidden;
    }
    .welcome-card::after {
      content: ''; position: absolute; top: -30px; left: -30px;
      width: 120px; height: 120px; border-radius: 50%;
      background: rgba(255,255,255,0.1);
    }
    .welcome-card h2 { font-size: 20px; margin-bottom: 4px; }
    .welcome-card .sub { opacity: 0.85; font-size: 14px; margin-bottom: 16px; }
    .welcome-card .balance-box {
      background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);
      border-radius: var(--radius); padding: 16px; text-align: center;
    }
    .welcome-card .balance-box .label { font-size: 13px; opacity: 0.9; margin-bottom: 4px; }
    .welcome-card .balance-box .amount { font-size: 32px; font-weight: 800; }

    /* ===== Stats Row ===== */
    .stats-row {
      display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px;
    }
    .stat-mini {
      background: var(--bg-card); border-radius: var(--radius); padding: 16px 12px;
      text-align: center; box-shadow: var(--shadow);
    }
    .stat-mini i { font-size: 22px; margin-bottom: 6px; display: block; }
    .stat-mini i.blue { color: #3b82f6; }
    .stat-mini i.green { color: #10b981; }
    .stat-mini i.orange { color: #f59e0b; }
    .stat-mini .stat-num { font-size: 22px; font-weight: 800; color: var(--text-main); }
    .stat-mini .stat-label { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

    /* ===== Section Title ===== */
    .section-title {
      font-size: 18px; font-weight: 700; margin-bottom: 16px;
      display: flex; align-items: center; gap: 8px;
    }
    .section-title i { color: var(--accent); }

    /* ===== Packages Grid ===== */
    .packages-grid {
      display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 16px; margin-bottom: 24px;
    }
    .pkg-card {
      background: var(--bg-card); border-radius: var(--radius-lg);
      box-shadow: var(--shadow); padding: 24px 20px; text-align: center;
      transition: transform 0.2s, box-shadow 0.2s; border: 2px solid transparent;
    }
    .pkg-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); border-color: var(--accent); }
    .pkg-icon {
      width: 52px; height: 52px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 14px; font-size: 22px; color: white;
    }
    .pkg-icon.c1 { background: linear-gradient(135deg, #10b981, #059669); }
    .pkg-icon.c2 { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .pkg-icon.c3 { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .pkg-icon.c4 { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .pkg-card h3 { font-size: 16px; margin-bottom: 6px; }
    .pkg-card .pkg-info { font-size: 13px; color: var(--text-muted); margin-bottom: 10px; }
    .pkg-card .pkg-price { font-size: 24px; font-weight: 800; color: var(--accent); margin-bottom: 4px; }
    .pkg-card .pkg-stock { font-size: 12px; color: var(--text-muted); margin-bottom: 14px; }
    .btn-buy {
      width: 100%; padding: 11px; background: var(--accent); color: white;
      border: none; border-radius: var(--radius); font-size: 15px; font-weight: 700;
      font-family: 'Tajawal', sans-serif; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 6px;
      transition: background 0.2s;
    }
    .btn-buy:hover { background: var(--accent-dark); }
    .btn-buy:disabled { background: #d1d5db; cursor: not-allowed; }

    /* ===== My Card Item ===== */
    .card-item {
      background: var(--bg-card); border-radius: var(--radius); padding: 16px;
      box-shadow: var(--shadow); margin-bottom: 12px;
    }
    .card-item-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    .card-item-header .pkg { font-weight: 700; font-size: 15px; display: flex; align-items: center; gap: 6px; }
    .card-item-header .pkg i { color: var(--accent); }
    .card-item-header .date { font-size: 12px; color: var(--text-muted); }
    .card-creds {
      background: var(--bg-main); border-radius: 8px; padding: 12px 14px;
      display: flex; gap: 16px; font-size: 14px; flex-wrap: wrap;
    }
    .card-creds code { background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 13px; direction: ltr; user-select: all; }

    /* ===== Form Card ===== */
    .form-card {
      background: var(--bg-card); border-radius: var(--radius-lg);
      box-shadow: var(--shadow); padding: 24px; margin-bottom: 20px;
    }
    .form-card label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; }
    .form-card input, .form-card textarea, .form-card select {
      width: 100%; padding: 12px 14px; border: 1.5px solid var(--border); border-radius: var(--radius);
      font-size: 15px; font-family: 'Tajawal', sans-serif; background: var(--bg-main);
      color: var(--text-main); transition: border-color 0.2s; margin-bottom: 16px;
    }
    .form-card input:focus, .form-card textarea:focus { outline: none; border-color: var(--accent); }
    .form-card .btn-submit {
      width: 100%; padding: 14px; background: var(--accent); color: white;
      border: none; border-radius: var(--radius); font-size: 16px; font-weight: 700;
      font-family: 'Tajawal', sans-serif; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .form-card .btn-submit:hover { background: var(--accent-dark); }

    /* ===== Recharge Item ===== */
    .recharge-item {
      display: flex; align-items: center; justify-content: space-between;
      padding: 12px 0; border-bottom: 1px solid var(--border); font-size: 14px;
      flex-wrap: wrap; gap: 6px;
    }
    .recharge-item:last-child { border-bottom: none; }

    /* ===== Transaction Item ===== */
    .trans-item {
      background: var(--bg-card); border-radius: var(--radius); padding: 14px 16px;
      box-shadow: var(--shadow); margin-bottom: 10px;
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
    }
    .trans-item .trans-type { font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 6px; }
    .trans-item .trans-type i { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; color: white; }
    .trans-item .trans-type i.deposit { background: #10b981; }
    .trans-item .trans-type i.withdraw { background: #ef4444; }
    .trans-item .trans-type i.purchase { background: #3b82f6; }
    .trans-item .trans-amount { font-weight: 800; font-size: 16px; }
    .trans-item .trans-date { font-size: 12px; color: var(--text-muted); width: 100%; }
    .trans-item .trans-note { font-size: 12px; color: var(--text-muted); }

    /* ===== Badge Status ===== */
    .badge-st {
      padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
    }
    .badge-st.pending { background: #fef3c7; color: #92400e; }
    .badge-st.approved { background: #d1fae5; color: #065f46; }
    .badge-st.rejected { background: #fecaca; color: #991b1b; }

    /* ===== Empty State ===== */
    .empty-box {
      text-align: center; padding: 40px 20px; color: var(--text-muted);
      background: var(--bg-card); border-radius: var(--radius-lg);
    }
    .empty-box i { font-size: 44px; margin-bottom: 12px; display: block; opacity: 0.4; }
    .empty-box h3 { font-size: 16px; font-weight: 600; }

    /* ===== Bottom Nav ===== */
    .bottom-nav {
      position: fixed; bottom: 0; left: 0; right: 0;
      background: var(--bg-card); box-shadow: 0 -2px 12px rgba(0,0,0,0.08);
      display: flex; justify-content: space-around; align-items: center;
      padding: 8px 0; padding-bottom: calc(8px + env(safe-area-inset-bottom, 0px));
      z-index: 200;
    }
    .bottom-nav .nav-item {
      display: flex; flex-direction: column; align-items: center; gap: 3px;
      text-decoration: none; color: var(--text-muted); font-size: 11px;
      font-weight: 600; padding: 6px 12px; border-radius: 12px;
      transition: color 0.2s, background 0.2s; cursor: pointer;
      background: none; border: none; font-family: 'Tajawal', sans-serif;
    }
    .bottom-nav .nav-item i { font-size: 20px; transition: transform 0.2s; }
    .bottom-nav .nav-item.active { color: var(--accent); }
    .bottom-nav .nav-item.active i { transform: scale(1.15); }
    .bottom-nav .nav-item:hover { color: var(--accent); }

    /* Center buy button */
    .bottom-nav .nav-buy {
      background: var(--accent); color: white !important;
      width: 52px; height: 52px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin-top: -22px; box-shadow: 0 4px 14px rgba(16,185,129,0.4);
      font-size: 22px; transition: transform 0.2s, background 0.2s;
      border: 3px solid var(--bg-card);
    }
    .bottom-nav .nav-buy:hover { background: var(--accent-dark); transform: scale(1.08); }
    .bottom-nav .nav-buy span { display: none; }

    /* ===== Section visibility ===== */
    .section { display: none; }
    .section.active { display: block; }

    /* ===== Modal ===== */
    .modal-overlay {
      display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5); z-index: 300;
      align-items: center; justify-content: center; padding: 20px;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
      background: var(--bg-card); border-radius: var(--radius-lg);
      width: 100%; max-width: 420px; padding: 24px; position: relative;
      max-height: 90vh; overflow-y: auto;
    }
    .modal-box .modal-title {
      font-size: 18px; font-weight: 700; margin-bottom: 16px;
      display: flex; align-items: center; gap: 8px;
    }
    .modal-close {
      position: absolute; top: 16px; left: 16px; background: none;
      border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);
      width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
      border-radius: 50%;
    }
    .modal-close:hover { background: var(--bg-main); color: var(--text-main); }

    /* ===== Toast ===== */
    .toast-box {
      position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
      background: #1e293b; color: white; padding: 14px 24px; border-radius: var(--radius);
      font-size: 14px; font-weight: 600; z-index: 400; box-shadow: var(--shadow-lg);
      display: none; align-items: center; gap: 8px; max-width: 90%;
    }
    .toast-box.show { display: flex; }
    .toast-box.error { background: #dc2626; }
    .toast-box i { font-size: 16px; }

    /* ===== Responsive ===== */
    @media (max-width: 600px) {
      .page-content { padding: 12px; }
      .welcome-card { padding: 20px; }
      .welcome-card h2 { font-size: 18px; }
      .welcome-card .balance-box .amount { font-size: 26px; }
      .stats-row { gap: 8px; }
      .stat-mini { padding: 12px 8px; }
      .stat-mini .stat-num { font-size: 18px; }
      .packages-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
      .pkg-card { padding: 18px 14px; }
      .pkg-card h3 { font-size: 14px; }
      .pkg-card .pkg-price { font-size: 20px; }
      .card-creds { flex-direction: column; gap: 8px; }
      .trans-item { padding: 12px; }
      .form-card { padding: 18px; }
    }
    @media (max-width: 380px) {
      .packages-grid { grid-template-columns: 1fr; }
      .bottom-nav .nav-item { font-size: 10px; padding: 6px 8px; }
      .bottom-nav .nav-item i { font-size: 18px; }
    }
    @media (min-width: 768px) {
      .packages-grid { grid-template-columns: repeat(3, 1fr); }
    }
  </style>
  @stack('styles')
</head>
<body>

<!-- Top Header -->
<div class="top-header">
  <a href="{{ route('client.dashboard') }}" class="logo" style="display:flex;align-items:center;gap:8px">
    <img src="{{ asset('images/logo.png') }}" style="height:32px;width:32px;border-radius:6px" alt="بطاقتي">
    <span>بطاقتي</span>
  </a>
  <div style="display:flex;align-items:center;gap:10px">
    <!-- Notification Bell -->
    <div style="position:relative" id="clientNotifWrapper">
      <button onclick="toggleClientNotif()"
        style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-size:20px;padding:6px;position:relative;display:flex;align-items:center">
        <i class="fas fa-bell"></i>
        <span id="clientNotifBadge"
          style="display:none;position:absolute;top:2px;right:2px;background:#ef4444;color:white;border-radius:50%;width:16px;height:16px;font-size:10px;font-weight:700;align-items:center;justify-content:center;font-family:'Tajawal',sans-serif">0</span>
      </button>
      <div id="clientNotifDropdown"
        style="display:none;position:absolute;top:calc(100% + 8px);left:0;width:300px;max-width:92vw;background:var(--bg-card);border-radius:var(--radius-lg);box-shadow:0 8px 30px rgba(0,0,0,0.15);z-index:500;overflow:hidden;border:1px solid var(--border)">
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
          <span style="font-weight:700;font-size:14px">الإشعارات</span>
          <button onclick="markClientNotifsRead()" style="background:none;border:none;color:var(--accent);font-size:12px;cursor:pointer;font-family:'Tajawal',sans-serif">تعيين كمقروء</button>
        </div>
        <div id="clientNotifBody" style="max-height:320px;overflow-y:auto">
          <div style="text-align:center;padding:24px;color:var(--text-muted)">
            <i class="fas fa-bell-slash" style="font-size:28px;opacity:0.4;display:block;margin-bottom:8px"></i>لا توجد إشعارات
          </div>
        </div>
      </div>
    </div>
    <div class="user-btn">
      <div class="user-avatar" id="userAvatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
      <span class="user-name" id="userName">{{ auth()->user()->name }}</span>
    </div>
    <form method="POST" action="{{ route('client.logout') }}" id="logoutForm" style="display:inline">
      @csrf
      <button type="submit" class="logout-btn" title="تسجيل الخروج"><i class="fas fa-sign-out-alt"></i></button>
    </form>
  </div>
</div>

<!-- Page Content -->
<div class="page-content">
  @if(session('success'))
    <div style="background:#d1fae5;color:#065f46;padding:12px 16px;border-radius:var(--radius);margin-bottom:12px;font-weight:600;font-size:14px">
      <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div style="background:#fef2f2;color:#dc2626;padding:12px 16px;border-radius:var(--radius);margin-bottom:12px;font-weight:600;font-size:14px">
      <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
  @endif
  @if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:var(--radius);margin-bottom:12px;font-size:14px">
      @foreach($errors->all() as $error)
        <div><i class="fas fa-times-circle"></i> {{ $error }}</div>
      @endforeach
    </div>
  @endif
  @yield('content')
</div>

<!-- Bottom Navigation -->
<nav class="bottom-nav">
  <button class="nav-item active" id="navHome" onclick="switchTab('Home', this)">
    <i class="fas fa-home"></i>
    <span>الرئيسية</span>
  </button>
  <button class="nav-item" id="navMyCards" onclick="switchTab('MyCards', this)">
    <i class="fas fa-credit-card"></i>
    <span>بطاقاتي</span>
  </button>

  <button class="nav-item" id="navRecharge" onclick="switchTab('Recharge', this)">
    <i class="fas fa-wallet"></i>
    <span>شحن</span>
  </button>
    <button class="nav-item nav-buy" id="navBuy" onclick="switchTab('Home', document.getElementById('navHome'))">
        <i class="fas fa-shopping-cart"></i>
        <span>شراء</span>
    </button>
  <a href="{{ route('client.tickets') }}" class="nav-item {{ request()->routeIs('client.tickets*') ? 'active' : '' }}">
    <i class="fas fa-headset"></i>
    <span>الدعم</span>
  </a>
  <a href="{{ route('client.chat') }}" class="nav-item {{ request()->routeIs('client.chat*') ? 'active' : '' }}">
    <i class="fas fa-comments"></i>
    <span>المحادثة</span>
  </a>
  <button class="nav-item" id="navSettings" onclick="switchTab('Settings', this)">
    <i class="fas fa-ellipsis-h"></i>
    <span>المزيد</span>
  </button>
</nav>

@stack('modals')

<!-- Toast -->
<div class="toast-box" id="toastBox"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<script src="{{ asset('js/pusher.js') }}"></script>
<script src="{{ asset('js/echo.iife.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
<script>
// ===== Global Currency Config =====
window.CURRENCY = @json(\App\Helpers\CurrencyHelper::jsConfig());
window.CURRENCY.format = function(n) {
  var amt = parseFloat(n || 0).toFixed(2);
  return this.position === 'before' ? this.symbol + amt : amt + ' ' + this.symbol;
};
</script>
<script>
// ===== Client Notification System =====
(function() {
  function fetchClientNotifCount() {
    fetch('/notifications/unread-count', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
      .then(r => r.json())
      .then(data => {
        const badge = document.getElementById('clientNotifBadge');
        if (!badge) return;
        const count = data.count || 0;
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
      }).catch(()=>{});
  }

  window.toggleClientNotif = function() {
    const dd = document.getElementById('clientNotifDropdown');
    if (!dd) return;
    const isOpen = dd.style.display !== 'none';
    dd.style.display = isOpen ? 'none' : 'block';
    if (!isOpen) loadClientNotifList();
  };

  window.loadClientNotifList = function() {
    const body = document.getElementById('clientNotifBody');
    if (!body) return;
    body.innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8"><i class="fas fa-spinner fa-spin"></i></div>';

    fetch('/notifications', {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
      .then(r => r.json())
      .then(res => {
        const items = (res.notifications && res.notifications.data) ? res.notifications.data : [];
        if (!items.length) {
          body.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8"><i class="fas fa-bell-slash" style="font-size:24px;opacity:0.4;display:block;margin-bottom:8px"></i>لا توجد إشعارات</div>';
          return;
        }
        const iconMap = {purchase:{i:'fa-shopping-cart',c:'#10b981'},message:{i:'fa-comment',c:'#3b82f6'},ticket:{i:'fa-ticket-alt',c:'#f59e0b'},reply:{i:'fa-reply',c:'#06b6d4'}};
        body.innerHTML = items.map(n => {
          const ic = iconMap[n.type] || {i:'fa-bell',c:'#64748b'};
          const diff = Math.floor((Date.now() - new Date(n.created_at)) / 1000);
          const ago = diff < 60 ? 'الآن' : diff < 3600 ? Math.floor(diff/60)+'د' : diff < 86400 ? Math.floor(diff/3600)+'س' : Math.floor(diff/86400)+'ي';
          return `<div onclick="${n.url && n.url!='#' ? "window.location.href='"+n.url+"'" : ''}"
            style="display:flex;gap:10px;padding:12px 14px;border-bottom:1px solid #f1f5f9;cursor:${n.url ? 'pointer' : 'default'};background:${!n.is_read?'#f0fdf4':'inherit'};transition:background 0.15s"
            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='${!n.is_read?'#f0fdf4':'inherit'}'">
            <div style="width:34px;height:34px;border-radius:50%;background:${ic.c}20;color:${ic.c};display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0">
              <i class="fas ${ic.i}"></i></div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:600;color:#1e293b">${n.title}</div>
              ${n.message ? `<div style="font-size:12px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${n.message}</div>` : ''}
              <div style="font-size:11px;color:#94a3b8;margin-top:2px">${ago}</div>
            </div>
            ${!n.is_read ? '<div style="width:7px;height:7px;border-radius:50%;background:#10b981;flex-shrink:0;margin-top:4px"></div>' : ''}
          </div>`;
        }).join('');
        // Reset badge
        const badge = document.getElementById('clientNotifBadge');
        if (badge) { badge.textContent='0'; badge.style.display='none'; }
      }).catch(()=>{
        body.innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8">تعذر التحميل</div>';
      });
  };

  window.markClientNotifsRead = function() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch('/notifications/mark-read', {method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json','Accept':'application/json'}})
      .then(() => {
        const badge = document.getElementById('clientNotifBadge');
        if (badge) { badge.textContent='0'; badge.style.display='none'; }
        loadClientNotifList();
      });
  };

  // Close dropdown when clicking outside
  document.addEventListener('click', e => {
    const wrapper = document.getElementById('clientNotifWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
      const dd = document.getElementById('clientNotifDropdown');
      if (dd) dd.style.display = 'none';
    }
  });

  // Poll every 30s (fallback when WebSocket not available)
  fetchClientNotifCount();
  setInterval(fetchClientNotifCount, 30000);

  // ===== Real-Time via Laravel Reverb (WebSocket) =====
  (function initReverbClient() {
    try {
      // iife bundle exports Echo as a module object: Echo.default is the actual class
      const EchoLib = (window.Echo && window.Echo.default) ? window.Echo.default
                    : (window.LaravelEcho || window.Echo);
      if (!EchoLib || typeof EchoLib !== 'function') {
        console.warn('Echo class not found');
        return;
      }

      const echo = new EchoLib({
        broadcaster: 'reverb',
        key:         '{{ env("REVERB_APP_KEY") }}',
        wsHost:      '{{ env("REVERB_HOST", "localhost") }}',
        wsPort:       {{ env("REVERB_PORT", 8080) }},
        wssPort:      {{ env("REVERB_PORT", 8080) }},
        forceTLS:    {{ env("REVERB_SCHEME", "http") === "https" ? "true" : "false" }},
        enabledTransports: ['ws', 'wss'],
      });

      echo.private('notifications.{{ auth()->id() }}')
        .listen('.new-notification', (event) => {
          // 1. Update badge immediately
          const badge = document.getElementById('clientNotifBadge');
          if (badge) {
            const count = event.unread_count || 0;
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
          }

          // 2. Toast — use the client toast() function
          if (typeof toast === 'function') {
            toast('🔔 ' + (event.title || 'إشعار جديد'));
          }

          // 3. Refresh dropdown if it's currently open
          const dd = document.getElementById('clientNotifDropdown');
          if (dd && dd.style.display !== 'none') {
            loadClientNotifList();
          }
        });

      console.log('✅ Reverb connected (client)');
    } catch (e) {
      console.warn('Reverb client init:', e.message);
    }
  })();
})();
</script>
</body>
</html>
