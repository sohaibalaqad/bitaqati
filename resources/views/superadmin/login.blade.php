<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>دخول - Super Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <style>
    body { background: linear-gradient(135deg, #1e1b4b 0%, #312e81 60%, #4f46e5 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .login-box { background:#fff; border-radius:16px; padding:40px; width:100%; max-width:420px; box-shadow:0 25px 50px rgba(0,0,0,.3); }
    .login-icon { width:64px;height:64px;background:linear-gradient(135deg,#7c3aed,#4f46e5);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:28px;color:#fff }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="login-icon"><i class="fas fa-crown"></i></div>
    <h2 style="text-align:center;margin:0 0 6px;font-size:22px;font-weight:800;color:#1e1b4b">لوحة التحكم العليا</h2>
    <p style="text-align:center;color:#6b7280;font-size:14px;margin-bottom:28px">منصة بطاقتي — Super Admin</p>

    @if($errors->any())
      <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px">
        <i class="fas fa-exclamation-circle" style="margin-left:6px"></i>{{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('superadmin.login.submit') }}">
      @csrf
      <div style="margin-bottom:16px">
        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:6px">البريد الإلكتروني</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
          style="width:100%;padding:11px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:15px;font-family:'Tajawal',sans-serif;box-sizing:border-box;direction:ltr">
      </div>
      <div style="margin-bottom:24px">
        <label style="font-size:14px;font-weight:600;color:#374151;display:block;margin-bottom:6px">كلمة المرور</label>
        <input type="password" name="password" required
          style="width:100%;padding:11px 14px;border:1.5px solid #d1d5db;border-radius:8px;font-size:15px;font-family:'Tajawal',sans-serif;box-sizing:border-box;direction:ltr">
      </div>
      <button type="submit" style="width:100%;padding:13px;background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:700;font-family:'Tajawal',sans-serif;cursor:pointer">
        <i class="fas fa-sign-in-alt" style="margin-left:8px"></i> دخول
      </button>
    </form>
  </div>
</body>
</html>
