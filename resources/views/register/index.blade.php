<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل شبكة جديدة — بطاقتي</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'Tajawal',sans-serif; background:#f8fafc; color:#1e293b; min-height:100vh; }

    /* ── Top nav ── */
    .top-nav {
      background:#fff; border-bottom:1px solid #e2e8f0; padding:0 32px;
      display:flex; align-items:center; justify-content:space-between; height:64px;
      position:sticky; top:0; z-index:10;
    }
    .top-nav .logo { font-size:22px; font-weight:800; color:#7c3aed; display:flex; align-items:center; gap:8px; }
    .top-nav a { font-size:14px; color:#64748b; text-decoration:none; }
    .top-nav a:hover { color:#7c3aed; }

    /* ── Page wrapper ── */
    .page { max-width:860px; margin:40px auto; padding:0 20px 60px; }

    .page-title { font-size:28px; font-weight:800; color:#1e293b; text-align:center; margin-bottom:8px; }
    .page-sub   { font-size:15px; color:#64748b; text-align:center; margin-bottom:40px; }

    /* ── Step label ── */
    .step-label {
      display:inline-flex; align-items:center; gap:8px;
      background:#ede9fe; color:#7c3aed; border-radius:24px;
      font-size:13px; font-weight:700; padding:5px 16px; margin-bottom:12px;
    }
    .step-label .num {
      width:20px; height:20px; background:#7c3aed; color:#fff;
      border-radius:50%; display:flex; align-items:center; justify-content:center;
      font-size:11px; font-weight:800;
    }

    /* ── Section card ── */
    .section-card {
      background:#fff; border:1px solid #e2e8f0; border-radius:16px;
      padding:28px; margin-bottom:24px;
    }
    .section-card h2 { font-size:17px; font-weight:800; color:#1e293b; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
    .section-card h2 i { color:#7c3aed; }

    /* ── Plan cards ── */
    .plans-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:14px; }
    .plan-card {
      border:2px solid #e2e8f0; border-radius:12px; padding:20px 16px;
      cursor:pointer; transition:all .2s; position:relative; text-align:center;
    }
    .plan-card:hover { border-color:#7c3aed; box-shadow:0 4px 16px rgba(124,58,237,.1); }
    .plan-card.selected { border-color:#7c3aed; background:#faf5ff; box-shadow:0 4px 16px rgba(124,58,237,.15); }
    .plan-card input[type=radio] { position:absolute; opacity:0; pointer-events:none; }
    .plan-check {
      position:absolute; top:10px; left:10px; width:22px; height:22px;
      background:#e2e8f0; border-radius:50%; display:flex; align-items:center; justify-content:center;
      transition:all .2s;
    }
    .plan-card.selected .plan-check { background:#7c3aed; }
    .plan-card.selected .plan-check::after { content:'✓'; color:#fff; font-size:12px; font-weight:800; }
    .plan-popular {
      position:absolute; top:-1px; right:50%; transform:translateX(50%);
      background:#f59e0b; color:#fff; font-size:10px; font-weight:700;
      padding:3px 12px; border-radius:0 0 8px 8px;
    }
    .plan-name  { font-size:16px; font-weight:800; color:#1e293b; margin:12px 0 4px; }
    .plan-price { font-size:22px; font-weight:800; color:#7c3aed; margin-bottom:12px; }
    .plan-price span { font-size:13px; font-weight:500; color:#64748b; }
    .plan-feat  { font-size:12px; color:#64748b; line-height:1.8; }

    /* ── Form fields ── */
    .field-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
    .field { margin-bottom:16px; }
    .field:last-child { margin-bottom:0; }
    .field label { display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px; }
    .field label .req { color:#ef4444; margin-right:2px; }
    .field input {
      width:100%; padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:10px;
      font-size:14px; font-family:'Tajawal',sans-serif; color:#1e293b; background:#fff;
      transition:border-color .15s;
    }
    .field input:focus { outline:none; border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.1); }
    .field input[dir=ltr] { text-align:left; }
    .field .hint { font-size:11px; color:#94a3b8; margin-top:4px; }
    .field .hint code { background:#f1f5f9; padding:1px 5px; border-radius:4px; font-size:11px; }
    .subdomain-wrap { display:flex; align-items:center; gap:0; }
    .subdomain-wrap input { border-radius:10px 0 0 10px; border-left:none; flex:1; }
    .subdomain-suffix {
      padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:0 10px 10px 0;
      background:#f8fafc; color:#64748b; font-size:13px; white-space:nowrap;
      font-family:'Tajawal',sans-serif;
    }

    /* ── Error messages ── */
    .field-error { font-size:12px; color:#ef4444; margin-top:4px; display:flex; align-items:center; gap:4px; }
    .alert-error {
      background:#fef2f2; border:1px solid #fecaca; color:#991b1b;
      padding:14px 18px; border-radius:12px; margin-bottom:24px; font-size:14px;
    }

    /* ── Submit button ── */
    .btn-submit {
      width:100%; padding:14px; background:linear-gradient(135deg,#7c3aed,#6d28d9);
      color:#fff; border:none; border-radius:12px; font-size:16px; font-weight:700;
      font-family:'Tajawal',sans-serif; cursor:pointer; transition:all .2s;
      display:flex; align-items:center; justify-content:center; gap:8px;
    }
    .btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(124,58,237,.35); }
    .btn-submit:active { transform:translateY(0); }

    .login-link { text-align:center; margin-top:20px; font-size:14px; color:#64748b; }
    .login-link a { color:#7c3aed; font-weight:700; text-decoration:none; }

    @media(max-width:640px) {
      .field-grid-2 { grid-template-columns:1fr; }
      .plans-grid { grid-template-columns:1fr 1fr; }
      .page-title { font-size:22px; }
    }
  </style>
</head>
<body>

<nav class="top-nav">
  <div class="logo">
    <i class="fas fa-wifi"></i> بطاقتي
  </div>
  <div style="display:flex;align-items:center;gap:20px">
    <a href="{{ url('/') }}"><i class="fas fa-home" style="margin-left:4px"></i> الرئيسية</a>
    <a href="{{ route('admin.login') }}">تسجيل الدخول</a>
  </div>
</nav>

<div class="page">

  <h1 class="page-title">سجّل شبكتك الآن</h1>
  <p class="page-sub">اختر الباقة المناسبة واملأ بياناتك — سيتم مراجعة طلبك وتفعيله خلال 24 ساعة</p>

  @if(session('error'))
    <div class="alert-error"><i class="fas fa-exclamation-circle" style="margin-left:6px"></i>{{ session('error') }}</div>
  @endif

  <form method="POST" action="{{ route('register.submit') }}" id="regForm">
    @csrf

    {{-- ── Step 1: Plan ── --}}
    <div class="step-label"><div class="num">1</div> اختر الباقة</div>
    <div class="section-card">
      <h2><i class="fas fa-tags"></i> باقات الاشتراك</h2>

      @error('plan_id')
        <div class="alert-error" style="margin-bottom:16px"><i class="fas fa-exclamation-circle" style="margin-left:6px"></i>{{ $message }}</div>
      @enderror

      <div class="plans-grid">
        @foreach($plans as $plan)
        <label class="plan-card {{ old('plan_id') == $plan->id ? 'selected' : ($loop->first && !old('plan_id') ? 'selected' : '') }}"
               id="plan-label-{{ $plan->id }}" onclick="selectPlan({{ $plan->id }})">
          <input type="radio" name="plan_id" value="{{ $plan->id }}"
            {{ old('plan_id', $loop->first ? $plan->id : '') == $plan->id ? 'checked' : '' }}>
          @if($plan->is_popular)
            <div class="plan-popular">⭐ الأكثر طلباً</div>
          @endif
          <div class="plan-check" id="check-{{ $plan->id }}"></div>
          <div class="plan-name">{{ $plan->name }}</div>
          <div class="plan-price">
            {{ $plan->price == 0 ? 'مجاناً' : number_format($plan->price, 0) }}
            @if($plan->price > 0)<span>ر.س / شهر</span>@endif
          </div>
          <div class="plan-feat">
            @if($plan->max_cards == 0) بطاقات غير محدودة @else حتى {{ number_format($plan->max_cards) }} بطاقة @endif<br>
            @if($plan->max_users == 0) مستخدمون غير محدودون @else حتى {{ number_format($plan->max_users) }} مستخدم @endif
            @if(!empty($plan->features))
              @foreach(array_slice($plan->features, 0, 2) as $f)<br>{{ $f }}@endforeach
            @endif
          </div>
        </label>
        @endforeach
      </div>
    </div>

    {{-- ── Step 2: Network ── --}}
    <div class="step-label"><div class="num">2</div> معلومات الشبكة</div>
    <div class="section-card">
      <h2><i class="fas fa-network-wired"></i> بيانات الشبكة</h2>

      <div class="field">
        <label>اسم الشبكة <span class="req">*</span></label>
        <input type="text" name="network_name" value="{{ old('network_name') }}" placeholder="مثال: شبكة النور للإنترنت" required>
        @error('network_name')<div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>@enderror
      </div>

      <div class="field">
        <label>الرابط المختصر (Subdomain) <span class="req">*</span></label>
        <div class="subdomain-wrap">
          <input type="text" name="subdomain" dir="ltr" value="{{ old('subdomain') }}"
            placeholder="alnour" required pattern="[a-z0-9\-]+"
            oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9\-]/g,'')">
          <div class="subdomain-suffix">.{{ config('app.domain', 'بطاقتي.com') }}</div>
        </div>
        <div class="hint">أحرف إنجليزية صغيرة وأرقام وشرطة فقط — مثال: <code>al-nour</code></div>
        @error('subdomain')<div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- ── Step 3: Admin account ── --}}
    <div class="step-label"><div class="num">3</div> بيانات حسابك</div>
    <div class="section-card">
      <h2><i class="fas fa-user-tie"></i> حساب مدير الشبكة</h2>

      <div class="field-grid-2">
        <div class="field" style="margin-bottom:0">
          <label>الاسم الكامل <span class="req">*</span></label>
          <input type="text" name="name" value="{{ old('name') }}" placeholder="أحمد محمد" required>
          @error('name')<div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>@enderror
        </div>
        <div class="field" style="margin-bottom:0">
          <label>رقم الهاتف <span class="req">*</span></label>
          <input type="text" name="phone" dir="ltr" value="{{ old('phone') }}" placeholder="05xxxxxxxx" required>
          @error('phone')<div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="field" style="margin-top:16px">
        <label>البريد الإلكتروني <span class="req">*</span></label>
        <input type="email" name="email" dir="ltr" value="{{ old('email') }}" placeholder="admin@example.com" required>
        @error('email')<div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>@enderror
      </div>

      <div class="field-grid-2" style="margin-top:16px">
        <div class="field" style="margin-bottom:0">
          <label>كلمة المرور <span class="req">*</span></label>
          <input type="password" name="password" placeholder="8 أحرف على الأقل" required minlength="8">
          @error('password')<div class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>@enderror
        </div>
        <div class="field" style="margin-bottom:0">
          <label>تأكيد كلمة المرور <span class="req">*</span></label>
          <input type="password" name="password_confirmation" placeholder="أعد كتابة كلمة المرور" required>
        </div>
      </div>
    </div>

    <button type="submit" class="btn-submit">
      <i class="fas fa-paper-plane"></i>
      إرسال طلب التسجيل
    </button>
  </form>

  <div class="login-link">
    لديك حساب بالفعل؟ <a href="{{ route('admin.login') }}">سجّل الدخول</a>
  </div>

</div>

<script>
function selectPlan(id) {
  // Deselect all
  document.querySelectorAll('.plan-card').forEach(el => el.classList.remove('selected'));
  // Select clicked
  document.getElementById('plan-label-' + id).classList.add('selected');
  // Check the radio
  document.querySelector(`input[name=plan_id][value="${id}"]`).checked = true;
}
// Init: mark the pre-selected plan (for old() repopulation on validation fail)
document.querySelectorAll('.plan-card.selected').forEach(el => {
  const id = el.id.replace('plan-label-', '');
  document.getElementById('check-' + id).style.cssText = '';
});
</script>
</body>
</html>
