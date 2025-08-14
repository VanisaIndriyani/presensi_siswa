<?php
// Debug file untuk masalah email reset password
header('Content-Type: text/html; charset=utf-8');

// Test database connection
try {
    $pdo = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbStatus = '✅ Connected';
} catch (PDOException $e) {
    $dbStatus = '❌ Failed: ' . $e->getMessage();
}

// Test password_resets table
try {
    $stmt = $pdo->query("DESCRIBE password_resets");
    $tableStructure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tableStatus = '✅ Table exists';
} catch (PDOException $e) {
    $tableStatus = '❌ Table not found: ' . $e->getMessage();
    $tableStructure = [];
}

// Check environment variables
$envVars = [
    'MAIL_MAILER' => $_ENV['MAIL_MAILER'] ?? 'NOT SET',
    'MAIL_HOST' => $_ENV['MAIL_HOST'] ?? 'NOT SET',
    'MAIL_PORT' => $_ENV['MAIL_PORT'] ?? 'NOT SET',
    'MAIL_USERNAME' => $_ENV['MAIL_USERNAME'] ?? 'NOT SET',
    'MAIL_PASSWORD' => $_ENV['MAIL_PASSWORD'] ?? 'NOT SET',
    'MAIL_ENCRYPTION' => $_ENV['MAIL_ENCRYPTION'] ?? 'NOT SET',
    'MAIL_FROM_ADDRESS' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'NOT SET',
    'MAIL_FROM_NAME' => $_ENV['MAIL_FROM_NAME'] ?? 'NOT SET',
    'APP_URL' => $_ENV['APP_URL'] ?? 'NOT SET'
];

// Test Laravel mail configuration
$mailConfig = [
    'driver' => config('mail.default'),
    'host' => config('mail.mailers.smtp.host'),
    'port' => config('mail.mailers.smtp.port'),
    'username' => config('mail.mailers.smtp.username'),
    'encryption' => config('mail.mailers.smtp.encryption'),
    'from_address' => config('mail.from.address'),
    'from_name' => config('mail.from.name')
];

// Test SMTP connection
$smtpTest = 'Not tested';
if ($_ENV['MAIL_HOST'] && $_ENV['MAIL_PORT']) {
    $connection = @fsockopen($_ENV['MAIL_HOST'], $_ENV['MAIL_PORT'], $errno, $errstr, 10);
    if ($connection) {
        $smtpTest = '✅ Connected to ' . $_ENV['MAIL_HOST'] . ':' . $_ENV['MAIL_PORT'];
        fclose($connection);
    } else {
        $smtpTest = '❌ Failed to connect: ' . $errstr . ' (Error: ' . $errno . ')';
    }
}

