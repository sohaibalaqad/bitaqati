@extends('layouts.client')

@section('title', 'لوحة العميل')

@section('content')

  {{-- ===== Section: Home ===== --}}
  <div class="section active" id="sectionHome">
    <div class="welcome-card">
      <h2>مرحباً، <span id="welcomeName">{{ auth()->user()->name }}</span></h2>
      <p class="sub">اختر باقتك المفضلة واشترِ بطاقتك الآن</p>
      <div class="balance-box">
        <div class="label">رصيدك الحالي</div>
        <div class="amount" id="myBalance">{{ format_currency(auth()->user()->balance) }}</div>
      </div>
    </div>

    <div class="stats-row">
      <div class="stat-mini">
        <i class="fas fa-credit-card blue"></i>
        <div class="stat-num" id="myCardsCount">{{ $myCardsCount ?? 0 }}</div>
        <div class="stat-label">بطاقاتي</div>
      </div>
      <div class="stat-mini">
        <i class="fas fa-exchange-alt green"></i>
        <div class="stat-num" id="myTransCount">{{ $myTransCount ?? 0 }}</div>
        <div class="stat-label">العمليات</div>
      </div>
      <div class="stat-mini">
        <i class="fas fa-wallet orange"></i>
        <div class="stat-num" id="myRechargeCount">{{ $myRechargeCount ?? 0 }}</div>
        <div class="stat-label">طلبات شحن</div>
      </div>
    </div>

    <h3 class="section-title"><i class="fas fa-wifi"></i> الباقات المتوفرة</h3>
    <div class="packages-grid" id="packagesGrid">
      @forelse ($packages ?? [] as $index => $pkg)
        @php $colorClasses = ['c1','c2','c3','c4']; $cc = $colorClasses[$index % 4]; @endphp
        <div class="pkg-card">
          <div class="pkg-icon {{ $cc }}"><i class="fas fa-wifi"></i></div>
          <h3>{{ $pkg->name }}</h3>
          <div class="pkg-info">{{ $pkg->speed }} &bull; {{ $pkg->duration }}</div>
          <div class="pkg-price">{{ format_currency($pkg->price) }}</div>
          <div class="pkg-stock">
            {{ $pkg->available_count > 0 ? $pkg->available_count . ' بطاقة متوفرة' : 'غير متوفر' }}
          </div>
          <button class="btn-buy"
            onclick="openBuyModal({{ $pkg->id }}, '{{ e($pkg->name) }}', {{ $pkg->price }}, {{ $pkg->available_count }})"
            {{ $pkg->available_count == 0 ? 'disabled' : '' }}>
            <i class="fas fa-shopping-cart"></i>
            {{ $pkg->available_count > 0 ? 'شراء' : 'نفذت' }}
          </button>
        </div>
      @empty
        <div class="empty-box" style="grid-column:1/-1">
          <i class="fas fa-wifi"></i>
          <h3>لا يوجد باقات متوفرة حالياً</h3>
        </div>
      @endforelse
    </div>
  </div>

  {{-- ===== Section: My Cards ===== --}}
  <div class="section" id="sectionMyCards">
    <h3 class="section-title"><i class="fas fa-credit-card"></i> بطاقاتي المشتراة</h3>
    <div id="myCardsList">
      @forelse ($myCards ?? [] as $card)
        <div class="card-item">
          <div class="card-item-header">
            <span class="pkg"><i class="fas fa-wifi"></i> {{ $card->package->name ?? '-' }}</span>
            <span class="date">{{ $card->sold_at ? \Carbon\Carbon::parse($card->sold_at)->format('Y-m-d') : '-' }}</span>
          </div>
          <div class="card-creds">
            <span><strong>User:</strong> <code id="uname-{{ $card->id }}">{{ $card->username }}</code></span>
            <span><strong>Pass:</strong> <code id="upass-{{ $card->id }}">{{ $card->password }}</code></span>
          </div>
          @if($card->first_used_at)
            <div style="font-size:12px;color:var(--text-muted);margin-top:6px">
              <i class="fas fa-clock"></i> أول استخدام: {{ \Carbon\Carbon::parse($card->first_used_at)->format('Y-m-d H:i') }}
            </div>
          @endif
          <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap">
            <button onclick="connectCard({{ $card->id }}, '{{ e($card->username) }}', '{{ e($card->password) }}')"
              style="flex:1;background:linear-gradient(135deg,#3b82f6,#2563eb);color:white;border:none;padding:9px 14px;border-radius:8px;font-family:'Tajawal',sans-serif;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px;min-width:90px">
              <i class="fas fa-wifi"></i> اتصال
            </button>
            <button onclick="copyCredentials('{{ e($card->username) }}','{{ e($card->password) }}')"
              title="نسخ بيانات الدخول"
              style="background:var(--bg-main);color:var(--text-secondary);border:1.5px solid var(--border);padding:9px 14px;border-radius:8px;font-family:'Tajawal',sans-serif;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px">
              <i class="fas fa-copy"></i> نسخ
            </button>
          </div>
        </div>
      @empty
        <div class="empty-box">
          <i class="fas fa-credit-card"></i>
          <h3>لم تشترِ أي بطاقة بعد</h3>
        </div>
      @endforelse
    </div>
  </div>

  {{-- ===== Section: Recharge ===== --}}
  <div class="section" id="sectionRecharge">
    <h3 class="section-title"><i class="fas fa-wallet"></i> طلب شحن رصيد</h3>
    <div class="form-card">
      <form id="rechargeForm" onsubmit="submitRecharge(event)">
        @csrf
        <label for="rechargeAmount">المبلغ المطلوب ({{ currency_symbol() }})</label>
        <input type="number" id="rechargeAmount" name="amount" required min="1" step="0.01" placeholder="أدخل المبلغ">
        <label for="rechargeNote">ملاحظات (اختياري)</label>
        <textarea id="rechargeNote" name="note" rows="3" placeholder="أي ملاحظات للإدارة..."></textarea>
        <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> إرسال الطلب</button>
      </form>
    </div>
    <h3 class="section-title" style="font-size:16px"><i class="fas fa-history"></i> طلباتي السابقة</h3>
    <div class="form-card" style="padding:16px">
      <div id="myRechargeList">
        @forelse ($myRecharges ?? [] as $req)
          @php
            $statusLabels = ['pending' => 'قيد المراجعة', 'approved' => 'مقبول', 'rejected' => 'مرفوض'];
          @endphp
          <div class="recharge-item">
            <div>
              <strong>{{ format_currency($req->amount) }}</strong>
              <span style="color:var(--text-muted);margin-right:8px;font-size:13px">{{ $req->date ?? $req->created_at?->format('Y-m-d') }}</span>
            </div>
            <span class="badge-st {{ $req->status }}">{{ $statusLabels[$req->status] ?? $req->status }}</span>
          </div>
        @empty
          <p style="text-align:center;color:var(--text-muted);padding:12px">لا يوجد طلبات سابقة</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ===== Section: Transactions ===== --}}
  <div class="section" id="sectionTransactions">
    <h3 class="section-title"><i class="fas fa-exchange-alt"></i> سجل العمليات</h3>
    <div id="transactionsList">
      @forelse ($transactions ?? [] as $trans)
        @php
          $typeMap = [
            'deposit'  => ['label' => 'إيداع',  'icon' => 'fa-arrow-down',     'cls' => 'deposit'],
            'withdraw' => ['label' => 'سحب',    'icon' => 'fa-arrow-up',       'cls' => 'withdraw'],
            'purchase' => ['label' => 'شراء',   'icon' => 'fa-shopping-cart',  'cls' => 'purchase'],
          ];
          $tp = $typeMap[$trans->type] ?? ['label' => $trans->type, 'icon' => 'fa-circle', 'cls' => 'deposit'];
        @endphp
        <div class="trans-item">
          <div class="trans-type">
            <i class="fas {{ $tp['icon'] }} {{ $tp['cls'] }}"></i>
            {{ $tp['label'] }}
          </div>
          <div class="trans-amount">{{ format_currency($trans->amount) }}</div>
          <div class="trans-date">
            {{ $trans->date ?? $trans->created_at?->format('Y-m-d') }}
            @if (!empty($trans->note)) &bull; {{ $trans->note }} @endif
          </div>
        </div>
      @empty
        <div class="empty-box">
          <i class="fas fa-exchange-alt"></i>
          <h3>لا يوجد عمليات</h3>
        </div>
      @endforelse
    </div>
  </div>

  {{-- ===== Section: Settings ===== --}}
  <div class="section" id="sectionSettings">
    <h3 class="section-title"><i class="fas fa-user-cog"></i> إعدادات الحساب</h3>
    <div class="form-card">
      <form id="settingsForm" onsubmit="saveClientSettings(event)">
        @csrf
        @method('PUT')
        <label for="settingName">الاسم</label>
        <input type="text" id="settingName" name="name" value="{{ auth()->user()->name }}">

        <label for="settingPhone">رقم الهاتف</label>
        <input type="tel" id="settingPhone" name="phone" value="{{ auth()->user()->phone }}">

        <label for="settingEmail">البريد الإلكتروني</label>
        <input type="email" id="settingEmail" name="email" value="{{ auth()->user()->email }}" readonly style="opacity:0.6">

        <hr style="border:none;border-top:1px solid var(--border);margin:8px 0 20px">
        <h4 style="margin-bottom:16px;font-size:15px">تغيير كلمة المرور</h4>

        <label for="settingCurrentPass">كلمة المرور الحالية</label>
        <input type="password" id="settingCurrentPass" name="current_password" placeholder="اتركه فارغاً إذا لم تريد التغيير">

        <label for="settingNewPass">كلمة المرور الجديدة</label>
        <input type="password" id="settingNewPass" name="new_password" placeholder="اتركه فارغاً إذا لم تريد التغيير">

        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ التغييرات</button>
      </form>
    </div>
  </div>

