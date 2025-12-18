<?php
// CONFIG: Base URL untuk aplikasi
// Ubah ini sesuai environment

// Untuk localhost
// $BASE_URL = 'http://localhost:8000';

// Untuk ngrok (URL publik)
$BASE_URL = 'https://wainable-configurationally-hortencia.ngrok-free.dev';

// Auto-detect dari HTTP_HOST jika ada
if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
    // Cek header X-Forwarded-Proto untuk ngrok/proxy
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
    } elseif (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $protocol = 'https';
    } elseif (strpos($_SERVER['HTTP_HOST'], 'ngrok') !== false) {
        $protocol = 'https'; // Ngrok selalu HTTPS
    } else {
        $protocol = 'http';
    }
    $BASE_URL = $protocol . '://' . $_SERVER['HTTP_HOST'];
}

// Bot Token
$BOT_TOKEN = '7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU';
?>