// Test manual password reset
$testResult = '';
if ($_POST['test_email']) {
    try {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $email = $_POST['test_email'];
        
        // Insert into password_resets table
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$email, $token]);
        
        // Test Laravel Mail
        $resetUrl = $_ENV['APP_URL'] . '/reset-password?token=' . $token;
        
        // Try to send email using Laravel Mail
        try {
            Mail::send('emails.reset-password', ['resetUrl' => $resetUrl], function($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password - Sistem Presensi Siswa');
            });
            $testResult = '✅ Email sent successfully via Laravel Mail';
        } catch (Exception $e) {
            $testResult = '❌ Laravel Mail failed: ' . $e->getMessage();
        }
        
    } catch (Exception $e) {
        $testResult = '❌ Test failed: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Email Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .debug-section { 
            margin: 20px 0; 
            padding: 20px; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .warning { border-left: 4px solid #ffc107; }
        .info { border-left: 4px solid #17a2b8; }
        .env-var { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0; 
            border-bottom: 1px solid #eee;
        }
        .env-var:last-child { border-bottom: none; }
        .env-key { font-weight: bold; color: #495057; }
        .env-value { 
            font-family: monospace; 
            background: #f8f9fa; 
            padding: 2px 6px; 
            border-radius: 4px;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .env-value.not-set { color: #dc3545; }
        .env-value.set { color: #28a745; }
        pre { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 5px; 
            overflow-x: auto;
            border: 1px solid #dee2e6;
        }
        .test-form {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .test-form input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            margin: 10px 0;
        }
        .test-form button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .test-form button:hover { background: #0056b3; }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Debug Email Reset Password</h1>
        <p><strong>Masalah:</strong> Email reset password tidak masuk ke Gmail</p>
        
        <div class="debug-section info">
            <h3>📋 Langkah Debug yang Harus Dilakukan</h3>
            <ol>
                <li><strong>Periksa Environment Variables</strong> - Pastikan semua konfigurasi email sudah benar</li>
                <li><strong>Test SMTP Connection</strong> - Pastikan server email bisa diakses</li>
                <li><strong>Test Laravel Mail</strong> - Pastikan Laravel bisa mengirim email</li>
                <li><strong>Periksa Spam Folder</strong> - Email mungkin masuk ke spam</li>
                <li><strong>Periksa Server Logs</strong> - Lihat error log hosting</li>
            </ol>
        </div>

        <div class="debug-section <?= $dbStatus === '✅ Connected' ? 'success' : 'error' ?>">
            <h3>🗄️ Database Connection</h3>
            <p><span class="status-badge <?= strpos($dbStatus, '✅') !== false ? 'status-success' : 'status-error' ?>"><?= $dbStatus ?></span></p>
        </div>

        <div class="debug-section <?= $tableStatus === '✅ Table exists' ? 'success' : 'error' ?>">
            <h3>📊 Password Resets Table</h3>
            <p><span class="status-badge <?= strpos($tableStatus, '✅') !== false ? 'status-success' : 'status-error' ?>"><?= $tableStatus ?></span></p>
            
            <?php if (!empty($tableStructure)): ?>
                <h4>Table Structure:</h4>
                <pre><?= json_encode($tableStructure, JSON_PRETTY_PRINT) ?></pre>
            <?php endif; ?>
        </div>

        <div class="debug-section info">
            <h3>⚙️ Environment Variables</h3>
            <div class="env-vars">
                <?php foreach ($envVars as $key => $value): ?>
                    <div class="env-var">
                        <span class="env-key"><?= $key ?></span>
                        <span class="env-value <?= $value === 'NOT SET' ? 'not-set' : 'set' ?>" title="<?= $value ?>">
                            <?= $value === 'NOT SET' ? 'NOT SET' : (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="debug-section info">
            <h3>📧 Laravel Mail Configuration</h3>
            <pre><?= json_encode($mailConfig, JSON_PRETTY_PRINT) ?></pre>
        </div>

        <div class="debug-section <?= strpos($smtpTest, '✅') !== false ? 'success' : 'error' ?>">
            <h3>🔌 SMTP Connection Test</h3>
            <p><span class="status-badge <?= strpos($smtpTest, '✅') !== false ? 'status-success' : 'status-error' ?>"><?= $smtpTest ?></span></p>
        </div>

        <div class="debug-section warning">
            <h3>⚠️ Kemungkinan Penyebab Email Tidak Masuk</h3>
            <ul>
                <li><strong>SMTP credentials salah</strong> - Username/password email tidak valid</li>
                <li><strong>Port SMTP salah</strong> - Port 587 untuk TLS, 465 untuk SSL, 25 untuk non-encrypted</li>
                <li><strong>Encryption setting salah</strong> - TLS, SSL, atau null</li>
                <li><strong>Email masuk spam</strong> - Cek folder spam di Gmail</li>
                <li><strong>Hosting memblokir SMTP</strong> - Beberapa hosting memblokir port SMTP</li>
                <li><strong>Rate limiting</strong> - Gmail membatasi jumlah email yang dikirim</li>
            </ul>
        </div>

        <div class="debug-section">
            <h3>🧪 Test Manual Password Reset</h3>
            <div class="test-form">
                <form method="POST">
                    <label for="test_email"><strong>Email untuk test:</strong></label>
                    <input type="email" id="test_email" name="test_email" placeholder="Masukkan email Gmail Anda" required>
                    <button type="submit">Test Kirim Email</button>
                </form>
                
                <?php if ($testResult): ?>
                    <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                        <strong>Hasil Test:</strong> <?= $testResult ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="debug-section success">
            <h3>✅ Solusi yang Disarankan</h3>
            <ol>
                <li><strong>Periksa Gmail Spam Folder</strong> - Email mungkin masuk ke spam</li>
                <li><strong>Gunakan Gmail App Password</strong> - Jangan gunakan password Gmail biasa</li>
                <li><strong>Test dengan email lain</strong> - Coba dengan email non-Gmail</li>
                <li><strong>Periksa hosting SMTP policy</strong> - Beberapa hosting memblokir SMTP</li>
                <li><strong>Gunakan service email eksternal</strong> - Mailgun, SendGrid, dll</li>
            </ol>
        </div>

        <div class="debug-section info">
            <h3>📚 Referensi Konfigurasi Gmail</h3>
            <p><strong>Untuk Gmail dengan App Password:</strong></p>
            <pre>
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem Presensi Siswa"
            </pre>
            
            <p><strong>Untuk Gmail dengan SSL:</strong></p>
            <pre>
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem Presensi Siswa"
            </pre>
        </div>
    </div>

    <script>
        // Add copy functionality for environment variables
        document.querySelectorAll('.env-value').forEach(el => {
            if (el.textContent !== 'NOT SET') {
                el.style.cursor = 'pointer';
                el.title = 'Click to copy: ' + el.getAttribute('title');
                el.addEventListener('click', function() {
                    navigator.clipboard.writeText(this.getAttribute('title'));
                    this.style.background = '#28a745';
                    this.style.color = 'white';
                    setTimeout(() => {
                        this.style.background = '#f8f9fa';
                        this.style.color = '#495057';
                    }, 1000);
                });
            }
        });
    </script>
</body>
</html>