@endsection

@push('modals')
  <!-- Buy Confirmation Modal -->
  <div class="modal-overlay" id="buyModal">
    <div class="modal-box">
      <button class="modal-close" onclick="hideModal('buyModal')">&times;</button>
      <div class="modal-title"><i class="fas fa-shopping-cart" style="color:var(--accent)"></i> تأكيد الشراء</div>
      <div style="background:var(--bg-main);padding:16px;border-radius:var(--radius);margin-bottom:16px;text-align:center">
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:4px">الباقة</p>
        <h3 id="buyPkgName" style="margin-bottom:6px"></h3>
        <p style="font-size:26px;font-weight:800;color:var(--accent)" id="buyPkgPrice"></p>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:8px">
        <span>رصيدك الحالي:</span><strong id="buyCurrentBalance"></strong>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:16px">
        <span>الرصيد بعد الشراء:</span><strong id="buyRemainingBalance" style="color:var(--accent)"></strong>
      </div>
      <div id="buyError" style="background:#fef2f2;color:#dc2626;padding:12px;border-radius:var(--radius);font-size:14px;display:none;margin-bottom:16px;text-align:center"></div>
      <div style="display:flex;gap:10px">
        <button class="btn-submit" style="flex:1" id="confirmBuyBtn" onclick="confirmBuy()">
          <i class="fas fa-check"></i> تأكيد
        </button>
        <button onclick="hideModal('buyModal')" style="flex:0.6;padding:14px;background:var(--bg-main);border:1.5px solid var(--border);border-radius:var(--radius);font-size:15px;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif">
          إلغاء
        </button>
      </div>
    </div>
  </div>

  <!-- Connect / Credentials Modal -->
  <div class="modal-overlay" id="connectModal">
    <div class="modal-box" style="text-align:center">
      <button class="modal-close" onclick="hideModal('connectModal')">&times;</button>
      <div style="width:54px;height:54px;border-radius:50%;background:#dbeafe;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
        <i class="fas fa-wifi" style="font-size:24px;color:#3b82f6"></i>
      </div>
      <h3 style="margin-bottom:4px">بيانات الاتصال</h3>
      <p id="connectFirstUsed" style="font-size:12px;color:var(--text-muted);margin-bottom:16px"></p>
      <div style="background:var(--bg-main);padding:16px;border-radius:var(--radius);text-align:right;font-size:15px;margin-bottom:20px">
        <p style="margin-bottom:10px">
          <strong>Username:</strong>
          <code id="connectUser" style="background:#e2e8f0;padding:3px 10px;border-radius:4px;font-size:14px;user-select:all;direction:ltr;float:left"></code>
        </p>
        <p>
          <strong>Password:</strong>
          <code id="connectPass" style="background:#e2e8f0;padding:3px 10px;border-radius:4px;font-size:14px;user-select:all;direction:ltr;float:left"></code>
        </p>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn-submit" style="flex:1"
          onclick="copyCredentials(document.getElementById('connectUser').textContent, document.getElementById('connectPass').textContent)">
          <i class="fas fa-copy"></i> نسخ البيانات
        </button>
        <button onclick="hideModal('connectModal')"
          style="flex:0.5;padding:14px;background:var(--bg-main);border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif">
          إغلاق
        </button>
      </div>
    </div>
  </div>

  <!-- Card Result Modal -->
  <div class="modal-overlay" id="cardResultModal">
    <div class="modal-box" style="text-align:center">
      <button class="modal-close" onclick="hideModal('cardResultModal')">&times;</button>
      <div style="width:60px;height:60px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
        <i class="fas fa-check" style="font-size:28px;color:#10b981"></i>
      </div>
      <h3 style="margin-bottom:6px">تم الشراء بنجاح!</h3>
      <p style="font-size:14px;color:var(--text-muted);margin-bottom:20px">هذه بيانات بطاقتك، يرجى حفظها</p>
      <div style="background:var(--bg-main);padding:18px;border-radius:var(--radius);text-align:right;font-size:15px;margin-bottom:20px">
        <p style="margin-bottom:10px"><strong>الباقة:</strong> <span id="resultPkg"></span></p>
        <p style="margin-bottom:10px"><strong>Username:</strong> <code id="resultUsername" style="background:#e2e8f0;padding:3px 10px;border-radius:4px;font-size:14px;user-select:all;direction:ltr"></code></p>
        <p><strong>Password:</strong> <code id="resultPassword" style="background:#e2e8f0;padding:3px 10px;border-radius:4px;font-size:14px;user-select:all;direction:ltr"></code></p>
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn-submit" style="flex:1" onclick="hideModal('cardResultModal')">
          <i class="fas fa-thumbs-up"></i> تمام
        </button>
        <button onclick="copyCredentials(document.getElementById('resultUsername').textContent, document.getElementById('resultPassword').textContent)"
          style="flex:0.6;padding:14px;background:var(--bg-main);border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;font-weight:600;cursor:pointer;font-family:'Tajawal',sans-serif;display:flex;align-items:center;justify-content:center;gap:6px">
          <i class="fas fa-copy"></i> نسخ
        </button>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
