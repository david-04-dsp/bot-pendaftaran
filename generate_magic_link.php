<?php
/**
 * Generate Magic Link dan Kirim via Telegram Bot
 */

require_once 'config.php';
require_once 'app_config.php';

header('Content-Type: application/json');

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';

if (empty($contact)) {
    echo json_encode(['success' => false, 'message' => 'Contact tidak boleh kosong']);
    exit;
}

// Cek apakah contact adalah username atau phone number
$is_username = strpos($contact, '@') === 0;
$search_value = $is_username ? substr($contact, 1) : $contact; // Remove @ if username

// Cari chat_id dari database bot atau getUpdates
$chat_id = findChatId($search_value, $is_username);

if (!$chat_id) {
    echo json_encode([
        'success' => false, 
        'message' => 'User tidak ditemukan. Silakan gunakan bot Telegram langsung: kirim /daftar ke https://t.me/percobaan_pendaftaran_bot (lebih mudah & langsung berhasil!)'
    ]);
    exit;
}

// Generate token unik
$token = bin2hex(random_bytes(32));
$expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));

try {
    // Simpan token ke database
    $stmt = $conn->prepare("
        INSERT INTO auth_tokens (token, telegram_chat_id, telegram_username, phone_number, expires_at)
        VALUES (:token, :chat_id, :username, :phone, :expires)
    ");
    
    $stmt->execute([
        ':token' => $token,
        ':chat_id' => $chat_id,
        ':username' => $is_username ? $search_value : null,
        ':phone' => !$is_username ? $search_value : null,
        ':expires' => $expires_at
    ]);
    
    // Generate magic link
    $magic_link = $BASE_URL . '/verify_access.php?token=' . $token;
    
    // Kirim link via Telegram
    $message = "🔐 Link Akses Pendaftaran Mahasiswa\n\n";
    $message .= "Klik link di bawah untuk mengakses form pendaftaran:\n\n";
    $message .= "🔗 " . $magic_link . "\n\n";
    $message .= "⏰ Link berlaku selama 30 menit\n";
    $message .= "🔒 Link hanya bisa digunakan 1 kali\n\n";
    $message .= "⚠️ Jangan bagikan link ini ke orang lain!";
    
    $send_result = sendTelegramMessage($chat_id, $message);
    
    if ($send_result && isset($send_result['ok']) && $send_result['ok']) {
        echo json_encode([
            'success' => true,
            'message' => 'Link akses berhasil dikirim ke Telegram Anda'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Gagal mengirim pesan ke Telegram'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

// Fungsi untuk mencari chat_id berdasarkan username atau nama
function findChatId($search_value, $is_username) {
    // Ambil updates dari Telegram untuk mencari user
    // Gunakan offset negatif untuk mendapat update terbaru
    $url = API_URL . 'getUpdates?limit=100&offset=-100';
    $updates = @file_get_contents($url);
    
    if (!$updates) {
        return null;
    }
    
    $data = json_decode($updates, true);
    
    if (!isset($data['result']) || empty($data['result'])) {
        return null;
    }
    
    // Kumpulkan semua user unik
    $users = [];
    foreach ($data['result'] as $update) {
        if (!isset($update['message']['from'])) {
            continue;
        }
        
        $from = $update['message']['from'];
        $user_id = $from['id'];
        
        if (!isset($users[$user_id])) {
            $users[$user_id] = $from;
        }
    }
    
    // Cari chat_id yang cocok dari user yang sudah dikumpulkan
    foreach ($users as $from) {
        if ($is_username) {
            // Cari berdasarkan username
            if (isset($from['username']) && strtolower($from['username']) === strtolower($search_value)) {
                return $from['id'];
            }
        } else {
            // Cari berdasarkan nama atau ID
            $first_name = strtolower($from['first_name'] ?? '');
            $last_name = strtolower($from['last_name'] ?? '');
            $full_name = trim($first_name . ' ' . $last_name);
            $search_lower = strtolower(trim($search_value));
            
            // Cek apakah nama cocok atau search value adalah ID
            if (stripos($full_name, $search_lower) !== false || 
                stripos($first_name, $search_lower) !== false ||
                stripos($last_name, $search_lower) !== false ||
                $from['id'] == $search_value) {
                return $from['id'];
            }
        }
    }
    
    // Jika tidak ketemu, return user pertama yang ditemukan (untuk testing)
    // Di production, ini harus dinonaktifkan
    if (!empty($users)) {
        return array_values($users)[0]['id']; // Return first user for testing
    }
    
    return null;
}

// Fungsi untuk mengirim pesan Telegram
function sendTelegramMessage($chat_id, $text) {
    $url = API_URL . 'sendMessage';
    $data = [
        'chat_id' => $chat_id,
        'text' => $text
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    return json_decode($result, true);
}
?>
