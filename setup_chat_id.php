<?php
require_once 'config.php';

// Insert chat_id David yang sudah kita tahu
$chat_id = '1815508192';
$username = 'david_santogi'; // tanpa @
$first_name = 'David';

$stmt = $conn->prepare("INSERT OR REPLACE INTO telegram_users (chat_id, username, first_name, last_interaction) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
$stmt->execute([$chat_id, $username, $first_name]);

echo "✅ Chat ID tersimpan!\n\n";

// Tampilkan semua user yang tersimpan
$stmt = $conn->query("SELECT * FROM telegram_users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Users tersimpan di database:\n";
foreach ($users as $user) {
    echo "- Chat ID: {$user['chat_id']}\n";
    echo "  Username: " . ($user['username'] ?? 'tidak ada') . "\n";
    echo "  Nama: {$user['first_name']}\n";
    echo "  Terakhir aktif: {$user['last_interaction']}\n\n";
}
?>
