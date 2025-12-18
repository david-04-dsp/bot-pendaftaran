<?php
require_once 'config.php';
require_once 'app_config.php';

echo "Testing BASE_URL configuration...\n\n";
echo "BASE_URL: $BASE_URL\n";
echo "BOT_TOKEN: " . substr($BOT_TOKEN, 0, 10) . "...\n\n";

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
echo "NIM: " . $mahasiswa['nim'] . "\n\n";

// Ambil chat_id
$stmt = $conn->prepare("SELECT chat_id FROM telegram_users ORDER BY last_interaction DESC LIMIT 1");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    echo "ERROR: Tidak ada chat_id di database.\n";
    exit;
}

$chat_id = $result['chat_id'];
echo "Chat ID: $chat_id\n\n";

// Buat verification link dengan BASE_URL
$verification_link = $BASE_URL . "/verify_registration.php?token=" . $mahasiswa['verification_token'];
echo "Verification Link: $verification_link\n\n";

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

$url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
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
        echo "✅ Push notification berhasil dikirim dengan URL NGROK!\n";
        echo "Silakan cek Telegram Anda.\n";
    } else {
        echo "❌ Gagal mengirim: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "❌ Error menghubungi Telegram API.\n";
}
?>
