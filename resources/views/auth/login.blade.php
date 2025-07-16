<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Presensi Siswa</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #333;
            margin: 0;
            font-size: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #5a6fd8;
        }
        .error-message {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        .demo-info {
            background: #f7fafc;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            font-size: 0.875rem;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:80px; height:80px; object-fit:contain; margin-bottom: 1rem; border-radius: 10px; box-shadow: 0 2px 8px rgba(102,126,234,0.15);">
            <h1 style="font-weight:700; letter-spacing:1px; color:#4f46e5;">Sistem Presensi Siswa</h1>
            <p style="color:#555; margin-top:0.5rem;">Silakan login untuk melanjutkan</p>
        </div>

        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan email...">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password...">
            </div>

            <button type="submit" class="btn-login" style="font-weight:600; font-size:1.1rem; letter-spacing:0.5px;">Login</button>
        </form>

       
    </div>
</body>
</html> 