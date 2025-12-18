<?php
require_once 'config.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

echo "=== DEBUG VERIFY ACCESS ===\n\n";
echo "Token dari URL: " . substr($token, 0, 30) . "...\n\n";

if (empty($token)) {
    echo "❌ Token kosong!\n";
    exit;
}

$current_time = date('Y-m-d H:i:s');
echo "Current Time (PHP): " . $current_time . "\n\n";

// Cek apakah token ada di database
$stmt = $conn->prepare("SELECT * FROM auth_tokens WHERE token = :token");
$stmt->execute([':token' => $token]);
$token_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$token_data) {
    echo "❌ Token tidak ditemukan di database!\n";
    echo "Token yang dicari: " . $token . "\n\n";
    
    // Tampilkan 3 token terakhir
    echo "3 Token terakhir di database:\n";
    $stmt = $conn->query("SELECT token, expires_at, is_used FROM auth_tokens ORDER BY id DESC LIMIT 3");
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . substr($row['token'], 0, 30) . "... | Expires: " . $row['expires_at'] . " | Used: " . $row['is_used'] . "\n";
    }
    exit;
}

echo "✅ Token ditemukan!\n";
echo "- ID: " . $token_data['id'] . "\n";
echo "- Expires: " . $token_data['expires_at'] . "\n";
echo "- Is Used: " . $token_data['is_used'] . "\n";
echo "- Chat ID: " . $token_data['telegram_chat_id'] . "\n\n";

// Cek apakah sudah digunakan
if ($token_data['is_used'] == 1) {
    echo "❌ Token sudah digunakan!\n";
    exit;
}

// Cek apakah sudah expired
if ($token_data['expires_at'] <= $current_time) {
    echo "❌ Token sudah kadaluarsa!\n";
    echo "Expires: " . $token_data['expires_at'] . "\n";
    echo "Now: " . $current_time . "\n";
    exit;
}

echo "✅ Token VALID dan bisa digunakan!\n";
echo "\nQuery yang digunakan:\n";
echo "SELECT * FROM auth_tokens WHERE token = :token AND is_used = 0 AND expires_at > :current_time\n";
echo "Current time: " . $current_time . "\n";
echo "Expires at: " . $token_data['expires_at'] . "\n";
echo "Comparison: " . ($token_data['expires_at'] > $current_time ? "VALID" : "EXPIRED") . "\n";
