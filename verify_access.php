<?php
/**
 * Verify Access Token dan Create Session
 */

require_once 'config.php';

session_start();

// LOG untuk debugging
$log_file = __DIR__ . '/verify_log.txt';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Request received\n", FILE_APPEND);

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    file_put_contents($log_file, "Token kosong!\n", FILE_APPEND);
    showError('Token tidak valid');
    exit;
}

file_put_contents($log_file, "Token: " . substr($token, 0, 30) . "...\n", FILE_APPEND);

try {
    // Cek token di database
    $current_time = date('Y-m-d H:i:s');
    file_put_contents($log_file, "Current time: $current_time\n", FILE_APPEND);
    
    $stmt = $conn->prepare("
        SELECT * FROM auth_tokens 
        WHERE token = :token 
        AND is_used = 0 
        AND expires_at > :current_time
    ");
    $stmt->execute([
        ':token' => $token,
        ':current_time' => $current_time
    ]);
    $auth_token = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$auth_token) {
        // Cek kenapa gagal - cek token tanpa kondisi
        $stmt2 = $conn->prepare("SELECT * FROM auth_tokens WHERE token = :token");
        $stmt2->execute([':token' => $token]);
        $check = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if (!$check) {
            file_put_contents($log_file, "Token tidak ditemukan di database\n", FILE_APPEND);
        } else {
            file_put_contents($log_file, "Token ditemukan tapi: is_used=" . $check['is_used'] . ", expires_at=" . $check['expires_at'] . "\n", FILE_APPEND);
        }
        
        showError('Link tidak valid atau sudah kadaluarsa');
        exit;
    }
    
    file_put_contents($log_file, "Token VALID! Redirecting...\n", FILE_APPEND);
    
    // Cek apakah sudah ada session untuk token ini (dalam 10 detik terakhir)
    $stmt_check = $conn->prepare("
        SELECT * FROM sessions 
        WHERE telegram_chat_id = :chat_id 
        AND expires_at > :current_time
        ORDER BY id DESC LIMIT 1
    ");
    $stmt_check->execute([
        ':chat_id' => $auth_token['telegram_chat_id'],
        ':current_time' => $current_time
    ]);
    $existing_session = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_session) {
        // Session sudah ada, gunakan yang existing
        file_put_contents($log_file, "Using existing session\n", FILE_APPEND);
        $_SESSION['session_id'] = $existing_session['session_id'];
        $_SESSION['telegram_chat_id'] = $existing_session['telegram_chat_id'];
        $_SESSION['telegram_username'] = $existing_session['telegram_username'];
        $_SESSION['authenticated'] = true;
        
        file_put_contents($log_file, "Redirecting to form with existing session\n", FILE_APPEND);
        header('Location: index_form.php');
        exit;
    }
    
    // Token valid, tandai sebagai sudah digunakan
    $stmt = $conn->prepare("UPDATE auth_tokens SET is_used = 1 WHERE id = :id");
    $stmt->execute([':id' => $auth_token['id']]);
    
    // Buat session
    $session_id = bin2hex(random_bytes(32));
    $session_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    $stmt = $conn->prepare("
        INSERT INTO sessions (session_id, telegram_chat_id, telegram_username, expires_at)
        VALUES (:session_id, :chat_id, :username, :expires)
    ");
    
    $stmt->execute([
        ':session_id' => $session_id,
        ':chat_id' => $auth_token['telegram_chat_id'],
        ':username' => $auth_token['telegram_username'],
        ':expires' => $session_expires
    ]);
    
    // Set session
    $_SESSION['session_id'] = $session_id;
    $_SESSION['telegram_chat_id'] = $auth_token['telegram_chat_id'];
    $_SESSION['telegram_username'] = $auth_token['telegram_username'];
    $_SESSION['authenticated'] = true;
    
    file_put_contents($log_file, "Session created, redirecting to form\n", FILE_APPEND);
    
    // Langsung redirect ke form pendaftaran
    header('Location: index_form.php');
    exit;
    
} catch (Exception $e) {
    file_put_contents($log_file, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    showError('Terjadi kesalahan: ' . $e->getMessage());
}

function showSuccess($username) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verifikasi Berhasil</title>
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
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .container {
                background: white;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                padding: 40px;
                max-width: 500px;
                width: 100%;
                text-align: center;
            }

            .success-icon {
                font-size: 60px;
                margin-bottom: 20px;
                animation: checkmark 0.5s ease-in-out;
            }

            @keyframes checkmark {
                0% { transform: scale(0); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }

            h2 {
                color: #333;
                margin-bottom: 15px;
            }

            .username {
                color: #667eea;
                font-weight: bold;
                font-size: 1.2em;
                margin: 10px 0;
            }

            p {
                color: #666;
                line-height: 1.6;
                margin-bottom: 25px;
            }

            .button {
                display: inline-block;
                padding: 15px 40px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
                font-size: 1.1em;
                transition: transform 0.2s;
                cursor: pointer;
                border: none;
            }

            .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .info {
                background: #f0f0f0;
                padding: 15px;
                border-radius: 6px;
                margin: 20px 0;
                font-size: 0.9em;
                color: #555;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-icon">✅</div>
            <h2>Verifikasi Berhasil!</h2>
            <div class="username">@<?php echo htmlspecialchars($username ? $username : 'User'); ?></div>
            <p>Identitas Telegram Anda telah terverifikasi dengan sukses.</p>
            
            <div class="info">
                ✓ Akun Terverifikasi<br>
                ✓ Session Aktif 24 Jam<br>
                ✓ Siap Mengisi Formulir
            </div>

            <a href="index_form.php" class="button">Lanjut ke Form Pendaftaran →</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

function showError($message) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Verifikasi Akses</title>
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
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .container {
                background: white;
                border-radius: 10px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                padding: 40px;
                max-width: 500px;
                width: 100%;
                text-align: center;
            }

            .error-icon {
                font-size: 60px;
                margin-bottom: 20px;
            }

            h2 {
                color: #333;
                margin-bottom: 15px;
            }

            p {
                color: #666;
                line-height: 1.6;
                margin-bottom: 25px;
            }

            .button {
                display: inline-block;
                padding: 12px 30px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 600;
                transition: transform 0.2s;
            }

            .button:hover {
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error-icon">❌</div>
            <h2>Verifikasi Gagal</h2>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="request_access.php" class="button">Minta Link Baru</a>
        </div>
    </body>
    </html>
    <?php
}
?>
