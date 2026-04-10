<aside class="sidebar" id="sidebar">
  <div class="sidebar-header"><a href="{{ route('admin.dashboard') }}" class="sidebar-logo" style="display:flex;align-items:center;gap:8px"><img src="{{ asset('images/logo.png') }}" alt="بطاقتي" style="height:40px;width:auto;border-radius:8px"> <span>بطاقتي</span></a></div>
  <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> الرئيسية</a>
    <div class="nav-section-title">مراقبة النظام</div>
    <a href="{{ route('admin.active-cards') }}" class="nav-link {{ request()->routeIs('admin.active-cards') ? 'active' : '' }}"><i class="fas fa-bolt"></i> البطاقات النشطة <span class="status-dot"></span></a>
    <a href="{{ route('admin.sales-reports') }}" class="nav-link {{ request()->routeIs('admin.sales-reports') ? 'active' : '' }}"><i class="fas fa-chart-line"></i> تقارير المبيعات</a>
    <a href="{{ route('admin.shipping') }}" class="nav-link {{ request()->routeIs('admin.shipping') ? 'active' : '' }}"><i class="fas fa-truck"></i> طلبات الشحن</a>
    <a href="{{ route('admin.support') }}" class="nav-link {{ request()->routeIs('admin.support') ? 'active' : '' }}"><i class="fas fa-headset"></i> الدعم الفني</a>
    <div class="nav-section-title">إدارة المستخدمين</div>
    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> قائمة المستخدمين</a>
    <a href="{{ route('admin.balances') }}" class="nav-link {{ request()->routeIs('admin.balances') ? 'active' : '' }}"><i class="fas fa-shekel-sign"></i> أرصدة المستخدمين</a>
    <div class="nav-section-title">المخزن والباقات</div>
    <a href="{{ route('admin.packages') }}" class="nav-link {{ request()->routeIs('admin.packages') ? 'active' : '' }}"><i class="fas fa-wifi"></i> باقات الانترنت</a>
    <a href="{{ route('admin.cards-store') }}" class="nav-link {{ request()->routeIs('admin.cards-store') ? 'active' : '' }}"><i class="fas fa-warehouse"></i> مخزن البطاقات</a>
    <div class="nav-section-title">المالية</div>
    <a href="{{ route('admin.invoices') }}" class="nav-link {{ request()->routeIs('admin.invoices') ? 'active' : '' }}"><i class="fas fa-file-invoice-dollar"></i> الفواتير</a>
    <div class="nav-section-title">الأدوات</div>
    <a href="{{ route('admin.chat') }}" class="nav-link {{ request()->routeIs('admin.chat*') ? 'active' : '' }}"><i class="fas fa-comments"></i> المحادثات</a>
    <a href="{{ route('admin.dealer') }}" class="nav-link {{ request()->routeIs('admin.dealer') ? 'active' : '' }}"><i class="fas fa-store"></i> الديلر</a>
    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><i class="fas fa-cog"></i> الإعدادات</a>
  </nav>
  <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,0.08);margin-top:auto">
    <div style="font-size:11px;color:rgba(255,255,255,0.35);text-align:center;letter-spacing:0.5px">
      {{ config('platform.name') }} &bull; v{{ config('platform.version') }}
    </div>
  </div>
</aside>
