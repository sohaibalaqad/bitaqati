<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>تسجيل الدخول - بطاقتي</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body { background: var(--bg-main); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
    .login-container { width: 100%; max-width: 420px; }
    .login-card {
      background: var(--bg-card);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      padding: 40px 32px;
      text-align: center;
    }
    .login-logo {
      font-size: 32px;
      font-weight: 800;
      color: var(--text-main);
      margin-bottom: 8px;
      text-decoration: none;
      display: block;
    }
    .login-logo span { color: var(--accent); }
    .login-subtitle { color: var(--text-muted); font-size: 15px; margin-bottom: 32px; }
    .login-card .form-group { text-align: right; margin-bottom: 20px; }
    .login-card .form-group label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-main); }
    .input-icon-wrapper {
      position: relative;
    }
    .input-icon-wrapper i {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 15px;
    }
    .input-icon-wrapper input {
      padding-right: 42px !important;
    }
    .login-card .form-control {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius);
      font-size: 15px;
      font-family: 'Tajawal', sans-serif;
      background: var(--bg-main);
      color: var(--text-main);
      transition: border-color 0.2s;
    }
    .login-card .form-control:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }
    .login-card .form-control.is-invalid { border-color: #dc2626; }
    .remember-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; font-size: 14px; }
    .remember-row label { display: flex; align-items: center; gap: 8px; cursor: pointer; color: var(--text-secondary); }
    .remember-row input[type="checkbox"] { width: 16px; height: 16px; accent-color: var(--accent); }
    .login-btn {
      width: 100%;
      padding: 14px;
      background: var(--accent);
      color: white;
      border: none;
      border-radius: var(--radius);
      font-size: 16px;
      font-weight: 700;
      font-family: 'Tajawal', sans-serif;
      cursor: pointer;
      transition: background 0.2s, transform 0.1s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .login-btn:hover { background: #059669; }
    .login-btn:active { transform: scale(0.98); }
    .login-footer { margin-top: 24px; font-size: 14px; color: var(--text-muted); }
    .login-footer a { color: var(--accent); font-weight: 600; text-decoration: none; }
    .login-footer a:hover { text-decoration: underline; }
    .login-error {
      background: #fef2f2;
      color: #dc2626;
      padding: 12px 16px;
      border-radius: var(--radius);
      font-size: 14px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .login-error i { font-size: 16px; }
    .field-error {
      color: #dc2626;
      font-size: 13px;
      margin-top: 4px;
      text-align: right;
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-card">
    <img src="{{ asset('images/logo.png') }}" style="width:80px;height:80px;border-radius:16px;margin:0 auto 12px;display:block" alt="بطاقتي">
    <a href="/" class="login-logo">بطاقتي</a>
    <p class="login-subtitle">تسجيل الدخول إلى حسابك</p>

    {{-- Session-level error (e.g. invalid credentials) --}}
    @if (session('error'))
      <div class="login-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif

    {{-- General validation error summary --}}
    @if ($errors->any() && !$errors->has('phone') && !$errors->has('password'))
      <div class="login-error">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('client.login') }}">
      @csrf

      <div class="form-group">
        <label for="phone">رقم الجوال</label>
        <div class="input-icon-wrapper">
          <i class="fas fa-phone"></i>
          <input
            type="tel"
            class="form-control @error('phone') is-invalid @enderror"
            id="phone"
            name="phone"
            value="{{ old('phone') }}"
            required
            placeholder="أدخل رقم الجوال"
            autocomplete="tel"
          >
        </div>
        @error('phone')
          <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="password">كلمة المرور</label>
        <div class="input-icon-wrapper">
          <i class="fas fa-lock"></i>
          <input
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            id="password"
            name="password"
            required
            placeholder="أدخل كلمة المرور"
            autocomplete="current-password"
          >
        </div>
        @error('password')
          <div class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
        @enderror
      </div>

      <div class="remember-row">
        <label>
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
          تذكرني
        </label>
      </div>

      <button type="submit" class="login-btn">
        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
      </button>
    </form>

    <div class="login-footer">
      <p>لوحة تحكم الإدارة؟ <a href="{{ route('admin.login') }}">اضغط هنا</a></p>
    </div>
  </div>
</div>

</body>
</html>
