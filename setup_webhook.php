<?php
// File untuk setup webhook Telegram Bot
// Jalankan file ini sekali untuk mengaktifkan bot

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// URL webhook (ganti dengan URL ngrok atau domain Anda)
// Contoh: https://your-ngrok-url.ngrok.io/telegram_bot.php
$webhook_url = isset($_GET['url']) ? $_GET['url'] : '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Telegram Bot</title>
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
            max-width: 600px;
            width: 100%;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
        .warning-box {
            background: #fff3e0;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #ff9800;
        }
        .success-box {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
        }
        .error-box {
            background: #ffebee;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #f44336;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        ol, ul {
            margin-left: 20px;
            line-height: 1.8;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #28a745;
        }
        .btn-secondary:hover {
            background: #218838;
        }
        p {
            color: #555;
            line-height: 1.6;
            margin: 10px 0;
        }
        h3 {
            color: #333;
            margin: 20px 0 10px 0;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🤖 Setup Telegram Bot</h2>
        
        <?php if (!$webhook_url): ?>
        
        <div class="info-box">
            <strong>ℹ️ Token Bot:</strong><br>
            <code><?php echo BOT_TOKEN; ?></code>
        </div>
        
        <div class="warning-box">
            <strong>⚠️ Penting:</strong><br>
            Untuk menggunakan bot Telegram, Anda memerlukan URL publik (https). Ada 2 cara:
        </div>
        
        <h3>📋 Cara 1: Menggunakan ngrok (Recommended untuk Testing)</h3>
        <ol>
            <li>Download ngrok dari <a href="https://ngrok.com/download" target="_blank">https://ngrok.com/download</a></li>
            <li>Jalankan server PHP Anda (sudah berjalan di localhost:8000)</li>
            <li>Buka terminal/cmd baru dan jalankan:<br>
                <code>ngrok http 8000</code>
            </li>
            <li>Ngrok akan memberikan URL seperti:<br>
                <code>https://xxxx-xxx-xxx-xxx.ngrok-free.app</code>
            </li>
            <li>Copy URL tersebut dan tambahkan <code>/telegram_bot.php</code></li>
            <li>Paste URL lengkap di form di bawah</li>
        </ol>
        
        <h3>📋 Cara 2: Deploy ke Hosting (Untuk Production)</h3>
        <ol>
            <li>Upload semua file ke hosting yang support PHP</li>
            <li>Pastikan hosting memiliki SSL (https://)</li>
            <li>Gunakan URL: <code>https://domain-anda.com/telegram_bot.php</code></li>
        </ol>
        
        <form method="GET" action="">
            <div class="form-group">
                <label>Webhook URL:</label>
                <input type="text" name="url" placeholder="https://your-ngrok-url.ngrok.io/telegram_bot.php" required>
            </div>
            <button type="submit">Set Webhook</button>
        </form>
        
        <div class="info-box" style="margin-top: 20px;">
            <strong>💡 Cara Mudah Test Bot (Tanpa Webhook):</strong><br>
            Buka Telegram dan cari bot Anda, lalu gunakan perintah manual untuk testing tanpa perlu setup webhook terlebih dahulu.
        </div>
        
        <?php else: ?>
        
        <?php
        // Set webhook
        $api_url = API_URL . 'setWebhook?url=' . urlencode($webhook_url);
        $response = file_get_contents($api_url);
        $result = json_decode($response, true);
        
        if ($result['ok']): ?>
            <div class="success-box">
                <strong>✅ Webhook berhasil diset!</strong><br>
                URL: <code><?php echo $webhook_url; ?></code>
            </div>
            
            <div class="info-box">
                <strong>📱 Cara Menggunakan Bot:</strong>
                <ol>
                    <li>Buka Telegram di HP atau Web</li>
                    <li>Cari username bot Anda atau buka link yang diberikan oleh BotFather</li>
                    <li>Klik tombol <strong>START</strong></li>
                    <li>Bot akan menampilkan menu utama</li>
                    <li>Klik <strong>"📝 Daftar Sekarang"</strong> untuk mendapatkan link form</li>
                </ol>
            </div>
            
            <div class="success-box">
                <strong>🎉 Bot Siap Digunakan!</strong><br>
                Perintah yang tersedia:
                <ul>
                    <li><code>/start</code> - Menu utama</li>
                    <li><code>/daftar</code> - Link form pendaftaran</li>
                    <li><code>/data</code> - Lihat data mahasiswa</li>
                    <li><code>/statistik</code> - Statistik pendaftaran</li>
                    <li><code>/info</code> - Informasi bot</li>
                </ul>
            </div>
            
            <button onclick="window.location.href='setup_webhook.php'" class="btn-secondary">
                🔄 Update Webhook
            </button>
            
        <?php else: ?>
            <div class="error-box">
                <strong>❌ Gagal set webhook!</strong><br>
                Error: <?php echo isset($result['description']) ? $result['description'] : 'Unknown error'; ?>
            </div>
            <button onclick="window.location.href='setup_webhook.php'">
                🔄 Coba Lagi
            </button>
        <?php endif; ?>
        
        <?php endif; ?>
        
        <button onclick="window.location.href='index.html'" style="background: #6c757d; margin-top: 10px;">
            🏠 Kembali ke Home
        </button>
    </div>
</body>
</html>
