<?php
// Cek info user dari Telegram

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

echo "Mencari informasi user Telegram...\n\n";

$updates_url = API_URL . 'getUpdates?limit=5';
$updates = file_get_contents($updates_url);
$data = json_decode($updates, true);

if (isset($data['result']) && !empty($data['result'])) {
    echo "User yang pernah chat dengan bot:\n";
    echo str_repeat("=", 60) . "\n\n";
    
    $users = [];
    foreach ($data['result'] as $update) {
        if (isset($update['message']['from'])) {
            $from = $update['message']['from'];
            $id = $from['id'];
            
            if (!isset($users[$id])) {
                $users[$id] = $from;
            }
        }
    }
    
    foreach ($users as $user) {
        echo "👤 Nama: " . ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') . "\n";
        echo "🆔 User ID: " . $user['id'] . "\n";
        echo "📱 Username: " . (isset($user['username']) ? '@' . $user['username'] : '(tidak ada username)') . "\n";
        echo "🗣️  Language: " . ($user['language_code'] ?? 'unknown') . "\n";
        echo "\n";
    }
} else {
    echo "Tidak ada data user ditemukan.\n";
}
?>
