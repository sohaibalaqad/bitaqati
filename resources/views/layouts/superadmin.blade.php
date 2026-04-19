<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'لوحة التحكم العليا') - بطاقتي</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    :root { --accent: #7c3aed; --accent-light: #ede9fe; }
    .sa-sidebar { background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%); }
    .sa-sidebar .sidebar-logo { color: #c4b5fd; }
    .sa-sidebar .nav-link { color: #a5b4fc; }
    .sa-sidebar .nav-link:hover, .sa-sidebar .nav-link.active { background: rgba(167,139,250,.15); color: #fff; }
    .sa-badge { background:#7c3aed; color:#fff; font-size:11px; padding:2px 8px; border-radius:20px; margin-right:6px; }
    .stat-card-sa { background:linear-gradient(135deg,#7c3aed,#4f46e5); color:#fff; border-radius:12px; padding:20px; }
  </style>
  @stack('styles')
</head>
<body>
<div class="app-container">

  {{-- Sidebar --}}
  <aside class="sidebar sa-sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <i class="fas fa-crown" style="color:#fbbf24;margin-left:8px"></i>
        <span style="font-weight:800">Super Admin</span>
      </div>
      <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    </div>

    <div style="padding:12px 16px;background:rgba(255,255,255,.05);margin:0 12px 16px;border-radius:8px;font-size:13px;color:#c4b5fd">
      <i class="fas fa-user-shield" style="margin-left:6px"></i>
      {{ auth()->user()->name }}
    </div>

    <nav class="sidebar-nav">
      <a href="{{ route('superadmin.dashboard') }}" class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> لوحة التحكم
      </a>
      <div class="nav-section">الشبكات</div>
      <a href="{{ route('superadmin.tenants') }}" class="nav-link {{ request()->routeIs('superadmin.tenants*') ? 'active' : '' }}">
        <i class="fas fa-network-wired"></i> الشبكات
      </a>
      <a href="{{ route('superadmin.plans') }}" class="nav-link {{ request()->routeIs('superadmin.plans*') ? 'active' : '' }}">
        <i class="fas fa-tags"></i> باقات الاشتراك
      </a>
      <div class="nav-section">المنصة</div>
      <a href="{{ route('superadmin.homepage.index') }}" class="nav-link {{ request()->routeIs('superadmin.homepage*') ? 'active' : '' }}">
        <i class="fas fa-globe"></i> الصفحة الرئيسية
      </a>
    </nav>

    <div style="position:absolute;bottom:20px;right:0;left:0;padding:0 16px">
      <form method="POST" action="{{ route('superadmin.logout') }}">
        @csrf
        <button type="submit" style="width:100%;background:rgba(239,68,68,.2);color:#fca5a5;border:none;padding:10px;border-radius:8px;cursor:pointer;font-family:inherit;font-size:14px">
          <i class="fas fa-sign-out-alt" style="margin-left:8px"></i> تسجيل الخروج
        </button>
      </form>
    </div>
  </aside>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="main-wrapper">
    {{-- Top Bar --}}
    <header class="navbar">
      <div class="navbar-right">
        <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
        <span style="font-weight:700;color:var(--text-dark)">@yield('page-title', 'لوحة التحكم')</span>
      </div>
      <div class="navbar-left" style="display:flex;align-items:center;gap:10px">
        <span class="sa-badge"><i class="fas fa-crown" style="margin-left:4px"></i> Super Admin</span>
      </div>
    </header>

    <main class="main-content">
      @if(session('success'))
        <div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:14px 18px;border-radius:8px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
          <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
      @endif
      @if(session('error') || $errors->has('error'))
        <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:14px 18px;border-radius:8px;margin-bottom:16px">
          <i class="fas fa-exclamation-circle"></i>
          {{ session('error') ?? $errors->first('error') }}
        </div>
      @endif
      @if($errors->any() && !$errors->has('error'))
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:14px 18px;border-radius:8px;margin-bottom:16px">
          <div style="font-weight:700;margin-bottom:8px"><i class="fas fa-exclamation-triangle"></i> يرجى تصحيح الأخطاء:</div>
          <ul style="margin:0;padding-right:20px">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
