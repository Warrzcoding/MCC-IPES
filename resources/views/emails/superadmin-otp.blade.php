<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Verification Code</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            line-height: 1.6;
            color: #39FF14;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #0a0a0a;
        }
        .email-container {
            background: #111;
            border: 1px solid #00FFFF;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header .logo-wrapper {
            width: 125px;
            height: 125px;
            margin: 0 auto 25px;
            border-radius: 50%;
            background-color: #000;
            border: 3px solid #00FFFF;
            /* Glowing dropshadow cyan + purple/magenta */
            box-shadow: 0 0 15px #00FFFF, 0 0 30px #FF00FF;
            display: table;
        }
        .header .logo-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .header .logo {
            width: 90px;
            height: auto;
            display: inline-block;
            vertical-align: middle;
            filter: drop-shadow(0 0 5px #00FFFF);
        }
        .header h1 {
            color: #00FFFF;
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }
        .header p {
            color: #39FF14;
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.8;
        }
        .otp-section {
            background: #000;
            border: 1px solid #39FF14;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            box-shadow: inset 0 0 10px rgba(57, 255, 20, 0.2);
        }
        .otp-code {
            margin: 10px 0;
            text-align: center;
        }
        .otp-box {
            display: inline-block;
            padding: 15px 25px;
            background: #111;
            color: #00FFFF;
            border: 2px solid #00FFFF;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            text-shadow: 0 0 15px rgba(0, 255, 255, 0.8);
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
        }
        .otp-label {
            color: #39FF14;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .expiry-info {
            color: #ff00ff;
            font-size: 13px;
            margin-top: 20px;
            font-weight: bold;
        }
        .content {
            margin: 25px 0;
            line-height: 1.8;
        }
        .content p {
            margin-bottom: 15px;
        }
        .warning {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #ff0000;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #ff4444;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
            color: #555;
            font-size: 12px;
        }
        .school-info {
            margin-top: 20px;
            color: #444;
            font-size: 11px;
        }
        .highlight {
            color: #00FFFF;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-wrapper">
                <div class="logo-cell">
                    <img class="logo" src="{{ url('images/logo.png') }}" alt="MCC-IPES Logo">
                </div>
            </div>
            <h1>Access Protocol: Super Admin</h1>
            <p>Verification Required for System Override</p>
        </div>

        <div class="content">
            <p>Greetings, <span class="highlight">{{ $adminName ?? 'Root User' }}</span>.</p>
            <p>A secure login attempt detected. Input the following authorization token to proceed with system access.</p>
        </div>

        <div class="otp-section">
            <div class="otp-label">Authorization Token</div>
            <div class="otp-code">
                <span class="otp-box">{{ $otpCode }}</span>
            </div>
            <div class="expiry-info">TOKEN EXPIRES IN: {{ $expiryMinutes }} MINUTES</div>
        </div>

        <div class="content">
            <p><strong style="color: #00FFFF;">SECURITY PARAMETERS:</strong></p>
            <ul style="list-style-type: square; color: #39FF14;">
                <li>Single-use token valid for {{ $expiryMinutes }} minutes only.</li>
                <li>Confidentiality level: HIGH. Do not disclose.</li>
                <li>Unauthorized access attempts are logged and monitored.</li>
            </ul>
        </div>

        <div class="warning">
            <strong>CRITICAL NOTICE:</strong><br>
            If you did not initiate this authentication sequence, the system security may be compromised. 
            Initiate immediate counter-measures.
        </div>

        <div class="footer">
            <p>AUTOMATED SECURE PROTOCOL // MCC-IPES</p>
            <p>NO REPLY REQUESTED</p>
            
            <div class="school-info">
                <strong>MADRIDEJOS COMMUNITY COLLEGE</strong><br>
                SUPER ADMIN TERMINAL ACCESS<br>
                EST. MCC-IPES SECURITY NODE
            </div>
        </div>
    </div>
</body>
</html>
