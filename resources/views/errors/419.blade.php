<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>انتهت صلاحية الجلسة — بطاقتي</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Tajawal', sans-serif;
      background: #f1f5f9;
      color: #1e293b;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      direction: rtl;
    }
    .card {
      background: #fff;
      border-radius: 16px;
      padding: 48px 40px;
      text-align: center;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      max-width: 440px;
      width: 90%;
    }
    .icon {
      width: 72px; height: 72px;
      background: #fff7ed;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 20px;
      font-size: 32px;
      color: #f59e0b;
    }
    h1 { font-size: 22px; font-weight: 800; margin-bottom: 10px; }
    p  { font-size: 15px; color: #64748b; margin-bottom: 28px; line-height: 1.7; }
    a  {
      display: inline-flex; align-items: center; gap: 8px;
      background: #10b981; color: #fff;
      padding: 12px 28px; border-radius: 10px;
      font-size: 15px; font-weight: 700; text-decoration: none;
      transition: background 0.2s;
    }
    a:hover { background: #059669; }
  </style>
</head>
<body>
  <div class="card">
    <div class="icon">⏱</div>
    <h1>انتهت صلاحية الجلسة</h1>
    <p>
      انتهت صلاحية نموذجك بسبب عدم النشاط لفترة طويلة.<br>
      يرجى تحديث الصفحة والمحاولة مجدداً.
    </p>
    <a href="javascript:history.back()">&#8592; العودة والمحاولة مجدداً</a>
  </div>
</body>
</html>
