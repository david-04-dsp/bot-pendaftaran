<?php
// Token Bot Telegram
define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// URL Web Form (sesuaikan dengan domain/ngrok Anda)
define('WEB_FORM_URL', 'http://localhost:8000/index.html');

// Include konfigurasi database
require_once 'config.php';

// Fungsi untuk mengirim pesan ke Telegram
function sendMessage($chat_id, $text, $reply_markup = null) {
    $url = API_URL . "sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
    ];
    
    if ($reply_markup) {
        $data['reply_markup'] = json_encode($reply_markup);
    }
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

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

// Terima update dari Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    exit;
}

// Ambil data pesan
$message = isset($update['message']) ? $update['message'] : null;
$callback_query = isset($update['callback_query']) ? $update['callback_query'] : null;

if ($message) {
    $chat_id = $message['chat']['id'];
    $text = isset($message['text']) ? $message['text'] : '';
    $first_name = isset($message['from']['first_name']) ? $message['from']['first_name'] : 'User';
    
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
    }
    
    // Perintah /daftar atau tombol Daftar
    elseif ($text == '/daftar' || $text == '📝 Daftar Sekarang') {
        $daftar_text = "📝 <b>Form Pendaftaran Mahasiswa</b>\n\n";
        $daftar_text .= "Silakan klik tombol di bawah untuk mengisi form pendaftaran:\n\n";
        $daftar_text .= "🔗 <a href='{WEB_FORM_URL}'>Buka Form Pendaftaran</a>\n\n";
        $daftar_text .= "<i>Form akan terbuka di browser Anda.</i>";
        
        $inline_keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🌐 Buka Form Pendaftaran', 'url' => WEB_FORM_URL]
                ]
            ]
        ];
        
        sendMessage($chat_id, $daftar_text, $inline_keyboard);
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
            
            $inline_keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => '📋 Lihat Semua Data', 'url' => str_replace('index.html', 'tampil.php', WEB_FORM_URL)]
                    ]
                ]
            ];
            
            sendMessage($chat_id, $data_text, $inline_keyboard);
        } else {
            sendMessage($chat_id, "📭 <b>Belum ada data mahasiswa.</b>\n\nSilakan daftar terlebih dahulu menggunakan /daftar");
        }
    }
    
    // Perintah /statistik atau tombol Statistik
    elseif ($text == '/statistik' || $text == '📈 Statistik') {
        $total = getTotalMahasiswa();
        
        // Hitung berdasarkan jenis kelamin
        $stmt_laki = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE jenis_kelamin = 'Laki-laki'");
        $laki = $stmt_laki->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt_perempuan = $conn->query("SELECT COUNT(*) as total FROM mahasiswa WHERE jenis_kelamin = 'Perempuan'");
        $perempuan = $stmt_perempuan->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Hitung berdasarkan jurusan
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
    }
}

// Handle callback query (inline button)
elseif ($callback_query) {
    $chat_id = $callback_query['message']['chat']['id'];
    $data = $callback_query['data'];
    
    // Bisa ditambahkan handler untuk callback query jika diperlukan
}

?>
