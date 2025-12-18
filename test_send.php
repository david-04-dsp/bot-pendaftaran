<?php
// Test kirim pesan langsung ke chat Anda

$bot_token = '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU';

// Ambil chat ID terbaru
$updates_url = "https://api.telegram.org/bot{$bot_token}/getUpdates";
$updates_json = file_get_contents($updates_url);
$updates = json_decode($updates_json, true);

if (isset($updates['result']) && count($updates['result']) > 0) {
    $latest = end($updates['result']);
    $chat_id = $latest['message']['chat']['id'];
    
    echo "Chat ID ditemukan: {$chat_id}<br><br>";
    
    // Kirim pesan test dengan inline button
    $message = "🎓 <b>Form Pendaftaran Mahasiswa</b>\n\n";
    $message .= "Klik tombol di bawah untuk membuka form pendaftaran:\n\n";
    $message .= "📝 Isi semua data dengan lengkap dan benar.";
    
    $inline_keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '🌐 BUKA FORM PENDAFTARAN', 'url' => 'http://localhost:8000/index.html']
            ],
            [
                ['text' => '📊 Lihat Data Terdaftar', 'url' => 'http://localhost:8000/tampil.php']
            ]
        ]
    ];
    
    $send_url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $post_data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML',
        'reply_markup' => json_encode($inline_keyboard)
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($post_data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($send_url, false, $context);
    $response = json_decode($result, true);
    
    echo "<h2>Hasil Pengiriman:</h2>";
    echo "<pre>";
    print_r($response);
    echo "</pre>";
    
    if ($response['ok']) {
        echo "<h3 style='color: green;'>✅ Pesan berhasil dikirim!</h3>";
        echo "<p>Cek Telegram Anda sekarang. Pesan baru dengan tombol inline sudah dikirim.</p>";
    } else {
        echo "<h3 style='color: red;'>❌ Gagal kirim pesan</h3>";
        echo "<p>Error: " . htmlspecialchars($response['description']) . "</p>";
    }
} else {
    echo "Tidak ada chat ditemukan. Kirim pesan /start ke bot dulu.";
}
?>