// ===== User data from server (hydrated once on page load) =====
const _serverUser = {
  id:      {{ auth()->id() }},
  name:    @json(auth()->user()->name),
  phone:   @json(auth()->user()->phone),
  balance: {{ auth()->user()->balance ?? 0 }},
};

// ===== Toast =====
function toast(msg, isError) {
  const el = document.getElementById('toastBox');
  const icon = el.querySelector('i');
  document.getElementById('toastMsg').textContent = msg;
  el.className = 'toast-box show' + (isError ? ' error' : '');
  icon.className = isError ? 'fas fa-exclamation-circle' : 'fas fa-check-circle';
  clearTimeout(el._timer);
  el._timer = setTimeout(() => { el.classList.remove('show'); }, 3000);
}

// ===== Tab Switching =====
function switchTab(name, btnEl) {
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  const target = document.getElementById('section' + name);
  if (target) target.classList.add('active');

  document.querySelectorAll('.bottom-nav .nav-item').forEach(b => b.classList.remove('active'));
  if (btnEl && !btnEl.classList.contains('nav-buy')) btnEl.classList.add('active');

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ===== Modal helpers =====
function showModal(id) { document.getElementById(id).classList.add('show'); }
function hideModal(id)  { document.getElementById(id).classList.remove('show'); }

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('show'); });
});

