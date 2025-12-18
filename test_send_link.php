<?php
// Test kirim link manual ke user

require_once 'config.php';

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
define('CHAT_ID', '1815508192'); // Your chat ID

echo "Generating magic link...\n";

// Generate token
$token = bin2hex(random_bytes(32));
$expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));

// Simpan ke database
$stmt = $conn->prepare("
    INSERT INTO auth_tokens (token, telegram_chat_id, telegram_username, expires_at)
    VALUES (:token, :chat_id, :username, :expires)
");

$stmt->execute([
    ':token' => $token,
    ':chat_id' => CHAT_ID,
    ':username' => 'David',
    ':expires' => $expires_at
]);

echo "Token saved to database\n";

// Generate magic link
$magic_link = 'http://localhost:8000/verify_access.php?token=' . $token;

// Kirim ke Telegram
$message = "🔐 Link Akses Pendaftaran Mahasiswa\n\n";
$message .= "Klik atau copy link di bawah untuk mengakses form pendaftaran:\n\n";
$message .= "🔗 " . $magic_link . "\n\n";
$message .= "⏰ Link berlaku selama 30 menit\n";
$message .= "🔒 Link hanya bisa digunakan 1 kali\n";
$message .= "📱 Tidak perlu password!\n\n";
$message .= "⚠️ Jangan bagikan link ini ke orang lain!";

$url = API_URL . 'sendMessage';
$data = [
    'chat_id' => CHAT_ID,
    'text' => $message
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
$response = json_decode($result, true);

if ($response && $response['ok']) {
    echo "\n✅ Magic link berhasil dikirim ke Telegram!\n";
    echo "Cek Telegram Anda sekarang.\n";
    echo "\nLink: " . $magic_link . "\n";
} else {
    echo "\n❌ Gagal mengirim pesan\n";
    echo "Response: " . print_r($response, true) . "\n";
}
?>
