<?php
// File untuk test bot tanpa webhook
// Simulasi mendapatkan link form dari bot

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// Fungsi untuk mengirim pesan
function sendMessage($chat_id, $text) {
    $url = API_URL . "sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

// Dapatkan update terbaru
$updates_url = API_URL . 'getUpdates';
$updates = file_get_contents($updates_url);
$updates_data = json_decode($updates, true);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Telegram Bot</title>
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
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #2196f3;
        }
        .success-box {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #4caf50;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 10px 0;
        }
        button {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        input {
            width: 300px;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            margin-right: 10px;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        ol {
            margin-left: 20px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🧪 Test Telegram Bot</h2>
        
        <div class="info-box">
            <strong>Token Bot:</strong><br>
            <code><?php echo BOT_TOKEN; ?></code>
        </div>
        
        <div class="success-box">
            <strong>📋 Cara Test Bot (Tanpa Webhook):</strong>
            <ol style="margin-top: 10px;">
                <li>Buka Telegram dan cari bot Anda</li>
                <li>Kirim pesan APA SAJA ke bot (contoh: "halo" atau "/start")</li>
                <li><strong>Refresh halaman ini</strong> - Chat ID Anda akan muncul di bawah</li>
                <li>Copy Chat ID tersebut</li>
                <li>Gunakan form di bawah untuk kirim pesan dari bot ke Telegram Anda</li>
            </ol>
        </div>
        
        <?php if (isset($updates_data['result']) && count($updates_data['result']) > 0): ?>
            <div class="success-box">
                <strong>✅ Chat Terdeteksi!</strong><br>
                Berikut chat yang mengirim pesan ke bot:
            </div>
            
            <?php 
            $latest_updates = array_slice(array_reverse($updates_data['result']), 0, 5);
            foreach ($latest_updates as $update): 
                if (isset($update['message'])):
                    $chat = $update['message']['chat'];
                    $from = $update['message']['from'];
                    $text = isset($update['message']['text']) ? $update['message']['text'] : '';
            ?>
                <div class="info-box">
                    <strong>👤 User:</strong> <?php echo htmlspecialchars($from['first_name']); ?><br>
                    <strong>🆔 Chat ID:</strong> <code><?php echo $chat['id']; ?></code><br>
                    <strong>💬 Pesan:</strong> <?php echo htmlspecialchars($text); ?>
                </div>
            <?php 
                endif;
            endforeach; 
            ?>
        <?php else: ?>
            <div class="info-box">
                <strong>ℹ️ Belum ada chat terdeteksi.</strong><br>
                Kirim pesan <code>/start</code> ke bot Anda di Telegram, lalu refresh halaman ini.
            </div>
        <?php endif; ?>
        
        <h3>📤 Kirim Pesan Test ke Chat:</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label>Chat ID:</label>
                <input type="text" name="chat_id" placeholder="Masukkan Chat ID" required>
            </div>
            <div class="form-group">
                <label>Pesan Test:</label>
                <select name="message_type" style="width: 300px; padding: 10px; margin-bottom: 10px;">
                    <option value="welcome">Pesan Welcome</option>
                    <option value="form_link">Link Form Pendaftaran</option>
                    <option value="custom">Custom Message</option>
                </select>
            </div>
            <button type="submit" name="send_test">Kirim Pesan Test</button>
        </form>
        
        <?php
        if (isset($_POST['send_test'])) {
            $chat_id = $_POST['chat_id'];
            $type = $_POST['message_type'];
            
            if ($type == 'welcome') {
                $message = "👋 <b>Selamat Datang!</b>\n\n";
                $message .= "🎓 <b>Bot Pendaftaran Mahasiswa</b>\n\n";
                $message .= "Silakan gunakan perintah berikut:\n";
                $message .= "/daftar - Link pendaftaran\n";
                $message .= "/data - Lihat data mahasiswa\n";
                $message .= "/statistik - Statistik pendaftaran";
            } elseif ($type == 'form_link') {
                $message = "📝 <b>Form Pendaftaran Mahasiswa</b>\n\n";
                $message .= "Klik link berikut untuk mengisi form:\n";
                $message .= "🔗 http://localhost:8000/index.html\n\n";
                $message .= "<i>Isi data dengan lengkap dan benar.</i>";
            } else {
                $message = "🧪 Ini adalah pesan test dari bot!\n\nBot berfungsi dengan baik. ✅";
            }
            
            $result = sendMessage($chat_id, $message);
            
            if ($result['ok']) {
                echo '<div class="success-box"><strong>✅ Pesan berhasil dikirim!</strong></div>';
            } else {
                echo '<div class="info-box" style="border-color: #f44336; background: #ffebee;">';
                echo '<strong>❌ Gagal kirim pesan:</strong><br>';
                echo htmlspecialchars($result['description']);
                echo '</div>';
            }
        }
        ?>
        
        <div style="margin-top: 30px;">
            <button onclick="location.reload()">🔄 Refresh</button>
            <button onclick="window.location.href='setup_webhook.php'" style="background: #28a745;">
                ⚙️ Setup Webhook
            </button>
            <button onclick="window.location.href='index.html'" style="background: #6c757d;">
                🏠 Home
            </button>
        </div>
        
        <div class="info-box" style="margin-top: 20px;">
            <strong>💡 Tips:</strong>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Gunakan Chat ID yang muncul di atas untuk kirim pesan test</li>
                <li>Simpan Chat ID untuk notifikasi otomatis di <code>proses.php</code></li>
                <li>Untuk webhook, gunakan ngrok atau hosting dengan HTTPS</li>
            </ul>
        </div>
    </div>
</body>
</html>
