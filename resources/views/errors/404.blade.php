<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الشبكة غير موجودة — بطاقتي</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Tajawal', sans-serif;
      background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
      color: #0f172a;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 24px;
      direction: rtl;
    }
    .card {
      background: #fff;
      border-radius: 20px;
      padding: 52px 44px;
      text-align: center;
      box-shadow: 0 8px 40px rgba(15,23,42,.10);
      max-width: 480px;
      width: 100%;
    }
    .icon-wrap {
      width: 80px; height: 80px;
      background: #fff7ed;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 24px;
      font-size: 36px;
    }
    .code { font-size: 72px; font-weight: 900; color: #e2e8f0; line-height: 1; margin-bottom: 8px; }
    h1 { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
    p  { font-size: 15px; color: #64748b; line-height: 1.8; margin-bottom: 32px; }
    .subdomain {
      display: inline-block;
      background: #f1f5f9;
      border-radius: 8px;
      padding: 3px 10px;
      font-family: monospace;
      font-size: 14px;
      color: #475569;
      margin: 0 2px;
    }
    .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .btn {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 12px 24px; border-radius: 10px;
      font-size: 15px; font-weight: 700; text-decoration: none;
      transition: all .2s; font-family: 'Tajawal', sans-serif;
      cursor: pointer; border: none;
    }
    .btn-primary { background: #0ea5e9; color: #fff; }
    .btn-primary:hover { background: #0284c7; transform: translateY(-1px); }
    .btn-outline { background: transparent; border: 2px solid #e2e8f0; color: #0f172a; }
    .btn-outline:hover { border-color: #0ea5e9; color: #0ea5e9; }
    .logo { margin-bottom: 32px; font-size: 18px; font-weight: 800; color: #0f172a; }
    .logo span { color: #0ea5e9; }
  </style>
</head>
<body>
  <div class="logo">بطاقتي<span>.</span></div>

  <div class="card">
    <div class="icon-wrap">🔍</div>
    <div class="code">404</div>
    <h1>الشبكة غير موجودة</h1>
    <p>
      لم نتمكن من العثور على الشبكة
      <span class="subdomain">{{ request()->getHost() }}</span><br>
      قد يكون الرابط خاطئاً أو أن الشبكة معطّلة أو لم تُفعَّل بعد.
    </p>

    <div class="actions">
      <a href="https://{{ config('app.domain', 'xnet-wifi.store') }}/register" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        سجّل شبكة جديدة
      </a>
      <a href="https://{{ config('app.domain', 'xnet-wifi.store') }}" class="btn btn-outline">
        الصفحة الرئيسية
      </a>
    </div>
  </div>
</body>
</html>
