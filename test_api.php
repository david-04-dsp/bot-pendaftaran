<?php
// Test koneksi ke Telegram API

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

echo "Testing Telegram Bot API...\n\n";

// Test 1: getMe
echo "1. Testing getMe (info bot)...\n";
$me_url = API_URL . 'getMe';
$me_result = @file_get_contents($me_url);
$me_data = json_decode($me_result, true);

if ($me_data && isset($me_data['ok']) && $me_data['ok']) {
    echo "   ✅ Bot aktif!\n";
    echo "   Bot username: @" . $me_data['result']['username'] . "\n";
    echo "   Bot name: " . $me_data['result']['first_name'] . "\n\n";
} else {
    echo "   ❌ Bot tidak aktif atau token salah!\n";
    echo "   Response: " . print_r($me_data, true) . "\n\n";
    exit;
}

// Test 2: getUpdates
echo "2. Testing getUpdates (pesan terbaru)...\n";
$updates_url = API_URL . 'getUpdates?limit=1';
$updates_result = @file_get_contents($updates_url);
$updates_data = json_decode($updates_result, true);

if ($updates_data && isset($updates_data['ok']) && $updates_data['ok']) {
    echo "   ✅ Bisa mendapatkan updates!\n";
    if (!empty($updates_data['result'])) {
        $last_update = end($updates_data['result']);
        if (isset($last_update['message'])) {
            $chat_id = $last_update['message']['chat']['id'];
            $from_name = $last_update['message']['from']['first_name'];
            echo "   Chat ID terakhir: $chat_id\n";
            echo "   Dari: $from_name\n\n";
            
            // Test 3: Kirim pesan
            echo "3. Testing sendMessage...\n";
            $test_text = "🤖 Test dari bot!\n\nWaktu: " . date('Y-m-d H:i:s');
            
            $send_url = API_URL . 'sendMessage';
            $post_data = [
                'chat_id' => $chat_id,
                'text' => $test_text,
                'parse_mode' => 'HTML'
            ];
            
            $options = [
                'http' => [
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($post_data)
                ]
            ];
            
            $context = stream_context_create($options);
            $send_result = @file_get_contents($send_url, false, $context);
            $send_data = json_decode($send_result, true);
            
            if ($send_data && isset($send_data['ok']) && $send_data['ok']) {
                echo "   ✅ Pesan berhasil dikirim!\n";
                echo "   Cek Telegram Anda sekarang.\n\n";
            } else {
                echo "   ❌ Gagal mengirim pesan!\n";
                echo "   Response: " . print_r($send_data, true) . "\n\n";
            }
        } else {
            echo "   ⚠️  Tidak ada pesan. Kirim /start ke bot terlebih dahulu.\n\n";
        }
    } else {
        echo "   ⚠️  Belum ada updates. Kirim /start ke bot terlebih dahulu.\n\n";
    }
} else {
    echo "   ❌ Gagal mendapatkan updates!\n";
    echo "   Response: " . print_r($updates_data, true) . "\n\n";
}

echo "Test selesai.\n";
?>