// ===== Buy Modal =====
let buyingPackage = null;

function openBuyModal(pkgId, pkgName, pkgPrice, available) {
  if (available <= 0) { toast('عذراً، نفذت البطاقات', true); return; }

  buyingPackage = { id: pkgId, name: pkgName, price: pkgPrice };

  const balance = _serverUser.balance;
  const remaining = balance - pkgPrice;

  document.getElementById('buyPkgName').textContent = pkgName;
  document.getElementById('buyPkgPrice').textContent = CURRENCY.format(pkgPrice);
  document.getElementById('buyCurrentBalance').textContent = CURRENCY.format(balance);
  document.getElementById('buyRemainingBalance').textContent = CURRENCY.format(remaining);

  const errorEl  = document.getElementById('buyError');
  const buyBtn   = document.getElementById('confirmBuyBtn');

  if (remaining < 0) {
    errorEl.textContent = 'رصيدك غير كافٍ لشراء هذه الباقة';
    errorEl.style.display = 'block';
    buyBtn.disabled = true; buyBtn.style.opacity = '0.5';
  } else {
    errorEl.style.display = 'none';
    buyBtn.disabled = false; buyBtn.style.opacity = '1';
  }
  showModal('buyModal');
}

function confirmBuy() {
  if (!buyingPackage) return;

  const pkg = buyingPackage;
  buyingPackage = null; // clear immediately to prevent double-submit

  const btn = document.getElementById('confirmBuyBtn');
  const errEl = document.getElementById('buyError');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الشراء...';
  errEl.style.display = 'none';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  fetch('{{ route("client.buy") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
    },
    body: JSON.stringify({ package_id: pkg.id }),
  })
  .then(r => {
    if (!r.ok && r.status !== 422) throw new Error('server_' + r.status);
    return r.json();
  })
  .then(data => {
    if (data.success) {
      hideModal('buyModal');

      // ✅ Fix: parse float to avoid toFixed() on string
      const newBalance = parseFloat(data.new_balance) || 0;
      _serverUser.balance = newBalance;
      const balEl = document.getElementById('myBalance');
      if (balEl) balEl.textContent = CURRENCY.format(newBalance);

      // Show card result
      document.getElementById('resultPkg').textContent      = data.card.package;
      document.getElementById('resultUsername').textContent = data.card.username;
      document.getElementById('resultPassword').textContent = data.card.password;
      showModal('cardResultModal');

      // Update stats counter
      const cnt = document.getElementById('myCardsCount');
      if (cnt) cnt.textContent = parseInt(cnt.textContent || '0') + 1;

      toast('تم شراء البطاقة بنجاح! 🎉');
    } else {
      // Show inline error in modal
      errEl.textContent   = data.message || 'حدث خطأ غير متوقع';
      errEl.style.display = 'block';
      buyingPackage = pkg; // restore so user can retry
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-check"></i> تأكيد الشراء';
    }
  })
  .catch(err => {
    const msg = err.message && err.message.startsWith('server_')
      ? 'خطأ في الخادم، حاول مجدداً'
      : 'تعذر الاتصال، تحقق من الإنترنت';
    errEl.textContent   = msg;
    errEl.style.display = 'block';
    buyingPackage = pkg; // restore so user can retry
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-check"></i> تأكيد الشراء';
  });
}

