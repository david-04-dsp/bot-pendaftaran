<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Link ke Telegram</title>
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
            max-width: 700px;
            width: 100%;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .step {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        .step h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 18px;
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
        .error-box {
            background: #ffebee;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #f44336;
        }
        code {
            background: #f5f5f5;
            padding: 3px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #d63384;
        }
        button, .btn {
            padding: 14px 28px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        button:hover, .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: #28a745;
        }
        .btn-info {
            background: #17a2b8;
        }
        .chat-list {
            max-height: 300px;
            overflow-y: auto;
            margin: 15px 0;
        }
        .chat-item {
            background: white;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.3s;
        }
        .chat-item:hover {
            border-color: #667eea;
            transform: translateX(5px);
        }
        .chat-item.selected {
            border-color: #667eea;
            background: #f0f4ff;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🤖 Kirim Link Form ke Telegram</h2>
        
        <?php
        $bot_token = '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU';
        $api_url = "https://api.telegram.org/bot{$bot_token}/";
        
        // Cek apakah form disubmit
        if (isset($_POST['send_message'])) {
            $chat_id = $_POST['chat_id'];
            
            $message = "👋 <b>Selamat Datang!</b>\n\n";
            $message .= "🎓 <b>Bot Pendaftaran Mahasiswa</b>\n\n";
            $message .= "📝 Klik tombol di bawah untuk mengisi form pendaftaran mahasiswa:\n\n";
            $message .= "🔗 Link Form: http://localhost:8000/index.html\n\n";
            $message .= "<b>Atau gunakan perintah:</b>\n";
            $message .= "/daftar - Link form pendaftaran\n";
            $message .= "/data - Lihat data mahasiswa\n";
            $message .= "/statistik - Statistik pendaftaran\n\n";
            $message .= "<i>Silakan isi form dengan lengkap dan benar.</i>";
            
            $send_url = $api_url . "sendMessage";
            $post_data = [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'HTML'
            ];
            
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($post_data)
                ]
            ];
            
            $context = stream_context_create($options);
            $result = file_get_contents($send_url, false, $context);
            $response = json_decode($result, true);
            
            if ($response['ok']) {
                echo '<div class="success-box">';
                echo '<strong>✅ Pesan berhasil dikirim ke Telegram!</strong><br>';
                echo 'Silakan cek aplikasi Telegram Anda.';
                echo '</div>';
            } else {
                echo '<div class="error-box">';
                echo '<strong>❌ Gagal mengirim pesan:</strong><br>';
                echo htmlspecialchars($response['description']);
                echo '</div>';
            }
        }
        
        // Ambil data chat dari bot
        $updates_url = $api_url . "getUpdates";
        $updates_json = @file_get_contents($updates_url);
        $updates = json_decode($updates_json, true);
        
        ?>
        
        <div class="step">
            <h3>📋 Langkah 1: Kirim Pesan ke Bot</h3>
            <p>1. Buka Telegram di HP/Desktop Anda</p>
            <p>2. Cari bot Anda (username dari BotFather)</p>
            <p>3. Kirim pesan <code>/start</code> atau <code>halo</code></p>
            <p>4. Kembali ke halaman ini dan klik tombol refresh di bawah</p>
        </div>
        
        <?php if ($updates && isset($updates['result']) && count($updates['result']) > 0): ?>
            
            <div class="success-box">
                <strong>✅ Chat terdeteksi!</strong> Pilih chat Anda di bawah:
            </div>
            
            <form method="POST" action="">
                <div class="chat-list">
                    <?php 
                    $seen_chats = [];
                    $latest_updates = array_reverse($updates['result']);
                    
                    foreach ($latest_updates as $update):
                        if (isset($update['message'])):
                            $chat = $update['message']['chat'];
                            $from = $update['message']['from'];
                            $chat_id = $chat['id'];
                            
                            // Skip jika chat sudah ditampilkan
                            if (in_array($chat_id, $seen_chats)) continue;
                            $seen_chats[] = $chat_id;
                            
                            $name = isset($from['first_name']) ? $from['first_name'] : 'User';
                            $last_name = isset($from['last_name']) ? ' ' . $from['last_name'] : '';
                            $username = isset($from['username']) ? '@' . $from['username'] : '';
                            $text = isset($update['message']['text']) ? $update['message']['text'] : '';
                    ?>
                    <label class="chat-item">
                        <input type="radio" name="chat_id" value="<?php echo $chat_id; ?>" required>
                        <strong>👤 <?php echo htmlspecialchars($name . $last_name); ?></strong>
                        <?php if ($username): ?>
                            <span style="color: #666;"><?php echo htmlspecialchars($username); ?></span>
                        <?php endif; ?>
                        <br>
                        <small>🆔 Chat ID: <code><?php echo $chat_id; ?></code></small>
                        <?php if ($text): ?>
                            <br><small>💬 Pesan terakhir: "<?php echo htmlspecialchars(substr($text, 0, 50)); ?>"</small>
                        <?php endif; ?>
                    </label>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                
                <div class="step">
                    <h3>📋 Langkah 2: Kirim Link Form</h3>
                    <p>Pilih chat Anda di atas, lalu klik tombol di bawah:</p>
                    <button type="submit" name="send_message" class="btn-success" style="width: 100%; margin-top: 10px;">
                        📤 Kirim Link Form ke Telegram Saya
                    </button>
                </div>
            </form>
            
        <?php else: ?>
            
            <div class="info-box">
                <strong>ℹ️ Belum ada chat terdeteksi</strong><br>
                Pastikan Anda sudah:
                <ol style="margin-left: 20px; margin-top: 10px;">
                    <li>Buka Telegram</li>
                    <li>Cari bot Anda</li>
                    <li>Kirim pesan <code>/start</code> atau <code>halo</code></li>
                    <li>Klik tombol refresh di bawah</li>
                </ol>
            </div>
            
        <?php endif; ?>
        
        <div style="margin-top: 20px; text-align: center;">
            <button onclick="location.reload()" class="btn-info">
                🔄 Refresh / Cek Chat
            </button>
            <a href="index.html" class="btn" style="background: #6c757d;">
                🏠 Kembali ke Home
            </a>
        </div>
        
        <div class="info-box" style="margin-top: 20px;">
            <strong>💡 Catatan:</strong>
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>Bot akan mengirim link form pendaftaran ke chat Telegram Anda</li>
                <li>Ini cara mudah tanpa perlu setup webhook</li>
                <li>Untuk fitur bot otomatis, gunakan webhook dengan ngrok</li>
            </ul>
        </div>
    </div>
</body>
</html>
