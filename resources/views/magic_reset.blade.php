<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Reset Password - MCC IPES</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            max-width: 360px;
            padding: 30px 25px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 22px;
        }
        .logo-container .icon-box {
            width: 65px;
            height: 65px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
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
            margin-bottom: 15px;
        }
        .input-group:focus-within {
            border-color: #4c6ef5;
            box-shadow: 0 0 0 4px rgba(76, 110, 245, 0.1);
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
            margin-top: 10px;
            box-shadow: 0 8px 20px rgba(208, 0, 111, 0.2);
        }
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

        <form method="POST" action="{{ route('magic.reset.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Min. 8 characters">
                </div>
                @error('password')
                    <div class="text-danger small fw-bold mb-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check-double"></i></span>
                    <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                </div>
            </div>

            <button type="submit" class="btn btn-reset">
                Update Password
            </button>
        </form>
    </div>
</body>
</html>
