<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Forgot Password - MCC IPES</title>
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
        .forgot-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 340px;
            padding: 30px 25px;
            transition: all 0.3s ease;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 22px;
        }
        .logo-container .icon-box {
            width: 80px;
            height: 80px;
            background: radial-gradient(circle at 30% 20%, #ffffff 0%, #f3f5ff 45%, #e3e7ff 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
        }
        .logo-container img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .forgot-card h3 {
            color: #1a1a1a;
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .forgot-card p {
            color: #666;
            font-size: 0.8rem;
            line-height: 1.4;
            margin-bottom: 0;
        }
        .form-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #444;
            font-weight: 700;
            margin-bottom: 10px;
            display: block;
        }
        .input-group {
            background: #fdfdfd;
            border-radius: 14px;
            overflow: hidden;
            border: 2px solid #eee;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .input-group:focus-within {
            border-color: #4c6ef5;
            box-shadow: 0 0 0 4px rgba(76, 110, 245, 0.1);
            transform: translateY(-1px);
        }
        .input-group-text {
            background: transparent;
            border: none;
            padding-left: 15px;
            color: #4c6ef5;
            font-size: 0.9rem;
        }
        .form-control {
            background: transparent;
            border: none;
            padding: 11px 15px;
            font-size: 0.85rem;
            color: #333;
            font-weight: 500;
        }
        .form-control:focus {
            background: transparent;
            box-shadow: none;
            outline: none;
        }
        .btn-magic {
            background: linear-gradient(135deg, #4c6ef5 0%, #d0006f 100%);
            color: white !important;
            border: none;
            border-radius: 14px;
            padding: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(208, 0, 111, 0.2);
            margin-top: 5px;
        }
        .btn-magic:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(208, 0, 111, 0.3);
            filter: brightness(1.05);
        }
        .btn-magic:active {
            transform: translateY(0);
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #888;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-top: 15px;
        }
        .back-link:hover {
            color: #4c6ef5;
        }
        .back-link i {
            font-size: 0.7rem;
            transition: transform 0.2s ease;
        }
        .back-link:hover i {
            transform: translateX(-3px);
        }
        .invalid-feedback {
            font-size: 0.7rem;
            font-weight: 600;
            padding-left: 5px;
        }
    </style>
</head>
<body>
    <div class="forgot-card">
        <div class="logo-container">
            <div class="icon-box">
                <img src="{{ asset('images/logo.png') }}" alt="MCC Logo">
            </div>
            <h3>Reset Password</h3>
            <p>Enter your ms email to receive a secure reset link.</p>
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
                    let title = 'Error';
                    let text = "{{ session('error') }}";
                    
                    if (text.includes('daily limit')) {
                        title = 'Daily Limit Reached';
                    } else if (text.includes('wait')) {
                        title = 'Too Many Requests';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: title,
                        text: text,
                        confirmButtonColor: '#4c6ef5'
                    });
                @endif

                const emailInput = document.getElementById('email');
                if (emailInput) {
                    emailInput.addEventListener('input', function(e) {
                        let value = this.value;
                        
                        // Accept only letters, numbers, dot, at, and ñ/Ñ
                        // Using a regex to strip invalid characters
                        const cleanValue = value.replace(/[^a-zA-Z0-9.@ñÑ]/g, '');
                        
                        if (value !== cleanValue) {
                            this.value = cleanValue;
                            value = cleanValue;
                        }

                        // Auto-complete @mcclawis.edu.ph
                        if (value.includes('@')) {
                            const parts = value.split('@');
                            if (parts.length > 1) {
                                // If user just typed '@' or started typing after '@'
                                // but we want to force the domain
                                this.value = parts[0] + '@mcclawis.edu.ph';
                            }
                        }
                    });

                    // Prevent spaces
                    emailInput.addEventListener('keydown', function(e) {
                        if (e.key === ' ') {
                            e.preventDefault();
                        }
                    });
                }
            });
        </script>

        <form method="POST" action="{{ route('magic.link.send') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="form-label">MS Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                           name="email" value="{{ old('email') }}" required autocomplete="email" 
                           autofocus placeholder="your.msemail@mcclawis.edu.ph">
                </div>
                @error('email')
                    <span class="invalid-feedback d-block mt-2" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-magic">
                    <i class="fas fa-paper-plane me-2"></i> Send Link
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
      @stack('scripts')
    @include('partials.chatbot')
</body>
</html>
