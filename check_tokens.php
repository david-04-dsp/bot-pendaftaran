<?php
require_once 'config.php';

echo "=== CEK AUTH TOKENS ===\n\n";

// Cek token terakhir
$stmt = $conn->query("SELECT token, expires_at, is_used, datetime('now') as now_time FROM auth_tokens ORDER BY id DESC LIMIT 3");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Token: " . substr($row['token'], 0, 20) . "...\n";
    echo "Expires: " . $row['expires_at'] . "\n";
    echo "Used: " . $row['is_used'] . "\n";
    echo "Now: " . $row['now_time'] . "\n";
    
    // Cek apakah masih valid
    if ($row['expires_at'] > $row['now_time'] && $row['is_used'] == 0) {
        echo "Status: ✅ VALID\n";
    } else {
        echo "Status: ❌ EXPIRED/USED\n";
    }
    echo "-------------------\n\n";
}
