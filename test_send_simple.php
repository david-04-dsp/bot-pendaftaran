<?php
// Test kirim pesan sederhana dengan inline keyboard

define('BOT_TOKEN', '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
define('CHAT_ID', '1815508192');

$form_url = 'http://localhost:8000/index.html';
$data_url = 'http://localhost:8000/tampil.php';

echo "Testing simple message with inline keyboard...\n\n";

// Test 1: Pesan sederhana tanpa keyboard
echo "1. Testing simple text message...\n";
$url1 = API_URL . 'sendMessage';
$data1 = [
    'chat_id' => CHAT_ID,
    'text' => '📝 Form Pendaftaran Mahasiswa

Klik tombol di bawah atau copy link:

🔗 Form: ' . $form_url . '

🔗 Data: ' . $data_url
];

$options1 = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data1)
    ]
];

$context1 = stream_context_create($options1);
$result1 = file_get_contents($url1, false, $context1);
$response1 = json_decode($result1, true);

if ($response1 && $response1['ok']) {
    echo "   ✅ Text message berhasil dikirim!\n\n";
} else {
    echo "   ❌ Error: " . ($response1['description'] ?? 'Unknown') . "\n\n";
}

// Test 2: Pesan dengan inline keyboard
echo "2. Testing message with inline keyboard...\n";
$inline_keyboard = [
    'inline_keyboard' => [
        [
            ['text' => '🌐 BUKA FORM', 'url' => $form_url]
        ],
        [
            ['text' => '📊 LIHAT DATA', 'url' => $data_url]
        ]
    ]
];

$url2 = API_URL . 'sendMessage';
$data2 = [
    'chat_id' => CHAT_ID,
    'text' => '📝 Form Pendaftaran Mahasiswa

Klik tombol di bawah atau copy link:

🔗 Form: ' . $form_url . '

🔗 Data: ' . $data_url,
    'reply_markup' => json_encode($inline_keyboard)
];

$options2 = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data2)
    ]
];

$context2 = stream_context_create($options2);
$result2 = file_get_contents($url2, false, $context2);
$response2 = json_decode($result2, true);

if ($response2 && $response2['ok']) {
    echo "   ✅ Message dengan keyboard berhasil dikirim!\n";
    echo "   Cek Telegram Anda sekarang!\n\n";
} else {
    echo "   ❌ Error: " . ($response2['description'] ?? 'Unknown') . "\n";
    echo "   Raw response: " . $result2 . "\n\n";
}

echo "Test selesai.\n";
?>
