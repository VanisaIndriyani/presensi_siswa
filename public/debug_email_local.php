<?php
// Debug file untuk masalah email reset password di LOCAL environment
header('Content-Type: text/html; charset=utf-8');

// Load Laravel environment
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test database connection
try {
    $pdo = new PDO(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
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
    'MAIL_MAILER' => env('MAIL_MAILER'),
    'MAIL_HOST' => env('MAIL_HOST'),
    'MAIL_PORT' => env('MAIL_PORT'),
    'MAIL_USERNAME' => env('MAIL_USERNAME'),
    'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
    'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
    'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
    'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
    'APP_URL' => env('APP_URL')
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
if (env('MAIL_HOST') && env('MAIL_PORT')) {
    $connection = @fsockopen(env('MAIL_HOST'), env('MAIL_PORT'), $errno, $errstr, 10);
    if ($connection) {
        $smtpTest = '✅ Connected to ' . env('MAIL_HOST') . ':' . env('MAIL_PORT');
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
        $resetUrl = env('APP_URL') . '/reset-password?token=' . $token;
        
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

// Check if .env file exists and readable
$envFileStatus = 'Not checked';
$envFileContent = '';
if (file_exists(__DIR__ . '/../.env')) {
    $envFileStatus = '✅ .env file exists';
    $envFileContent = file_get_contents(__DIR__ . '/../.env');
} else {
    $envFileStatus = '❌ .env file not found';
}

// Check Laravel logs
$logFile = __DIR__ . '/../storage/logs/laravel.log';
$logStatus = 'Not checked';
$logContent = '';
if (file_exists($logFile)) {
    $logStatus = '✅ Laravel log exists';
    $logContent = file_get_contents($logFile);
    // Get last 20 lines
    $lines = explode("\n", $logContent);
    $logContent = implode("\n", array_slice($lines, -20));
} else {
    $logStatus = '❌ Laravel log not found';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Email Local - Reset Password</title>
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
            max-height: 300px;
            overflow-y: auto;
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
        .quick-fix {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        .quick-fix h4 { margin-top: 0; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Debug Email Local - Reset Password</h1>
        <p><strong>Environment:</strong> Local Development (localhost)</p>
        <p><strong>Masalah:</strong> Email reset password tidak masuk ke Gmail di local environment</p>
        
        <div class="debug-section info">
            <h3>📋 Langkah Debug untuk Local Environment</h3>
            <ol>
                <li><strong>Periksa .env file</strong> - Pastikan konfigurasi email sudah benar</li>
                <li><strong>Test SMTP Connection</strong> - Pastikan bisa connect ke Gmail SMTP</li>
                <li><strong>Test Laravel Mail</strong> - Pastikan Laravel bisa mengirim email</li>
                <li><strong>Periksa Laravel Logs</strong> - Lihat error detail di storage/logs/laravel.log</li>
                <li><strong>Test dengan email Gmail</strong> - Pastikan App Password sudah benar</li>
            </ol>
        </div>

        <div class="debug-section <?= $envFileStatus === '✅ .env file exists' ? 'success' : 'error' ?>">
            <h3>📁 .env File Status</h3>
            <p><span class="status-badge <?= strpos($envFileStatus, '✅') !== false ? 'status-success' : 'status-error' ?>"><?= $envFileStatus ?></span></p>
            
            <?php if ($envFileContent): ?>
                <h4>Email-related variables in .env:</h4>
                <pre><?= htmlspecialchars($envFileContent) ?></pre>
            <?php endif; ?>
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
            <h3>⚙️ Environment Variables (from Laravel)</h3>
            <div class="env-vars">
                <?php foreach ($envVars as $key => $value): ?>
                    <div class="env-var">
                        <span class="env-key"><?= $key ?></span>
                        <span class="env-value <?= $value ? 'set' : 'not-set' ?>" title="<?= htmlspecialchars($value) ?>">
                            <?= $value ? (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value) : 'NOT SET' ?>
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

        <div class="debug-section <?= $logStatus === '✅ Laravel log exists' ? 'success' : 'error' ?>">
            <h3>📝 Laravel Logs (Last 20 lines)</h3>
            <p><span class="status-badge <?= strpos($logStatus, '✅') !== false ? 'status-success' : 'status-error' ?>"><?= $logStatus ?></span></p>
            
            <?php if ($logContent): ?>
                <h4>Recent Log Entries:</h4>
                <pre><?= htmlspecialchars($logContent) ?></pre>
            <?php endif; ?>
        </div>

        <div class="debug-section warning">
            <h3>⚠️ Kemungkinan Penyebab Email Tidak Masuk di Local</h3>
            <ul>
                <li><strong>Gmail App Password belum dibuat</strong> - Harus buat App Password, bukan password Gmail biasa</li>
                <li><strong>2FA Gmail belum aktif</strong> - App Password hanya tersedia jika 2FA aktif</li>
                <li><strong>Port SMTP diblokir</strong> - ISP atau firewall local memblokir port 587/465</li>
                <li><strong>Konfigurasi .env salah</strong> - Typo atau format yang salah</li>
                <li><strong>Laravel cache belum di-clear</strong> - Perlu jalankan `php artisan config:clear`</li>
                <li><strong>Email masuk spam</strong> - Cek folder spam di Gmail</li>
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

        <div class="quick-fix">
            <h4>🚀 Quick Fix untuk Local Environment</h4>
            <ol>
                <li><strong>Buat Gmail App Password:</strong>
                    <ul>
                        <li>Buka <a href="https://myaccount.google.com/" target="_blank">Google Account Settings</a></li>
                        <li>Security → 2-Step Verification → App passwords</li>
                        <li>Generate App Password untuk "Mail"</li>
                    </ul>
                </li>
                <li><strong>Update .env file:</strong>
                    <pre>MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem Presensi Siswa"</pre>
                </li>
                <li><strong>Clear Laravel cache:</strong>
                    <code>php artisan config:clear</code>
                </li>
                <li><strong>Restart server:</strong>
                    <code>php artisan serve</code>
                </li>
            </ol>
        </div>

        <div class="debug-section success">
            <h3>✅ Solusi yang Disarankan untuk Local</h3>
            <ol>
                <li><strong>Gunakan Gmail App Password</strong> - Jangan gunakan password Gmail biasa</li>
                <li><strong>Pastikan 2FA aktif</strong> - App Password hanya tersedia dengan 2FA</li>
                <li><strong>Test SMTP connection</strong> - Pastikan bisa connect ke Gmail</li>
                <li><strong>Clear Laravel cache</strong> - Setelah update .env</li>
                <li><strong>Restart development server</strong> - Setelah perubahan konfigurasi</li>
                <li><strong>Cek folder spam</strong> - Email mungkin masuk ke spam</li>
            </ol>
        </div>

        <div class="debug-section info">
            <h3>📚 Referensi Konfigurasi Gmail untuk Local</h3>
            <p><strong>Untuk Gmail dengan App Password (RECOMMENDED):</strong></p>
            <pre>
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop  # 16 karakter App Password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Sistem Presensi Siswa"
            </pre>
            
            <p><strong>Alternatif: Gunakan Mailtrap untuk testing (tidak perlu Gmail):</strong></p>
            <pre>
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
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
