<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>طلبك قيد المراجعة — بطاقتي</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    body {
      font-family:'Tajawal',sans-serif;
      background:linear-gradient(135deg,#f5f3ff 0%,#faf5ff 50%,#ede9fe 100%);
      min-height:100vh; display:flex; align-items:center; justify-content:center;
      padding:24px;
    }
    .card {
      background:#fff; border-radius:24px; padding:52px 48px;
      max-width:520px; width:100%; text-align:center;
      box-shadow:0 20px 60px rgba(124,58,237,.12);
    }
    .icon-wrap {
      width:96px; height:96px; background:linear-gradient(135deg,#ede9fe,#ddd6fe);
      border-radius:50%; display:flex; align-items:center; justify-content:center;
      margin:0 auto 28px; font-size:42px; color:#7c3aed;
      animation: pulse 2.5s ease-in-out infinite;
    }
    @keyframes pulse {
      0%, 100% { transform:scale(1); box-shadow:0 0 0 0 rgba(124,58,237,.2); }
      50%       { transform:scale(1.05); box-shadow:0 0 0 12px rgba(124,58,237,0); }
    }
    h1 { font-size:26px; font-weight:800; color:#1e293b; margin-bottom:12px; }
    .sub { font-size:15px; color:#64748b; line-height:1.7; margin-bottom:28px; }
    .info-box {
      background:#f5f3ff; border:1px solid #ddd6fe; border-radius:12px;
      padding:16px 20px; margin-bottom:28px; text-align:right;
    }
    .info-row { display:flex; align-items:center; gap:10px; padding:6px 0; font-size:14px; color:#374151; }
    .info-row i { color:#7c3aed; width:16px; text-align:center; }
    .info-row strong { color:#1e293b; }
    .steps { text-align:right; margin-bottom:32px; }
    .steps h3 { font-size:14px; font-weight:700; color:#374151; margin-bottom:12px; }
    .step-item {
      display:flex; align-items:flex-start; gap:12px; padding:8px 0;
      border-bottom:1px solid #f1f5f9; font-size:13px; color:#64748b;
    }
    .step-item:last-child { border-bottom:none; }
    .step-dot {
      width:22px; height:22px; border-radius:50%; flex-shrink:0; margin-top:1px;
      display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700;
    }
    .step-done  { background:#d1fae5; color:#059669; }
    .step-wait  { background:#fef3c7; color:#d97706; }
    .step-next  { background:#f1f5f9; color:#94a3b8; }
    .btn-home {
      display:inline-flex; align-items:center; gap:8px;
      padding:13px 28px; background:linear-gradient(135deg,#7c3aed,#6d28d9);
      color:#fff; border-radius:12px; text-decoration:none;
      font-size:14px; font-weight:700; transition:all .2s;
    }
    .btn-home:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(124,58,237,.3); }
    .footer-note { font-size:12px; color:#94a3b8; margin-top:20px; }
  </style>
</head>
<body>
<div class="card">

  <div class="icon-wrap">
    <i class="fas fa-hourglass-half"></i>
  </div>

  <h1>طلبك قيد المراجعة</h1>
  <p class="sub">
    تم استلام طلب تسجيل شبكتك بنجاح.<br>
    سيقوم فريقنا بمراجعته وإشعارك خلال <strong>24 ساعة</strong> كحد أقصى.
  </p>

  @if($tenant)
  <div class="info-box">
    <div class="info-row">
      <i class="fas fa-network-wired"></i>
      <span>اسم الشبكة: <strong>{{ $tenant->name }}</strong></span>
    </div>
    <div class="info-row">
      <i class="fas fa-link"></i>
      <span dir="ltr" style="font-family:monospace">{{ $tenant->subdomain }}.{{ config('app.domain','بطاقتي.com') }}</span>
    </div>
    <div class="info-row">
      <i class="fas fa-tag"></i>
      <span>الباقة: <strong>{{ $tenant->plan?->name ?? '—' }}</strong></span>
    </div>
  </div>
  @endif

  <div class="steps">
    <h3><i class="fas fa-list-check" style="margin-left:6px;color:#7c3aed"></i> مراحل التفعيل</h3>
    <div class="step-item">
      <div class="step-dot step-done"><i class="fas fa-check" style="font-size:10px"></i></div>
      <div><strong>تم إرسال الطلب</strong><br>استلمنا طلبك وبياناتك بنجاح</div>
    </div>
    <div class="step-item">
      <div class="step-dot step-wait"><i class="fas fa-clock" style="font-size:10px"></i></div>
      <div><strong>مراجعة الطلب</strong><br>يراجع الفريق طلبك للتحقق من البيانات</div>
    </div>
    <div class="step-item">
      <div class="step-dot step-next">3</div>
      <div><strong>تفعيل الشبكة</strong><br>ستتلقى إشعاراً عند قبول طلبك وتفعيل لوحة التحكم</div>
    </div>
  </div>

  <a href="{{ url('/') }}" class="btn-home">
    <i class="fas fa-home"></i> العودة للصفحة الرئيسية
  </a>

  <p class="footer-note">
    لديك استفسار؟ تواصل معنا عبر البريد الإلكتروني
  </p>

</div>
</body>
</html>
