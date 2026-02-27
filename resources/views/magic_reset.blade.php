<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Reset Password - MCC IPES</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #5a189a 0%, #d0006f 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .reset-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 400px;
            padding: 30px 25px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 22px;
        }
        .logo-container .icon-box {
            width: 65px;
            height: 65px;
            background: radial-gradient(circle at 30% 20%, #ffffff 0%, #f3f5ff 45%, #e3e7ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        .logo-container img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }
        .reset-card h3 {
            color: #1a1a1a;
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .reset-card p {
            color: #666;
            font-size: 0.8rem;
            line-height: 1.4;
        }
        .form-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #444;
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
        }
        .input-group {
            background: #fdfdfd;
            border-radius: 14px;
            overflow: hidden;
            border: 2px solid #eee;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 5px;
        }
        .input-group:focus-within {
            border-color: #4c6ef5;
            box-shadow: 0 0 0 4px rgba(76, 110, 245, 0.1);
        }
        .input-group-text {
            background: transparent;
            border: none;
            padding: 0 15px;
            color: #4c6ef5;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .toggle-password {
            cursor: pointer;
            color: #888;
            transition: color 0.2s;
            background: none;
            border: none;
            padding: 0 15px;
            display: flex;
            align-items: center;
        }
        .toggle-password:hover {
            color: #4c6ef5;
        }
        .form-control {
            background: transparent;
            border: none;
            padding: 11px 15px;
            font-size: 0.85rem;
            color: #333;
        }
        .form-control:focus {
            background: transparent;
            box-shadow: none;
            outline: none;
        }
        .btn-reset {
            background: linear-gradient(135deg, #4c6ef5 0%, #d0006f 100%);
            color: white !important;
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 8px 20px rgba(208, 0, 111, 0.2);
            transition: all 0.3s ease;
        }
        .btn-reset:disabled {
            background: #ccc;
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Password Strength Styles from reset_password.blade.php */
        .password-strength {
            margin-top: 8px;
            margin-bottom: 4px;
            height: 6px;
            width: 100%;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width 0.3s, background 0.3s;
        }
        .password-strength-text {
            font-size: 0.75rem;
            margin-top: 2px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .password-suggestion {
            font-size: 0.7rem;
            color: #dc3545;
            margin-top: 4px;
            font-weight: 600;
        }
        .password-match-indicator {
            font-size: 0.75rem;
            margin-top: 4px;
            font-weight: 700;
            min-height: 18px;
        }
        .password-match-indicator.match {
            color: #28a745;
        }
        .password-match-indicator.mismatch {
            color: #dc3545;
        }
        #passwordRequirements ul {
            padding-left: 15px;
            margin-top: 8px;
            list-style-type: none;
        }
        #passwordRequirements li {
            font-size: 0.7rem;
            margin-bottom: 2px;
            font-weight: 600;
        }
        #passwordRequirements li i {
            margin-right: 5px;
        }
        .text-success { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }
    </style>
</head>
<body>
    <div class="reset-card">
        <div class="logo-container">
            <div class="icon-box">
                <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            </div>
            <h3>New Password</h3>
            <p>Please enter your new secure password below.</p>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: "{{ session('success') }}",
                        confirmButtonColor: '#4c6ef5'
                    });
                @endif

                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#4c6ef5'
                    });
                @endif
            });
        </script>

        <form id="magicResetForm" method="POST" action="{{ route('magic.reset.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" id="new_password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Min. 8 characters">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('new_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="password-strength-text" id="passwordStrengthText"></div>
                </div>
                <div class="password-suggestion" id="passwordSuggestion" style="display:none;">Use a stronger password to continue.</div>
                
                <div id="passwordRequirements" class="mt-2" style="display:none;">
                    <ul class="mb-0">
                        <li id="req-length" class="text-danger"><i class="fas fa-circle"></i> At least 8 characters</li>
                        <li id="req-upper" class="text-danger"><i class="fas fa-circle"></i> At least 1 uppercase letter</li>
                        <li id="req-lower" class="text-danger"><i class="fas fa-circle"></i> At least 1 lowercase letter</li>
                        <li id="req-number" class="text-danger"><i class="fas fa-circle"></i> At least 1 number</li>
                        <li id="req-symbol" class="text-danger"><i class="fas fa-circle"></i> At least 1 symbol</li>
                    </ul>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                    <input type="password" id="confirm_password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirm_password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div id="passwordMatchIndicator" class="password-match-indicator"></div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-reset" disabled>
                Update Password
            </button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('new_password');
            const confirmInput = document.getElementById('confirm_password');
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            const suggestion = document.getElementById('passwordSuggestion');
            const matchIndicator = document.getElementById('passwordMatchIndicator');
            const reqBox = document.getElementById('passwordRequirements');
            const submitBtn = document.getElementById('submitBtn');
            const reqs = {
                length: document.getElementById('req-length'),
                upper: document.getElementById('req-upper'),
                lower: document.getElementById('req-lower'),
                number: document.getElementById('req-number'),
                symbol: document.getElementById('req-symbol')
            };

            function checkPasswordStrength(pw) {
                let score = 0;
                if (pw.length >= 8) score++;
                if (/[A-Z]/.test(pw)) score++;
                if (/[a-z]/.test(pw)) score++;
                if (/[0-9]/.test(pw)) score++;
                if (/[^A-Za-z0-9]/.test(pw)) score++;
                if (pw.length >= 12) score++;
                return score;
            }

            function updateRequirements(pw) {
                const checks = {
                    length: pw.length >= 8,
                    upper: /[A-Z]/.test(pw),
                    lower: /[a-z]/.test(pw),
                    number: /[0-9]/.test(pw),
                    symbol: /[^A-Za-z0-9]/.test(pw)
                };
                
                Object.entries(checks).forEach(([key, ok]) => {
                    if (reqs[key]) {
                        reqs[key].classList.toggle('text-success', ok);
                        reqs[key].classList.toggle('text-danger', !ok);
                        const icon = reqs[key].querySelector('i');
                        if (icon) {
                            icon.className = ok ? 'fas fa-check-circle' : 'fas fa-circle';
                        }
                    }
                });
            }

            function validateForm() {
                const pw = passwordInput.value;
                const confirm = confirmInput.value;
                const score = checkPasswordStrength(pw);
                const isMatch = pw === confirm && confirm !== '';
                
                // Allow submit only if strength is Strong (score >= 5) and passwords match
                if (score >= 5 && isMatch) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }

            function updateStrengthMeter() {
                const pw = passwordInput.value;
                const score = checkPasswordStrength(pw);
                let width = '0%';
                let color = '#e9ecef';
                let text = '';
                
                if (!pw) {
                    strengthBar.style.width = '0%';
                    strengthText.textContent = '';
                    suggestion.style.display = 'none';
                    return;
                }

                if (score <= 2) {
                    width = '33%';
                    color = '#dc3545';
                    text = 'Weak';
                    suggestion.style.display = '';
                } else if (score <= 4) {
                    width = '66%';
                    color = '#ffc107';
                    text = 'Medium';
                    suggestion.style.display = '';
                } else {
                    width = '100%';
                    color = '#28a745';
                    text = 'Strong';
                    suggestion.style.display = 'none';
                }

                strengthBar.style.width = width;
                strengthBar.style.background = color;
                strengthText.textContent = text;
                strengthText.style.color = color;
            }

            function updatePasswordMatch() {
                const pw = passwordInput.value;
                const confirm = confirmInput.value;
                
                if (!confirm) {
                    matchIndicator.textContent = '';
                    matchIndicator.className = 'password-match-indicator';
                    return;
                }

                if (pw === confirm) {
                    matchIndicator.textContent = 'Passwords match';
                    matchIndicator.className = 'password-match-indicator match';
                } else {
                    matchIndicator.textContent = 'Passwords do not match';
                    matchIndicator.className = 'password-match-indicator mismatch';
                }
            }

            passwordInput.addEventListener('focus', () => {
                reqBox.style.display = 'block';
            });

            passwordInput.addEventListener('input', () => {
                updateStrengthMeter();
                updateRequirements(passwordInput.value);
                updatePasswordMatch();
                validateForm();
            });

            confirmInput.addEventListener('input', () => {
                updatePasswordMatch();
                validateForm();
            });
        });
    </script>
</body>
</html>
