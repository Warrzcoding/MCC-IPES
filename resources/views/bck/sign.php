<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Office Performance Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .signup-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            padding: 45px;
            max-width: 500px;
            width: 100%;
            transition: all 0.3s ease;
        }
        .signup-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.2);
        }
        .signup-header {
            text-align: center;
            margin-bottom: 35px;
        }
        .signup-header .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
        }
        .signup-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .signup-header p {
            color: #666;
            font-size: 14px;
        }
        .form-control, .form-select {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            margin-bottom: 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.3rem rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 25px;
            font-weight: 600;
            width: 100%;
            margin-bottom: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 15px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .signup-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group-text {
            background: #f8f9fa;
        }
        .alert {
            border-radius: 15px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease;
        }
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);
            color: white;
        }
        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #69db7c 100%);
            color: white;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .avatar-preview {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .avatar-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid #667eea;
        }
        .avatar-label {
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="signup-card">
        <div class="signup-header">
            <div class="logo">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Sign Up</h2>
            <p>Create your account for the Office Performance Evaluation System</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Please fix the errors below.
            </div>
        @endif

        <form method="POST" action="{{ route('signup.submit') }}" enctype="multipart/form-data">
            @csrf

            <div class="avatar-preview">
                <img id="avatarImg" src="{{ asset('default-avatar.png') }}" alt="Avatar" class="avatar-img">
                <div>
                    <label for="profile_image" class="avatar-label">
                        <i class="fas fa-image"></i> Profile Image (optional)
                    </label>
                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="previewAvatar(event)">
                </div>
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                       id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                @error('full_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user-tag"></i> Username
                </label>
                <input type="text" class="form-control @error('username') is-invalid @enderror"
                       id="username" name="username" value="{{ old('username') }}" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                       id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="school_id" class="form-label">
                    <i class="fas fa-id-card"></i> School ID
                </label>
                <input type="text" class="form-control @error('school_id') is-invalid @enderror"
                       id="school_id" name="school_id" value="{{ old('school_id') }}" required>
                @error('school_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="course" class="form-label">
                    <i class="fas fa-book"></i> Course
                </label>`
                <select class="form-select @error('course') is-invalid @enderror" id="course" name="course" required>
                    <option value="">Select course...</option>
                    <option value="BSIT" {{ old('course') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                    <option value="BSBA" {{ old('course') == 'BSBA' ? 'selected' : '' }}>BSBA</option>
                    <option value="BSHM" {{ old('course') == 'BSHM' ? 'selected' : '' }}>BSHM</option>
                    <option value="BSED" {{ old('course') == 'BSED' ? 'selected' : '' }}>BSED</option>
                    <option value="BEED" {{ old('course') == 'BEED' ? 'selected' : '' }}>BEED</option>
                </select>
                @error('course')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 d-none">
                <input type="hidden" name="role" value="student">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">
                    <i class="fas fa-lock"></i> Confirm Password
                </label>
                <input type="password" class="form-control"
                       id="password_confirmation" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Reset
            </button>
        </form>
        <div class="signup-link">
            <p>Already have an account? <a href="{{ route('login') }}">
                <i class="fas fa-sign-in-alt"></i> Login here
            </a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Registration Successful!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#667eea',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
</script>

        function previewAvatar(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('avatarImg').src = URL.createObjectURL(file);
            } else {
                document.getElementById('avatarImg').src = '{{ asset('default-avatar.png') }}';
            }
        }
    </script>
</body>
</html>