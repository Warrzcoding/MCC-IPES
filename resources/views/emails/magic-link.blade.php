<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { text-align: center; margin-bottom: 30px; }
        .btn { display: inline-block; padding: 12px 24px; background: #4c6ef5; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .footer { margin-top: 30px; font-size: 12px; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Magic Login Link</h2>
        </div>
        <p>Hello,</p>
        <p>You requested a magic link to reset your password for MCC IPES. Click the button below to proceed:</p>
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $url }}" class="btn">Reset Password</a>
        </div>
        <p>This link will expire in 60 minutes and can only be used once.</p>
        <p>If you did not request this, please ignore this email.</p>
        <div class="footer">
            <p>&copy; {{ date('Y') }} MCC IPES. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
