<?php
/**
 * Bot Telegram dengan Polling Mode
 * Jalankan file ini di terminal dan biarkan berjalan
 * Bot akan otomatis merespon semua pesan yang masuk
 */

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

require_once 'config.php';
require_once 'app_config.php';

define('WEB_FORM_URL', $BASE_URL);

echo "🤖 Bot Telegram Polling Mode Started...\n";
echo "📱 Bot sedang berjalan dan menunggu pesan...\n";
echo "⚠️  JANGAN tutup terminal ini!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$offset = 0;

// Fungsi untuk mendapatkan total mahasiswa
function getTotalMahasiswa() {
    global $conn;
    $stmt = $conn->query("SELECT COUNT(*) as total FROM mahasiswa");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Fungsi untuk mendapatkan 5 mahasiswa terbaru
function getRecentMahasiswa() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM mahasiswa ORDER BY tanggal_daftar DESC LIMIT 5");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk mengirim pesan
function sendMessage($chat_id, $text, $reply_markup = null) {
    $url = API_URL . "sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
    ];
    
    if ($reply_markup) {
        $data['reply_markup'] = json_encode($reply_markup);
    }
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);
    
    // Debug: tampilkan error jika ada
    if (!$response || !isset($response['ok']) || !$response['ok']) {
        echo "   ⚠️ ERROR: " . ($response['description'] ?? 'No response') . "\n";
        if ($result) {
            echo "   Raw response: " . substr($result, 0, 200) . "\n";
        }
    }
    
    return $response;
}

