<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Verification Code</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #102a4f; background-color: #f5f7fb; margin: 0; padding: 24px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 36px; box-shadow: 0 12px 30px rgba(16, 42, 79, 0.12); }
        .header { text-align: center; margin-bottom: 28px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 700; }
        .intro { margin-bottom: 24px; font-size: 15px; }
        .otp-box { display: inline-block; margin: 0 auto 18px; padding: 12px 24px; border-radius: 12px; background: linear-gradient(135deg, #1f8aff, #60a9ff); color: #ffffff; font-size: 30px; letter-spacing: 12px; font-weight: 700; font-family: 'Courier New', monospace; }
        .details { font-size: 14px; color: #304a6d; margin-bottom: 24px; }
        .note { background: #e8f1ff; border-left: 4px solid #1f8aff; padding: 16px; border-radius: 10px; font-size: 13px; }
        .footer { margin-top: 32px; font-size: 12px; color: #607191; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MCC-IPES Administrator Verification</h1>
        </div>
        <div class="intro">
            <p>Hello{{ $adminName ? ' ' . $adminName : '' }},</p>
            <p>A sign-in attempt was made using your administrator credentials. Use the code below to confirm the login.</p>
        </div>
        <div style="text-align:center;">
            <div class="otp-box">{{ $otpCode }}</div>
        </div>
        <div class="details">
            <p>This code expires in {{ $expiryMinutes }} minutes.</p>
            <p>If you did not initiate this request, please notify the system maintainer.</p>
        </div>
        <div class="note">
            Keep this code confidential. Never share it with anyone. The login will complete only after entering this code in the verification screen.
        </div>
        <div class="footer">
            Madridejos Community College · Instructors Performance Evaluation System
        </div>
    </div>
</body>
</html>
