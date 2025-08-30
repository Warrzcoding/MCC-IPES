# reCAPTCHA Setup Guide

## Overview
This system implements progressive reCAPTCHA security:
- **reCAPTCHA v3**: Invisible verification for first-time login attempts
- **reCAPTCHA v2**: Checkbox verification after failed attempts or for high-security roles

## Setup Instructions

### 1. Get reCAPTCHA Keys
1. Go to [Google reCAPTCHA Admin Console](https://www.google.com/recaptcha/admin)
2. Create two sites:
   - **Site 1**: reCAPTCHA v2 (Checkbox)
   - **Site 2**: reCAPTCHA v3

### 2. Configure Environment Variables
Update your `.env` file with your actual keys:

```env
# reCAPTCHA Configuration
RECAPTCHA_SITE_KEY_V2=your_actual_recaptcha_v2_site_key_here
RECAPTCHA_SECRET_KEY_V2=your_actual_recaptcha_v2_secret_key_here
RECAPTCHA_SITE_KEY_V3=your_actual_recaptcha_v3_site_key_here
RECAPTCHA_SECRET_KEY_V3=your_actual_recaptcha_v3_secret_key_here
```

### 3. Progressive Security Logic

#### For Students:
- **First attempt**: reCAPTCHA v3 (invisible, score threshold: 0.5)
- **After 2 failed attempts**: reCAPTCHA v2 (checkbox)

#### For Staff:
- **First attempt**: reCAPTCHA v3 (invisible, score threshold: 0.6)
- **After 1 failed attempt**: reCAPTCHA v2 (checkbox)

#### For Admin:
- **First attempt**: reCAPTCHA v3 (invisible, score threshold: 0.7)
- **After 1 failed attempt**: reCAPTCHA v2 (checkbox)

### 4. Testing
1. Clear your browser cache
2. Try logging in with correct credentials (should use v3)
3. Try logging in with wrong credentials multiple times (should switch to v2)

### 5. Troubleshooting

#### Common Issues:
- **Keys not working**: Make sure domains are correctly configured in reCAPTCHA admin
- **v3 not loading**: Check browser console for JavaScript errors
- **v2 not showing**: Verify the site key is correct

#### Debug Mode:
Check browser console for reCAPTCHA-related messages and errors.

### 6. Security Features
- Automatic fallback if reCAPTCHA fails to load
- Different score thresholds based on user role
- Progressive escalation based on failed attempts
- Mobile-responsive reCAPTCHA sizing

## File Changes Made

1. **RecaptchaService.php**: Core reCAPTCHA verification logic
2. **LoginController.php**: Integration with login flow
3. **login.blade.php**: Frontend implementation
4. **services.php**: Configuration setup
5. **.env**: Environment variables

## Notes
- The system gracefully degrades if reCAPTCHA is not configured
- All verification is logged for security monitoring
- Mobile users get scaled reCAPTCHA for better UX