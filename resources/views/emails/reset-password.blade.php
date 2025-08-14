<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Presensi Siswa</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        
        .email-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #667eea;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .logo i {
            color: white;
            font-size: 1.5rem;
        }
        
        .title {
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .subtitle {
            color: #718096;
            font-size: 0.9rem;
            margin: 5px 0 0 0;
        }
        
        .content {
            margin-bottom: 30px;
        }
        
        .greeting {
            font-size: 1.1rem;
            margin-bottom: 20px;
            color: #2d3748;
        }
        
        .message {
            margin-bottom: 25px;
            color: #4a5568;
            line-height: 1.7;
        }
        
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .warning {
            background: #fef5e7;
            border: 1px solid #fbd38d;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #744210;
        }
        
        .warning strong {
            color: #d69e2e;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 0.9rem;
        }
        
        .contact-info {
            background: #f7fafc;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .contact-info strong {
            color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1 class="title">Sistem Presensi Siswa</h1>
            <p class="subtitle">Reset Password</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo <strong>{{ $user->name }}</strong>,
            </div>
            
            <div class="message">
                Kami menerima permintaan untuk reset password akun Anda di Sistem Presensi Siswa. 
                Jika Anda tidak melakukan permintaan ini, Anda dapat mengabaikan email ini.
            </div>
            
            <div class="message">
                Untuk melanjutkan proses reset password, silakan klik tombol di bawah ini:
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">
                    <i class="fas fa-key me-2"></i>Reset Password
                </a>
            </div>
            
            <div class="warning">
                <strong>⚠️ Penting:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <li>Link ini hanya berlaku selama 24 jam</li>
                    <li>Jangan bagikan link ini kepada siapapun</li>
                    <li>Jika link tidak berfungsi, silakan request ulang</li>
                </ul>
            </div>
            
            <div class="message">
                Jika tombol di atas tidak berfungsi, Anda dapat copy dan paste link berikut ke browser Anda:
            </div>
            
            <div style="background: #f7fafc; padding: 15px; border-radius: 8px; word-break: break-all; font-family: monospace; font-size: 0.9rem; color: #4a5568;">
                {{ $resetUrl }}
            </div>
        </div>
        
        <div class="contact-info">
            <strong>Butuh bantuan?</strong><br>
            Jika Anda mengalami masalah, silakan hubungi administrator sistem.
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh Sistem Presensi Siswa</p>
            <p>© {{ date('Y') }} Sistem Presensi Siswa. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 