<?php
// Test manual push notification untuk pendaftaran terakhir

require_once 'config.php';

$bot_token = '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU';

// Ambil data pendaftaran terakhir yang pending
$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE verification_status = 'pending' ORDER BY id DESC LIMIT 1");
$stmt->execute();
$mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa) {
    echo "Tidak ada pendaftaran pending.\n";
    exit;
}

echo "Data pendaftaran terakhir:\n";
echo "Nama: " . $mahasiswa['nama'] . "\n";
echo "NIM: " . $mahasiswa['nim'] . "\n";
echo "Telegram Username: " . ($mahasiswa['telegram_username'] ?? 'tidak ada') . "\n\n";

// Cari chat_id dari getUpdates
$updates_url = "https://api.telegram.org/bot{$bot_token}/getUpdates?limit=10";
$updates_response = file_get_contents($updates_url);
$updates = json_decode($updates_response, true);

$chat_id = null;
if (isset($updates['result']) && count($updates['result']) > 0) {
    // Ambil user pertama
    foreach ($updates['result'] as $update) {
        if (isset($update['message']['from']['id'])) {
            $chat_id = $update['message']['from']['id'];
            $username = $update['message']['from']['first_name'] ?? 'User';
            echo "Found user: $username (chat_id: $chat_id)\n";
            break;
        }
    }
}

if (!$chat_id) {
    echo "ERROR: Tidak bisa menemukan chat_id. Kirim /start ke bot terlebih dahulu.\n";
    exit;
}

// Buat verification link
$verification_link = "http://localhost:8000/verify_registration.php?token=" . $mahasiswa['verification_token'];

// Kirim push notification
$message = "🔐 <b>Verifikasi Pendaftaran</b>\n\n";
$message .= "Halo <b>{$mahasiswa['nama']}</b>!\n\n";
$message .= "Anda baru saja melakukan pendaftaran dengan data:\n";
$message .= "📝 NIM: {$mahasiswa['nim']}\n";
$message .= "📧 Email: {$mahasiswa['email']}\n";
$message .= "📱 Telepon: {$mahasiswa['telepon']}\n\n";
$message .= "⚠️ <b>Penting:</b> Klik link di bawah untuk memverifikasi dan mengaktifkan pendaftaran Anda:\n\n";
$message .= "🔗 {$verification_link}\n\n";
$message .= "Link ini akan kadaluarsa dalam 24 jam.\n";
$message .= "Jika Anda tidak melakukan pendaftaran, abaikan pesan ini.";

$url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
$post_data = [
    'chat_id' => $chat_id,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($post_data),
        'timeout' => 10
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result !== false) {
    $response = json_decode($result, true);
    if (isset($response['ok']) && $response['ok']) {
        echo "\n✅ Push notification berhasil dikirim ke Telegram!\n";
        echo "Silakan cek aplikasi Telegram Anda.\n";
    } else {
        echo "\n❌ Gagal mengirim: " . json_encode($response) . "\n";
    }
} else {
    echo "\n❌ Error menghubungi Telegram API.\n";
}
?>
