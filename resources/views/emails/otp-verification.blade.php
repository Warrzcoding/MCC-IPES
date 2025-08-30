<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCC-IPES Verification Code</title>
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
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: white;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            padding: 10px 16px; /* Compact but readable */
            background: #ffffff; /* Box color */
            color: #5b67e2; /* Number color */
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            font-family: 'Courier New', monospace;
            font-size: 28px; /* Adjust size to save width */
            font-weight: 700;
            letter-spacing: 6px; /* Visual spacing between digits inside single box */
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
            <div class="logo">🛡️</div>
            <h1>MCC-IPES Verification</h1>
            <p>Instructors Performance Evaluation System</p>
        </div>

        <div class="content">
            <p>Hello,</p>
            <p>You have requested to verify your Microsoft 365 account for MCC-IPES registration. Please use the verification code below to complete your pre-signup process.</p>
        </div>

        <div class="otp-section">
            <div class="otp-label">Your Verification Code</div>
            <div class="otp-code">
                <span class="otp-box">{{ $otpCode }}</span>
            </div>
            <div class="expiry-info">This code expires in {{ $expiryMinutes }} minutes</div>
        </div>

        <div class="content">
            <p><strong>How to use this code:</strong></p>
            <ol>
                <li>Return to the MCC-IPES pre-signup page</li>
                <li>Enter this 6-digit code in the verification field</li>
                <li>Click "Verify Code" to proceed</li>
            </ol>
        </div>

        <div class="warning">
            <strong>⚠️ Security Notice:</strong><br>
            • This code is valid for {{ $expiryMinutes }} minutes only<br>
            • Do not share this code with anyone<br>
            • If you didn't request this verification, please ignore this email
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