// Loop terus menerus untuk polling
while (true) {
    $url = API_URL . "getUpdates?offset={$offset}&timeout=30";
    
    $updates_json = @file_get_contents($url);
    if (!$updates_json) {
        echo "⚠️  Koneksi error, retry...\n";
        sleep(3);
        continue;
    }
    
    $updates = json_decode($updates_json, true);
    
    if (!isset($updates['result']) || empty($updates['result'])) {
        continue;
    }
    
    foreach ($updates['result'] as $update) {
        $offset = $update['update_id'] + 1;
        
        if (!isset($update['message'])) {
            continue;
        }
        
        $message = $update['message'];
        $chat_id = $message['chat']['id'];
        $text = isset($message['text']) ? $message['text'] : '';
        $first_name = isset($message['from']['first_name']) ? $message['from']['first_name'] : 'User';
        $username = isset($message['from']['username']) ? $message['from']['username'] : null;
        $last_name = isset($message['from']['last_name']) ? $message['from']['last_name'] : null;
        
        // SIMPAN CHAT_ID USER KE DATABASE (untuk push notification)
        try {
            $stmt = $conn->prepare("INSERT OR REPLACE INTO telegram_users (chat_id, username, first_name, last_name, last_interaction) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
            $stmt->execute([$chat_id, $username, $first_name, $last_name]);
        } catch(PDOException $e) {
            // Silent fail, tidak masalah jika gagal simpan
        }
        
        echo "📩 Pesan dari {$first_name}: {$text}\n";
        
        // Perintah /start
        if ($text == '/start') {
            $welcome_text = "👋 <b>Selamat Datang, {$first_name}!</b>\n\n";
            $welcome_text .= "🎓 <b>Bot Pendaftaran Mahasiswa</b>\n\n";
            $welcome_text .= "Saya akan membantu Anda dalam proses pendaftaran mahasiswa.\n\n";
            $welcome_text .= "<b>Menu yang tersedia:</b>\n";
            $welcome_text .= "📝 /daftar - Link pendaftaran mahasiswa\n";
            $welcome_text .= "📊 /data - Lihat data mahasiswa\n";
            $welcome_text .= "📈 /statistik - Statistik pendaftaran\n";
            $welcome_text .= "ℹ️ /info - Informasi tentang bot\n\n";
            $welcome_text .= "Silakan pilih menu di bawah atau ketik perintah.";
            
            $keyboard = [
                'keyboard' => [
                    [
                        ['text' => '📝 Daftar Sekarang'],
                        ['text' => '📊 Lihat Data']
                    ],
                    [
                        ['text' => '📈 Statistik'],
                        ['text' => 'ℹ️ Info Bot']
                    ]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ];
            
            sendMessage($chat_id, $welcome_text, $keyboard);
            echo "✅ Respon dikirim: Welcome message\n\n";
        }
        
        // Perintah /daftar atau tombol Daftar - PASSWORDLESS AUTH
        elseif ($text == '/daftar' || $text == '📝 Daftar Sekarang') {
            // Generate token unik
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            try {
                // Simpan token ke database
                $stmt = $conn->prepare("
                    INSERT INTO auth_tokens (token, telegram_chat_id, telegram_username, expires_at)
                    VALUES (:token, :chat_id, :username, :expires)
                ");
                
                $username = isset($message['from']['username']) ? $message['from']['username'] : null;
                
                $stmt->execute([
                    ':token' => $token,
                    ':chat_id' => $chat_id,
                    ':username' => $username,
                    ':expires' => $expires_at
                ]);
                
                // Generate magic link dengan bypass ngrok warning
                $magic_link = WEB_FORM_URL . '/verify_access.php?token=' . $token;
                
                $daftar_text = "🔐 Link Akses Pendaftaran Mahasiswa\n\n";
                $daftar_text .= "⚠️ PENTING: Jika muncul warning ngrok, klik 'Visit Site'\n";
                $daftar_text .= "Klik link di bawah untuk mengakses form pendaftaran:\n\n";
                $daftar_text .= "🔗 " . $magic_link . "\n\n";
                $daftar_text .= "⏰ Link berlaku selama 30 menit\n";
                $daftar_text .= "🔒 Link hanya bisa digunakan 1 kali\n";
                $daftar_text .= "📱 Tidak perlu password!\n\n";
                $daftar_text .= "⚠️ Jangan bagikan link ini ke orang lain!";
                
                sendMessage($chat_id, $daftar_text);
                echo "✅ Respon dikirim: Magic link untuk passwordless auth\n\n";
                
            } catch (Exception $e) {
                $error_text = "❌ Maaf, terjadi kesalahan saat membuat link akses.\n\n";
                $error_text .= "Silakan coba lagi atau hubungi admin.";
                sendMessage($chat_id, $error_text);
                echo "❌ Error: " . $e->getMessage() . "\n\n";
            }
        }
        
        // Perintah /data atau tombol Lihat Data
        elseif ($text == '/data' || $text == '📊 Lihat Data') {
            $recent = getRecentMahasiswa();
            $total = getTotalMahasiswa();
            
            if ($total > 0) {
                $data_text = "📊 <b>Data Mahasiswa Terdaftar</b>\n\n";
                $data_text .= "Total: <b>{$total} mahasiswa</b>\n\n";
                $data_text .= "<b>5 Pendaftar Terbaru:</b>\n";
                $data_text .= "━━━━━━━━━━━━━━━━━━\n\n";
                
                foreach ($recent as $index => $mhs) {
                    $no = $index + 1;
                    $data_text .= "<b>{$no}. {$mhs['nama']}</b>\n";
                    $data_text .= "   📌 NIM: {$mhs['nim']}\n";
                    $data_text .= "   🎓 Jurusan: {$mhs['jurusan']}\n";
                    $data_text .= "   📧 Email: {$mhs['email']}\n";
                    $data_text .= "   📱 HP: {$mhs['telepon']}\n";
                    $data_text .= "   ⏰ " . date('d/m/Y H:i', strtotime($mhs['tanggal_daftar'])) . "\n\n";
                }
                
                $data_text .= "\n💡 <i>Untuk melihat semua data, buka:</i>\n";
                $data_text .= "<code>localhost:8000/tampil.php</code>";
                
                sendMessage($chat_id, $data_text);
            } else {
                sendMessage($chat_id, "📭 <b>Belum ada data mahasiswa.</b>\n\nSilakan daftar terlebih dahulu menggunakan /daftar");
            }
            echo "✅ Respon dikirim: Data mahasiswa\n\n";
        }
        
        // Perintah /statistik atau tombol Statistik
        elseif ($text == '/statistik' || $text == '📈 Statistik') {
            $total = getTotalMahasiswa();
            
            $stmt_laki = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE jenis_kelamin = 'Laki-laki'");
            $laki = $stmt_laki->fetch(PDO::FETCH_ASSOC)['total'];
            
            $stmt_perempuan = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE jenis_kelamin = 'Perempuan'");
            $perempuan = $stmt_perempuan->fetch(PDO::FETCH_ASSOC)['total'];
            
            $stmt_jurusan = $conn->query("SELECT jurusan, COUNT(*) as total FROM mahasiswa GROUP BY jurusan ORDER BY total DESC");
            $jurusan_data = $stmt_jurusan->fetchAll(PDO::FETCH_ASSOC);
            
            $stat_text = "📈 <b>Statistik Pendaftaran Mahasiswa</b>\n\n";
            $stat_text .= "👥 <b>Total Mahasiswa:</b> {$total}\n\n";
            $stat_text .= "<b>Berdasarkan Jenis Kelamin:</b>\n";
            $stat_text .= "👨 Laki-laki: {$laki}\n";
            $stat_text .= "👩 Perempuan: {$perempuan}\n\n";
            
            if (count($jurusan_data) > 0) {
                $stat_text .= "<b>Berdasarkan Jurusan:</b>\n";
                foreach ($jurusan_data as $jur) {
                    $stat_text .= "🎓 {$jur['jurusan']}: {$jur['total']}\n";
                }
            }
            
            sendMessage($chat_id, $stat_text);
            echo "✅ Respon dikirim: Statistik\n\n";
        }
        
        // Perintah /info atau tombol Info Bot
        elseif ($text == '/info' || $text == 'ℹ️ Info Bot') {
            $info_text = "ℹ️ <b>Informasi Bot</b>\n\n";
            $info_text .= "🤖 <b>Nama:</b> Bot Pendaftaran Mahasiswa\n";
            $info_text .= "📝 <b>Fungsi:</b> Membantu proses pendaftaran mahasiswa secara online\n\n";
            $info_text .= "<b>Fitur:</b>\n";
            $info_text .= "✅ Link form pendaftaran online\n";
            $info_text .= "✅ Melihat data mahasiswa terdaftar\n";
            $info_text .= "✅ Statistik pendaftaran\n";
            $info_text .= "✅ Notifikasi pendaftaran baru\n\n";
            $info_text .= "<b>Cara Penggunaan:</b>\n";
            $info_text .= "1. Klik tombol menu atau ketik perintah\n";
            $info_text .= "2. Pilih 'Daftar Sekarang' untuk mengisi form\n";
            $info_text .= "3. Isi data dengan lengkap\n";
            $info_text .= "4. Data tersimpan otomatis\n\n";
            $info_text .= "💡 <i>Gunakan /start untuk kembali ke menu utama</i>";
            
            sendMessage($chat_id, $info_text);
            echo "✅ Respon dikirim: Info bot\n\n";
        }
        
        // Pesan tidak dikenali
        else {
            $help_text = "❓ Maaf, saya tidak mengerti perintah tersebut.\n\n";
            $help_text .= "Silakan gunakan menu atau perintah berikut:\n";
            $help_text .= "/start - Menu utama\n";
            $help_text .= "/daftar - Form pendaftaran\n";
            $help_text .= "/data - Lihat data mahasiswa\n";
            $help_text .= "/statistik - Lihat statistik\n";
            $help_text .= "/info - Info bot";
            
            sendMessage($chat_id, $help_text);
            echo "✅ Respon dikirim: Help message\n\n";
        }
    }
    
    // Delay kecil untuk tidak membebani server
    usleep(100000); // 0.1 detik
}
?>
