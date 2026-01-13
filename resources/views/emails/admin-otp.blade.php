<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Verification Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header .logo-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 120px;
            margin: 0 auto 25px;
            border-radius: 50%;
            background: #ffffff;
           
            border: 4px solid #f0f7ff;
            overflow: hidden;
        }
        .header .logo {
            width: 85%;
            height: 85%;
            object-fit: contain;
            border-radius: 50%;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .otp-section {
            background: linear-gradient(135deg, #1f8aff, #60a9ff);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            margin: 10px 0;
            text-align: center;
        }
        .otp-box {
            display: inline-block;
            padding: 10px 16px;
            background: #ffffff;
            color: #1f8aff;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            font-family: 'Courier New', monospace;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 6px;
        }
        .otp-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 10px;
        }
        .expiry-info {
            color: rgba(255, 255, 255, 0.8);
            font-size: 12px;
            margin-top: 15px;
        }
        .content {
            margin: 25px 0;
            line-height: 1.8;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 12px;
        }
        .school-info {
            margin-top: 20px;
            color: #888;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-wrapper">
                <img class="logo" src="{{ asset('images/logo.png') }}" alt="MCC-IPES Logo">
            </div>
            <h1>MCC-IPES Administrator Verification</h1>
            <p>Instructors Performance Evaluation System</p>
        </div>

        <div class="content">
            <p>Hello{{ $adminName ? ' ' . $adminName : '' }},</p>
            <p>A sign-in attempt was made using your administrator credentials. Use the code below to confirm the login.</p>
        </div>

        <div class="otp-section">
            <div class="otp-label">Your Verification Code</div>
            <div class="otp-code">
                <span class="otp-box">{{ $otpCode }}</span>
            </div>
            <div class="expiry-info">This code expires in {{ $expiryMinutes }} minutes</div>
        </div>

        <div class="content">
            <p><strong>Important:</strong></p>
            <ul>
                <li>This code is valid for {{ $expiryMinutes }} minutes only</li>
                <li>Do not share this code with anyone</li>
                <li>If you did not initiate this request, please notify the system maintainer immediately</li>
            </ul>
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong><br>
            Keep this code confidential. The login will complete only after entering this code in the verification screen. Never share it with anyone.
        </div>

        <div class="footer">
            <p>This is an automated message from MCC-IPES.</p>
            <p>Please do not reply to this email.</p>
            
            <div class="school-info">
                <strong>Madridejos Community College</strong><br>
                Instructors Performance Evaluation System<br>
                Madridejos, Cebu, Philippines
            </div>
        </div>
    </div>
</body>
</html>
