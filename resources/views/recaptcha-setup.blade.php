<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reCAPTCHA Setup - MCC IPES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .setup-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            padding: 40px;
            margin: 20px auto;
            max-width: 800px;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }
        .status-configured {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-missing {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .step-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 15px 0;
            border-left: 5px solid #667eea;
        }
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        .btn-copy {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: #e2e8f0;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn-copy:hover {
            background: rgba(255,255,255,0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-card">
            <div class="text-center mb-4">
                <h1><i class="fas fa-shield-alt text-primary"></i> reCAPTCHA Setup</h1>
                <p class="text-muted">Configure Google reCAPTCHA for enhanced login security</p>
            </div>

            <!-- Current Status -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><i class="fas fa-check-circle"></i> reCAPTCHA v2 (Checkbox)</h5>
                    <div class="status-badge {{ config('services.recaptcha.site_key_v2') ? 'status-configured' : 'status-missing' }}">
                        {{ config('services.recaptcha.site_key_v2') ? 'Configured' : 'Not Configured' }}
                    </div>
                    @if(config('services.recaptcha.site_key_v2'))
                        <small class="text-muted d-block mt-1">Site Key: {{ substr(config('services.recaptcha.site_key_v2'), 0, 20) }}...</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <h5><i class="fas fa-eye-slash"></i> reCAPTCHA v3 (Invisible)</h5>
                    <div class="status-badge {{ config('services.recaptcha.site_key_v3') ? 'status-configured' : 'status-missing' }}">
                        {{ config('services.recaptcha.site_key_v3') ? 'Configured' : 'Not Configured' }}
                    </div>
                    @if(config('services.recaptcha.site_key_v3'))
                        <small class="text-muted d-block mt-1">Site Key: {{ substr(config('services.recaptcha.site_key_v3'), 0, 20) }}...</small>
                    @endif
                </div>
            </div>

            <!-- Setup Steps -->
            <div class="step-card">
                <h4><i class="fas fa-step-forward text-primary"></i> Step 1: Get reCAPTCHA Keys</h4>
                <p>Visit the Google reCAPTCHA Admin Console to create your sites:</p>
                <ol>
                    <li>Go to <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin Console</a></li>
                    <li>Create <strong>two separate sites</strong>:
                        <ul>
                            <li><strong>Site 1:</strong> reCAPTCHA v2 → "I'm not a robot" Checkbox</li>
                            <li><strong>Site 2:</strong> reCAPTCHA v3</li>
                        </ul>
                    </li>
                    <li>Add your domain(s): <code>{{ request()->getHost() }}</code></li>
                    <li>Copy the Site Key and Secret Key for each site</li>
                </ol>
            </div>

            <div class="step-card">
                <h4><i class="fas fa-step-forward text-primary"></i> Step 2: Update Environment Variables</h4>
                <p>Add these lines to your <code>.env</code> file:</p>
                <div style="position: relative;">
                    <div class="code-block" id="envCode">
# reCAPTCHA Configuration
RECAPTCHA_SITE_KEY_V2=your_actual_recaptcha_v2_site_key_here
RECAPTCHA_SECRET_KEY_V2=your_actual_recaptcha_v2_secret_key_here
RECAPTCHA_SITE_KEY_V3=your_actual_recaptcha_v3_site_key_here
RECAPTCHA_SECRET_KEY_V3=your_actual_recaptcha_v3_secret_key_here</div>
                    <button class="btn-copy" onclick="copyToClipboard('envCode')">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Important:</strong> Replace the placeholder values with your actual keys from Google reCAPTCHA.
                </div>
            </div>

            <div class="step-card">
                <h4><i class="fas fa-step-forward text-primary"></i> Step 3: Clear Cache & Test</h4>
                <p>After updating your .env file:</p>
                <ol>
                    <li>Clear your application cache: <code>php artisan config:clear</code></li>
                    <li>Test the configuration using the links below</li>
                    <li>Try logging in to see reCAPTCHA in action</li>
                </ol>
            </div>

            <!-- Test Links -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <a href="{{ route('test.recaptcha.config') }}" class="btn btn-outline-primary w-100" target="_blank">
                        <i class="fas fa-cog"></i> Test Configuration
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('test.recaptcha.type') }}?failed_attempts=0&user_role=student" class="btn btn-outline-info w-100" target="_blank">
                        <i class="fas fa-vial"></i> Test Logic
                    </a>
                </div>
            </div>

            <!-- Security Info -->
            <div class="alert alert-info mt-4">
                <h5><i class="fas fa-info-circle"></i> How It Works</h5>
                <ul class="mb-0">
                    <li><strong>First login attempt:</strong> Invisible reCAPTCHA v3 (seamless for users)</li>
                    <li><strong>After failed attempts:</strong> Checkbox reCAPTCHA v2 (more secure)</li>
                    <li><strong>Different thresholds:</strong> Admin/Staff (1 failure), Students (2 failures)</li>
                    <li><strong>Graceful fallback:</strong> System works even without reCAPTCHA configured</li>
                </ul>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            navigator.clipboard.writeText(text).then(function() {
                // Show success feedback
                const btn = element.parentElement.querySelector('.btn-copy');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                btn.style.background = 'rgba(40, 167, 69, 0.8)';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = 'rgba(255,255,255,0.2)';
                }, 2000);
            });
        }
    </script>
</body>
</html>