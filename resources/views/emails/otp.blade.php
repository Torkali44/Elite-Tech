<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ app()->getLocale() === 'ar' ? 'رمز التحقق' : 'Verification Code' }} — Elite Tech Community</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: #F7FAFC;
      font-family: 'Segoe UI', Arial, sans-serif;
      color: #2D3748;
      direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
    }
    .wrapper { max-width: 560px; margin: 40px auto; padding: 0 16px; }
    .card {
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(26, 54, 93, 0.10);
    }
    .header {
      background: linear-gradient(135deg, #1A365D 0%, #2B4A7C 100%);
      padding: 32px 40px;
      text-align: center;
    }
    .header .brand {
      font-size: 22px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: -0.5px;
    }
    .header .brand span { color: #F6993F; }
    .body { padding: 40px; }
    .greeting {
      font-size: 18px;
      font-weight: 700;
      color: #1A365D;
      margin-bottom: 12px;
    }
    .message {
      font-size: 15px;
      line-height: 1.7;
      color: #4A5568;
      margin-bottom: 32px;
    }
    .otp-box {
      background: #F0F4F8;
      border: 2px dashed #CBD5E0;
      border-radius: 12px;
      padding: 28px;
      text-align: center;
      margin-bottom: 28px;
    }
    .otp-label {
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #718096;
      margin-bottom: 12px;
    }
    .otp-code {
      font-size: 48px;
      font-weight: 900;
      letter-spacing: 12px;
      color: #1A365D;
      font-family: 'Courier New', monospace;
      line-height: 1;
    }
    .otp-expiry {
      font-size: 12px;
      color: #718096;
      margin-top: 12px;
      font-weight: 600;
    }
    .otp-expiry strong { color: #E53E3E; }
    .security-box {
      background: #FFFBF0;
      border: 1px solid #F6E05E;
      border-radius: 8px;
      padding: 16px 20px;
      margin-bottom: 28px;
    }
    .security-box p {
      font-size: 13px;
      color: #744210;
      line-height: 1.6;
    }
    .security-box strong { font-weight: 700; }
    .divider {
      border: none;
      border-top: 1px solid #E2E8F0;
      margin: 28px 0;
    }
    .footer {
      text-align: center;
      padding: 24px 40px;
      background: #F7FAFC;
    }
    .footer p {
      font-size: 12px;
      color: #A0AEC0;
      line-height: 1.6;
    }
    .footer a { color: #1A365D; text-decoration: none; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="card">
      <!-- Header -->
      <div class="header">
        <div class="brand">Elite <span>Community</span></div>
      </div>

      <!-- Body -->
      <div class="body">
        <p class="greeting">
          @if(app()->getLocale() === 'ar')
            مرحباً، {{ $userName }} 👋
          @else
            Hello, {{ $userName }} 👋
          @endif
        </p>

        <p class="message">
          @if(app()->getLocale() === 'ar')
            شكراً لانضمامك إلى <strong>Elite Tech Community</strong>. لإتمام تسجيل حسابك، يرجى إدخال رمز التحقق أدناه في الصفحة المفتوحة.
          @else
            Thank you for joining <strong>Elite Tech Community</strong>. To complete your registration, please enter the verification code below on the open page.
          @endif
        </p>

        <!-- OTP Box -->
        <div class="otp-box">
          <div class="otp-label">
            {{ app()->getLocale() === 'ar' ? 'رمز التحقق' : 'Verification Code' }}
          </div>
          <div class="otp-code">{{ $code }}</div>
          <div class="otp-expiry">
            @if(app()->getLocale() === 'ar')
              ينتهي الرمز خلال <strong>{{ $expiryMinutes }} دقائق</strong>
            @else
              This code expires in <strong>{{ $expiryMinutes }} minutes</strong>
            @endif
          </div>
        </div>

        <!-- Security notice -->
        <div class="security-box">
          <p>
            @if(app()->getLocale() === 'ar')
              <strong>🔒 تنبيه أمني:</strong> إذا لم تطلب هذا الرمز، يُرجى تجاهل هذا البريد بالكامل. لا تُشارك هذا الرمز مع أي شخص. فريق Elite Tech لن يطلب منك رمز التحقق أبداً.
            @else
              <strong>🔒 Security Notice:</strong> If you didn't request this code, please ignore this email entirely. Never share this code with anyone. The Elite Tech team will never ask for your verification code.
            @endif
          </p>
        </div>

        <hr class="divider">

        <p style="font-size: 13px; color: #718096; line-height: 1.7;">
          @if(app()->getLocale() === 'ar')
            إذا واجهت أي مشكلة، تواصل معنا عبر البريد الإلكتروني:
            <a href="mailto:{{ config('mail.from.address') }}" style="color: #1A365D;">{{ config('mail.from.address') }}</a>
          @else
            If you need help, contact us at:
            <a href="mailto:{{ config('mail.from.address') }}" style="color: #1A365D;">{{ config('mail.from.address') }}</a>
          @endif
        </p>
      </div>

      <!-- Footer -->
      <div class="footer">
        <p>
          © {{ date('Y') }} Elite Tech Community. 
          @if(app()->getLocale() === 'ar')
            جميع الحقوق محفوظة.<br>
            هذا البريد أُرسل تلقائياً، يُرجى عدم الرد عليه.
          @else
            All rights reserved.<br>
            This is an automated email, please do not reply.
          @endif
        </p>
      </div>
    </div>
  </div>
</body>
</html>
