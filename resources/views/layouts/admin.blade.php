<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'لوحة التحكم') - بطاقتي</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  @stack('styles')
</head>
<body>
<div class="app-container">
  <!-- Sidebar -->
  @include('admin.partials.sidebar')
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="main-wrapper">
    <!-- Navbar -->
    @include('admin.partials.navbar')

    <!-- Admin Modals -->
    @include('admin.partials.modals')

    <!-- Main Content -->
    <main class="main-content">
      {{-- Flash Messages (for toast) --}}
      @if(session('success'))
        <div data-flash-success="{{ session('success') }}" style="display:none"></div>
      @endif
      @if(session('error'))
        <div data-flash-error="{{ session('error') }}" style="display:none"></div>
      @endif

      {{-- Validation Errors --}}
      @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:14px 18px;border-radius:8px;margin-bottom:16px">
          <div style="font-weight:700;margin-bottom:8px"><i class="fas fa-exclamation-triangle"></i> يرجى تصحيح الأخطاء التالية:</div>
          <ul style="margin:0;padding-right:20px">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>

@yield('modals')
<div class="toast-container" id="toastContainer"></div>
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
// ===== Admin Notification System =====
(function() {
  let _notifLoaded = false;

  // Update badge count
  function fetchNotifCount() {
    fetch('/notifications/unread-count', {headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}})
      .then(r => r.json())
      .then(data => {
        const badge = document.getElementById('notifBadge');
        if (badge) {
          const count = data.count || 0;
          badge.textContent = count;
          badge.style.display = count > 0 ? 'flex' : 'none';
        }
      }).catch(() => {});
  }

  // Load and render notifications list
  window.loadNotificationsList = function() {
    const body = document.getElementById('notifBody');
    if (!body) return;
    body.innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8"><i class="fas fa-spinner fa-spin"></i> جارٍ التحميل...</div>';

    fetch('/notifications', {headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}})
      .then(r => r.json())
      .then(res => {
        const items = (res.notifications && res.notifications.data) ? res.notifications.data : [];
        if (items.length === 0) {
          body.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i><p>لا توجد إشعارات</p></div>';
          return;
        }
        const iconMap = {
          purchase: {icon:'fa-shopping-cart', color:'#10b981'},
          message:  {icon:'fa-comment',       color:'#3b82f6'},
          ticket:   {icon:'fa-ticket-alt',    color:'#f59e0b'},
          recharge: {icon:'fa-wallet',         color:'#8b5cf6'},
          reply:    {icon:'fa-reply',          color:'#06b6d4'},
        };
        body.innerHTML = items.map(n => {
          const ic = iconMap[n.type] || {icon:'fa-bell', color:'#64748b'};
          const unread = !n.is_read;
          const timeAgo = formatTimeAgo(n.created_at);
          return `<div class="notif-item${unread ? ' unread' : ''}" onclick="handleNotifClick('${n.url || '#'}')">
            <div class="notif-icon" style="background:${ic.color}20;color:${ic.color}"><i class="fas ${ic.icon}"></i></div>
            <div class="notif-content">
              <div class="notif-title">${escapeHtml(n.title)}</div>
              ${n.message ? `<div class="notif-msg">${escapeHtml(n.message)}</div>` : ''}
              <div class="notif-time">${timeAgo}</div>
            </div>
            ${unread ? '<div class="notif-dot"></div>' : ''}
          </div>`;
        }).join('');

        // Update badge to 0 after reading
        const badge = document.getElementById('notifBadge');
        if (badge) { badge.textContent = '0'; badge.style.display = 'none'; }
      })
      .catch(() => {
        body.innerHTML = '<div class="notif-empty"><i class="fas fa-exclamation-triangle"></i><p>تعذر تحميل الإشعارات</p></div>';
      });
  };

  function handleNotifClick(url) {
    if (url && url !== '#') window.location.href = url;
  }

  function formatTimeAgo(dateStr) {
    if (!dateStr) return '';
    const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
    if (diff < 60)   return 'الآن';
    if (diff < 3600) return Math.floor(diff/60) + ' دقيقة';
    if (diff < 86400) return Math.floor(diff/3600) + ' ساعة';
    return Math.floor(diff/86400) + ' يوم';
  }

  // Override toggleDropdown to auto-load notifications
  const _origToggle = window.toggleDropdown;
  window.toggleDropdown = function(id) {
    _origToggle && _origToggle(id);
    if (id === 'notifDropdown') {
      const dropdown = document.getElementById('notifDropdown');
      if (dropdown && dropdown.classList.contains('show')) {
        loadNotificationsList();
      }
    }
  };

  // Mark all read
  window.markAllRead = function() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch('/notifications/mark-read', {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json'}
    }).then(() => {
      const badge = document.getElementById('notifBadge');
      if (badge) { badge.textContent = '0'; badge.style.display = 'none'; }
      const body = document.getElementById('notifBody');
      if (body) body.querySelectorAll('.notif-item.unread').forEach(el => {
        el.classList.remove('unread');
        const dot = el.querySelector('.notif-dot');
        if (dot) dot.remove();
      });
    });
  };

  window.clearNotifications = function(e) {
    if (e) e.preventDefault();
    const body = document.getElementById('notifBody');
    if (body) body.innerHTML = '<div class="notif-empty"><i class="fas fa-bell-slash"></i><p>لا توجد إشعارات</p></div>';
    window.markAllRead();
  };

  // Initial count + poll every 30s (fallback)
  fetchNotifCount();
  setInterval(fetchNotifCount, 30000);

  // ===== Real-Time via Laravel Reverb (WebSocket) =====
  (function initReverbAdmin() {
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
          const badge = document.getElementById('notifBadge');
          if (badge) {
            const count = event.unread_count || 0;
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
          }

          // 2. Toast notification
          showToast((event.title || '') + (event.message ? ': ' + event.message : ''), 'success');

          // 3. If dropdown is already open — refresh list
          const dd = document.getElementById('notifDropdown');
          if (dd && dd.classList.contains('show')) {
            loadNotificationsList();
          }
        });

      console.log('✅ Reverb connected (admin)');
    } catch (e) {
      console.warn('Reverb admin init:', e.message);
    }
  })();
})();
</script>
<style>
.notif-item {
  display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px;
  cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #f1f5f9;
}
.notif-item:hover { background: #f8fafc; }
.notif-item.unread { background: #f0fdf4; }
.notif-icon {
  width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center;
  justify-content: center; font-size: 14px; flex-shrink: 0;
}
.notif-content { flex: 1; min-width: 0; }
.notif-title { font-size: 13px; font-weight: 600; color: #1e293b; margin-bottom: 2px; }
.notif-msg { font-size: 12px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.notif-time { font-size: 11px; color: #94a3b8; margin-top: 3px; }
.notif-dot { width: 8px; height: 8px; border-radius: 50%; background: #10b981; flex-shrink: 0; margin-top: 4px; }
.notif-empty { text-align: center; padding: 30px 16px; color: #94a3b8; }
.notif-empty i { font-size: 28px; margin-bottom: 8px; display: block; opacity: 0.5; }
.notif-empty p { font-size: 13px; }
#notifBadge { display: none; }
</style>
</body>
</html>
