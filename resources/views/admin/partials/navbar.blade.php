<header class="navbar">
  <div class="navbar-right">
    <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
    <h1 class="page-title">@yield('page-title', 'لوحة التحكم')</h1>
  </div>
  <div class="navbar-left">
    <div class="navbar-search">
      <i class="fas fa-search"></i>
      <input type="text" id="globalSearch" placeholder="بحث عن مستخدم، بطاقة، فاتورة..." oninput="handleGlobalSearch(this.value)" onfocus="handleGlobalSearch(this.value)" autocomplete="off">
      <div class="search-results" id="searchResults"></div>
    </div>
    <div class="dropdown-wrapper">
      <button class="nav-icon-btn" title="الإشعارات" onclick="toggleDropdown('notifDropdown')">
        <i class="fas fa-bell"></i><span class="badge" id="notifBadge">0</span>
      </button>
      <div class="dropdown-menu" id="notifDropdown">
        <div class="dropdown-header"><h4>الإشعارات</h4><button class="mark-read" onclick="markAllRead()">تعيين الكل كمقروء</button></div>
        <div class="dropdown-body" id="notifBody"></div>
        <div class="dropdown-footer"><a href="#" onclick="clearNotifications()">مسح جميع الإشعارات</a></div>
      </div>
    </div>
    <div class="dropdown-wrapper">
      <div class="user-menu" onclick="toggleDropdown('profileDropdown')">
        <div class="user-avatar">{{ mb_substr(auth()->user()->name ?? 'أ', 0, 1) }}</div>
        <div class="user-info"><div class="name">{{ auth()->user()->name ?? 'المدير' }}</div><div class="status">متصل</div></div>
        <i class="fas fa-chevron-down" style="font-size:11px;color:var(--text-muted);margin-right:4px"></i>
      </div>
      <div class="dropdown-menu dropdown-sm profile-dropdown" id="profileDropdown">
        <div class="profile-header"><div class="avatar-lg">{{ mb_substr(auth()->user()->name ?? 'أ', 0, 1) }}</div><div class="profile-name">{{ auth()->user()->name ?? 'المدير' }}</div><div class="profile-role">مدير النظام</div></div>
        <button class="profile-menu-item" onclick="openProfileSettings()"><i class="fas fa-user-cog"></i> إعدادات الحساب</button>
        <button class="profile-menu-item" onclick="openChangePassword()"><i class="fas fa-key"></i> تغيير كلمة المرور</button>
        <button class="profile-menu-item" onclick="toggleDarkMode()"><i class="fas fa-moon"></i> الوضع الليلي</button>
        <div class="profile-divider"></div>
        <form method="POST" action="{{ route('admin.logout') }}" style="margin:0">
          @csrf
          <button type="submit" class="profile-menu-item danger" style="width:100%;text-align:right"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</button>
        </form>
      </div>
    </div>
  </div>
</header>
