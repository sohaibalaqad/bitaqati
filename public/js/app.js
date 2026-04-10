// ========== Sidebar Toggle (Mobile) ==========
const sidebar = document.getElementById('sidebar');
const menuToggle = document.getElementById('menuToggle');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (menuToggle) {
  menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('active');
  });
}

if (sidebarOverlay) {
  sidebarOverlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('active');
  });
}

// ========== Toast Notifications ==========
function showToast(message, type = 'success') {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
    <span>${message}</span>
  `;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ========== Modal ==========
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) { modal.classList.add('active'); document.body.style.overflow = 'hidden'; }
}
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) { modal.classList.remove('active'); document.body.style.overflow = ''; }
}
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) { overlay.classList.remove('active'); document.body.style.overflow = ''; }
  });
});

// ========== Confirm Delete ==========
function confirmDelete(formId, itemName) {
  if (confirm(`هل أنت متأكد من حذف "${itemName}"؟`)) {
    document.getElementById(formId).submit();
  }
}

// ========== Format Currency ==========
function formatCurrency(amount) {
  return parseFloat(amount || 0).toFixed(2) + ' ₪';
}

// ========== Escape HTML (XSS prevention) ==========
function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

// ========== Dropdown Toggle ==========
function toggleDropdown(id) {
  const dropdown = document.getElementById(id);
  if (!dropdown) return;
  document.querySelectorAll('.dropdown-menu.show').forEach(d => {
    if (d.id !== id) d.classList.remove('show');
  });
  dropdown.classList.toggle('show');
}

document.addEventListener('click', (e) => {
  if (!e.target.closest('.dropdown-wrapper')) {
    document.querySelectorAll('.dropdown-menu.show').forEach(d => d.classList.remove('show'));
  }
  if (!e.target.closest('.navbar-search')) {
    const sr = document.getElementById('searchResults');
    if (sr) sr.classList.remove('show');
  }
});

// ========== Global Search ==========
function handleGlobalSearch(query) {
  const resultsEl = document.getElementById('searchResults');
  if (!resultsEl) return;

  if (!query || query.length < 2) { resultsEl.classList.remove('show'); return; }

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  fetch('/admin/search?q=' + encodeURIComponent(query), {
    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
  })
  .then(r => r.json())
  .then(data => {
    let html = '';

    if (data.users && data.users.length > 0) {
      html += '<div class="search-section-title">المستخدمين</div>';
      data.users.forEach(u => {
        const safeName  = escapeHtml(u.name);
        const safePhone = escapeHtml(u.phone);
        const safeId    = parseInt(u.id, 10);
        html += `
          <div class="search-user-item">
            <div class="search-user-info" onclick="window.location.href='/admin/users/${safeId}'">
              <div class="search-user-avatar">${safeName.charAt(0)}</div>
              <div>
                <div class="search-user-name">${safeName}</div>
                <div class="search-user-phone">${safePhone} • رصيد: ${formatCurrency(u.balance)}</div>
              </div>
            </div>
          </div>
        `;
      });
    }

    // Search pages
    const pages = [
      { name: 'الرئيسية', icon: 'fa-home', url: '/admin' },
      { name: 'البطاقات النشطة', icon: 'fa-bolt', url: '/admin/active-cards' },
      { name: 'تقارير المبيعات', icon: 'fa-chart-line', url: '/admin/sales-reports' },
      { name: 'طلبات الشحن', icon: 'fa-truck', url: '/admin/shipping' },
      { name: 'الدعم الفني', icon: 'fa-headset', url: '/admin/support' },
      { name: 'قائمة المستخدمين', icon: 'fa-users', url: '/admin/users' },
      { name: 'أرصدة المستخدمين', icon: 'fa-shekel-sign', url: '/admin/balances' },
      { name: 'باقات الانترنت', icon: 'fa-wifi', url: '/admin/packages' },
      { name: 'مخزن البطاقات', icon: 'fa-warehouse', url: '/admin/cards-store' },
      { name: 'الفواتير', icon: 'fa-file-invoice-dollar', url: '/admin/invoices' },
    ];
    const q = query.toLowerCase();
    const matchedPages = pages.filter(p => p.name.includes(q));
    if (matchedPages.length > 0) {
      html += '<div class="search-section-title">الصفحات</div>';
      matchedPages.forEach(p => {
        html += `<div class="search-result-item" onclick="window.location.href='${p.url}'"><i class="fas ${p.icon}"></i><span>${p.name}</span><span class="result-label">صفحة</span></div>`;
      });
    }

    if (!html) {
      html = '<div class="search-no-results"><i class="fas fa-search" style="margin-left:6px"></i>لا توجد نتائج لـ "' + query + '"</div>';
    }

    resultsEl.innerHTML = html;
    resultsEl.classList.add('show');
  })
  .catch(() => {
    // Fallback: search pages only
    const q = query.toLowerCase();
    const pages = [
      { name: 'الرئيسية', icon: 'fa-home', url: '/admin' },
      { name: 'البطاقات النشطة', icon: 'fa-bolt', url: '/admin/active-cards' },
      { name: 'تقارير المبيعات', icon: 'fa-chart-line', url: '/admin/sales-reports' },
      { name: 'طلبات الشحن', icon: 'fa-truck', url: '/admin/shipping' },
      { name: 'الدعم الفني', icon: 'fa-headset', url: '/admin/support' },
      { name: 'قائمة المستخدمين', icon: 'fa-users', url: '/admin/users' },
      { name: 'أرصدة المستخدمين', icon: 'fa-shekel-sign', url: '/admin/balances' },
      { name: 'باقات الانترنت', icon: 'fa-wifi', url: '/admin/packages' },
      { name: 'مخزن البطاقات', icon: 'fa-warehouse', url: '/admin/cards-store' },
      { name: 'الفواتير', icon: 'fa-file-invoice-dollar', url: '/admin/invoices' },
    ];
    const matchedPages = pages.filter(p => p.name.includes(q));
    let html = '';
    if (matchedPages.length > 0) {
      html += '<div class="search-section-title">الصفحات</div>';
      matchedPages.forEach(p => {
        html += `<div class="search-result-item" onclick="window.location.href='${p.url}'"><i class="fas ${p.icon}"></i><span>${p.name}</span><span class="result-label">صفحة</span></div>`;
      });
    }
    if (!html) html = '<div class="search-no-results">لا توجد نتائج</div>';
    resultsEl.innerHTML = html;
    resultsEl.classList.add('show');
  });
}

// ========== Profile Functions ==========
function openProfileSettings() {
  toggleDropdown('profileDropdown');
  openModal('profileSettingsModal');
}
function openChangePassword() {
  toggleDropdown('profileDropdown');
  openModal('changePasswordModal');
}
function toggleDarkMode() {
  document.body.classList.toggle('dark-mode');
  localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
  toggleDropdown('profileDropdown');
}
if (localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark-mode');

// ========== Auto-show toast from flash messages ==========
document.addEventListener('DOMContentLoaded', () => {
  const successEl = document.querySelector('[data-flash-success]');
  if (successEl) showToast(successEl.dataset.flashSuccess, 'success');

  const errorEl = document.querySelector('[data-flash-error]');
  if (errorEl) showToast(errorEl.dataset.flashError, 'error');
});