// ===== Recharge Submit =====
function submitRecharge(e) {
  e.preventDefault();
  const form   = document.getElementById('rechargeForm');
  const amount = document.getElementById('rechargeAmount').value;
  const note   = document.getElementById('rechargeNote').value;
  const btn    = form.querySelector('.btn-submit');

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الإرسال...';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  fetch('{{ route("client.recharge.store") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
    },
    body: JSON.stringify({ amount, note }),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('rechargeAmount').value = '';
      document.getElementById('rechargeNote').value   = '';
      toast('تم إرسال طلب الشحن بنجاح');

      // Update stats counter
      const cnt = document.getElementById('myRechargeCount');
      if (cnt) cnt.textContent = parseInt(cnt.textContent || '0') + 1;

      // Prepend new item to list
      const list = document.getElementById('myRechargeList');
      const noItems = list.querySelector('p');
      if (noItems) noItems.remove();

      const item = document.createElement('div');
      item.className = 'recharge-item';
      item.innerHTML = `
        <div>
          <strong>${CURRENCY.format(amount)}</strong>
          <span style="color:var(--text-muted);margin-right:8px;font-size:13px">${new Date().toISOString().split('T')[0]}</span>
        </div>
        <span class="badge-st pending">قيد المراجعة</span>
      `;
      list.insertBefore(item, list.firstChild);
    } else {
      toast(data.message || 'حدث خطأ', true);
    }
  })
  .catch(() => toast('حدث خطأ في الاتصال، حاول مجدداً', true))
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الطلب';
  });
}

