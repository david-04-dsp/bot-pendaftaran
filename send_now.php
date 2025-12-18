<?php
// Test push notification dengan chat_id langsung

require_once 'config.php';

$bot_token = '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU';
$chat_id = '1815508192'; // David's chat ID

// Ambil data pendaftaran terakhir yang pending
$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE verification_status = 'pending' ORDER BY id DESC LIMIT 1");
$stmt->execute();
$mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mahasiswa) {
    echo "Tidak ada pendaftaran pending.\n";
    exit;
}

echo "Mengirim push notification untuk:\n";
echo "Nama: " . $mahasiswa['nama'] . "\n";
echo "NIM: " . $mahasiswa['nim'] . "\n";
echo "Chat ID: $chat_id\n\n";

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
        echo "✅ Push notification berhasil dikirim ke Telegram!\n";
        echo "Silakan cek aplikasi Telegram Anda.\n";
    } else {
        echo "❌ Gagal mengirim: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "❌ Error menghubungi Telegram API.\n";
}
?>
