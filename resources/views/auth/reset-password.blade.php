<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Presensi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reset-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .reset-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #667eea);
            background-size: 200% 100%;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .reset-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .icon-container {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .icon-container i {
            font-size: 2rem;
            color: white;
        }

        .reset-header h1 {
            color: #2d3748;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .reset-header p {
            color: #718096;
            font-size: 0.95rem;
            margin: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .input-group .toggle-password {
            position: absolute;
            right: 15px;
            left: auto;
            cursor: pointer;
            color: #a0aec0;
            transition: color 0.3s ease;
        }

        .input-group .toggle-password:hover {
            color: #667eea;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            background: #f7fafc;
            transition: all 0.3s ease;
            color: #2d3748;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control:focus + i {
            color: #667eea;
        }

        .btn-reset {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .btn-reset::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-reset:hover::before {
            left: 100%;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border-left: 4px solid #e53e3e;
        }

        .info-box {
            background: linear-gradient(135deg, #fef5e7, #fbd38d);
            border: none;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #744210;
        }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: #718096;
        }

        .strength-weak { color: #e53e3e; }
        .strength-medium { color: #d69e2e; }
        .strength-strong { color: #38a169; }

        @media (max-width: 480px) {
            .reset-container {
                padding: 2rem;
                margin: 10px;
            }
            
            .reset-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <div class="icon-container">
                <i class="fas fa-lock"></i>
            </div>
            <h1>Reset Password</h1>
            <p>Masukkan password baru Anda</p>
        </div>

        @if($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <div class="info-box">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Password harus minimal 8 karakter</strong>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label for="password">Password Baru</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" 
                           class="form-control" required placeholder="Masukkan password baru">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
                </div>
                <div class="password-strength" id="password-strength"></div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="form-control" required placeholder="Konfirmasi password baru">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password_confirmation')"></i>
                </div>
            </div>

            <button type="submit" class="btn-reset">
                <i class="fas fa-save me-2"></i>Simpan Password Baru
            </button>
        </form>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');
            
            let strength = 0;
            let message = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            if (strength < 3) {
                message = 'Password lemah';
                strengthDiv.className = 'password-strength strength-weak';
            } else if (strength < 4) {
                message = 'Password sedang';
                strengthDiv.className = 'password-strength strength-medium';
            } else {
                message = 'Password kuat';
                strengthDiv.className = 'password-strength strength-strong';
            }
            
            strengthDiv.textContent = message;
        });

        // Auto focus on password input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('password').focus();
        });
    </script>
</body>
</html> 