// ===== Settings Save =====
function saveClientSettings(e) {
  e.preventDefault();
  const btn = e.target.querySelector('.btn-submit');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الحفظ...';

  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  const payload = {
    name:             document.getElementById('settingName').value.trim(),
    phone:            document.getElementById('settingPhone').value.trim(),
    current_password: document.getElementById('settingCurrentPass').value,
    new_password:     document.getElementById('settingNewPass').value,
  };

  fetch('{{ route("client.settings.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
    },
    body: JSON.stringify(payload),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Update header display
      const initial = payload.name.charAt(0);
      document.getElementById('userAvatar').textContent  = initial;
      document.getElementById('userName').textContent    = payload.name;
      document.getElementById('welcomeName').textContent = payload.name;
      _serverUser.name = payload.name;

      document.getElementById('settingCurrentPass').value = '';
      document.getElementById('settingNewPass').value     = '';

      toast('تم حفظ التغييرات بنجاح');
    } else {
      toast(data.message || 'حدث خطأ', true);
    }
  })
  .catch(() => toast('حدث خطأ في الاتصال، حاول مجدداً', true))
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> حفظ التغييرات';
  });
}

// ===== localStorage helpers (kept for backward compat / future use) =====
function getData(key)        { try { return JSON.parse(localStorage.getItem(key)); } catch(e) { return null; } }
function setData(key, value) { localStorage.setItem(key, JSON.stringify(value)); }
function formatCurrency(n)   { return CURRENCY.format(n); }

// ===== Card Connect (Hotspot Login) =====
function connectCard(cardId, username, password) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  fetch('{{ route("client.cards.track-usage") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    body: JSON.stringify({ card_id: cardId })
  })
  .then(r => r.json())
  .then(data => {
    if (!data.success) { toast(data.message || 'حدث خطأ', true); return; }

    const mikrotikUrl = (data.mikrotik_url || '').trim();

    if (mikrotikUrl) {
      try {
        // Build login URL: http://mikrotik-ip/login?username=X&password=Y
        const base = mikrotikUrl.startsWith('http') ? mikrotikUrl : 'http://' + mikrotikUrl;
        const url  = new URL(base);
        url.searchParams.set('username', username);
        url.searchParams.set('password', password);
        window.open(url.toString(), '_blank');
        toast('جارٍ فتح صفحة الاتصال...');
      } catch(e) {
        // URL is invalid — fallback to copy
        copyCredentials(username, password);
      }
    } else {
      // No MikroTik URL configured — show credentials to copy
      showConnectModal(username, password, data.first_used_at);
    }
  })
  .catch(() => {
    // Server unreachable — still show credentials
    showConnectModal(username, password, null);
  });
}

function showConnectModal(username, password, firstUsedAt) {
  document.getElementById('connectUser').textContent = username;
  document.getElementById('connectPass').textContent = password;
  const usedEl = document.getElementById('connectFirstUsed');
  if (usedEl) usedEl.textContent = firstUsedAt ? 'أول استخدام: ' + firstUsedAt : '';
  showModal('connectModal');
}

// ===== Copy Credentials =====
function copyCredentials(username, password) {
  const text = 'Username: ' + username + '\nPassword: ' + password;
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).then(() => toast('تم نسخ بيانات الدخول ✓')).catch(() => legacyCopy(text));
  } else {
    legacyCopy(text);
  }
}
function legacyCopy(text) {
  const el = document.createElement('textarea');
  el.value = text; el.style.cssText = 'position:fixed;opacity:0';
  document.body.appendChild(el); el.select(); document.execCommand('copy');
  document.body.removeChild(el);
  toast('تم نسخ بيانات الدخول ✓');
}
</script>
@endpush
