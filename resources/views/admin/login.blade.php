<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>تسجيل دخول الإدارة - بطاقتي</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body { background: var(--bg-main); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
    .login-container { width: 100%; max-width: 420px; }
    .login-card { background: var(--bg-card); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); padding: 40px 32px; text-align: center; }
    .login-logo { font-size: 32px; font-weight: 800; color: var(--text-main); margin-bottom: 8px; display: block; }
    .login-logo span { color: var(--accent); }
    .login-subtitle { color: var(--text-muted); font-size: 15px; margin-bottom: 32px; }
    .admin-badge { display: inline-block; background: #dbeafe; color: #1d4ed8; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; margin-bottom: 24px; }
    .form-group { text-align: right; margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-main); }
    .input-icon-wrapper { position: relative; }
    .input-icon-wrapper i { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 15px; }
    .input-icon-wrapper input { padding-right: 42px !important; }
    .form-control { width: 100%; padding: 12px 16px; border: 1.5px solid var(--border); border-radius: var(--radius); font-size: 15px; font-family: 'Tajawal', sans-serif; background: var(--bg-main); color: var(--text-main); transition: border-color 0.2s; }
    .form-control:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
    .login-btn { width: 100%; padding: 14px; background: #1d4ed8; color: white; border: none; border-radius: var(--radius); font-size: 16px; font-weight: 700; font-family: 'Tajawal', sans-serif; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .login-btn:hover { background: #1e40af; }
    .login-footer { margin-top: 24px; font-size: 14px; color: var(--text-muted); }
    .login-footer a { color: var(--accent); font-weight: 600; text-decoration: none; }
    .login-error { background: #fef2f2; color: #dc2626; padding: 12px 16px; border-radius: var(--radius); font-size: 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
  </style>
</head>
<body>
<div class="login-container">
  <div class="login-card">
    <img src="{{ asset('images/logo.png') }}" style="width:80px;height:80px;border-radius:16px;margin:0 auto 12px;display:block" alt="بطاقتي">
    <span class="login-logo">بطاقتي</span>
    <span class="admin-badge"><i class="fas fa-shield-alt"></i> لوحة الإدارة</span>
    <p class="login-subtitle">تسجيل دخول المدير</p>

    @if($errors->any())
    <div class="login-error"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span></div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
      @csrf
      <div class="form-group">
        <label>البريد الإلكتروني</label>
        <div class="input-icon-wrapper">
          <i class="fas fa-envelope"></i>
          <input type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="admin@xnet-wifi.store">
        </div>
      </div>
      <div class="form-group">
        <label>كلمة المرور</label>
        <div class="input-icon-wrapper">
          <i class="fas fa-lock"></i>
          <input type="password" class="form-control" name="password" required placeholder="أدخل كلمة المرور">
        </div>
      </div>
      <button type="submit" class="login-btn"><i class="fas fa-sign-in-alt"></i> دخول</button>
    </form>

    <div class="login-footer">
      <p>دخول العملاء؟ <a href="{{ route('client.login') }}">اضغط هنا</a></p>
    </div>
  </div>
</div>
</body>
</html>
