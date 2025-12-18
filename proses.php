<?php
// Include file konfigurasi database
require_once 'config.php';
require_once 'app_config.php';

session_start();

// PASSWORDLESS AUTHENTICATION: Fungsi untuk kirim push notifikasi verifikasi ke Telegram
function sendVerificationToTelegram($data) {
    global $conn, $BASE_URL, $BOT_TOKEN;
    
    // Ambil chat_id dari session (user yang sedang login via link)
    if (!isset($_SESSION['telegram_chat_id'])) {
        return false; // User tidak login via telegram
    }
    
    $chat_id = $_SESSION['telegram_chat_id'];
    
    // Buat verification link dengan BASE_URL dari config
    $verification_link = $BASE_URL . "/verify_registration.php?token=" . $data['verification_token'];
    
    // PUSH NOTIFICATION: Kirim pesan verifikasi
    $message = "🔐 <b>Verifikasi Pendaftaran</b>\n\n";
    $message .= "Halo <b>{$data['nama']}</b>!\n\n";
    $message .= "Anda baru saja melakukan pendaftaran dengan data:\n";
    $message .= "📝 NIM: {$data['nim']}\n";
    $message .= "📧 Email: {$data['email']}\n";
    $message .= "📱 Telepon: {$data['telepon']}\n\n";
    $message .= "⚠️ <b>Penting:</b> Klik link di bawah untuk memverifikasi dan mengaktifkan pendaftaran Anda:\n\n";
    $message .= "🔗 {$verification_link}\n\n";
    $message .= "Link ini akan kadaluarsa dalam 24 jam.\n";
    $message .= "Jika Anda tidak melakukan pendaftaran, abaikan pesan ini.";
    
    $url = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
    $post_data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($post_data),
            'timeout' => 5
        ]
    ];
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    return ($result !== false);
}

// Cek apakah form disubmit dengan method POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    try {
        // Ambil data dari form
        $nama = $_POST['nama'];
        $nim = $_POST['nim'];
        $email = $_POST['email'];
        $telepon = $_POST['telepon'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $jurusan = $_POST['jurusan'];
        $alamat = $_POST['alamat'];
        
        // Generate verification token
        $verification_token = bin2hex(random_bytes(32));
        
        // Prepared statement untuk insert data ke database (STATUS: PENDING)
        $sql = "INSERT INTO mahasiswa (nama, nim, email, telepon, jenis_kelamin, jurusan, alamat, verification_status, verification_token) 
                VALUES (:nama, :nim, :email, :telepon, :jenis_kelamin, :jurusan, :alamat, 'pending', :verification_token)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':nama' => $nama,
            ':nim' => $nim,
            ':email' => $email,
            ':telepon' => $telepon,
            ':jenis_kelamin' => $jenis_kelamin,
            ':jurusan' => $jurusan,
            ':alamat' => $alamat,
            ':verification_token' => $verification_token
        ]);
        
        $mahasiswa_id = $conn->lastInsertId();
        
        // PASSWORDLESS AUTH: Kirim push notification untuk verifikasi
        $verification_sent = sendVerificationToTelegram([
            'id' => $mahasiswa_id,
            'nama' => $nama,
            'nim' => $nim,
            'email' => $email,
            'telepon' => $telepon,
            'verification_token' => $verification_token
        ]);
        
        // Tampilkan halaman sukses dengan instruksi verifikasi
        $verification_message = $verification_sent 
            ? "Push notifikasi verifikasi telah dikirim ke Telegram Anda. Silakan cek aplikasi Telegram dan klik link verifikasi untuk mengaktifkan pendaftaran."
            : "Data pendaftaran tersimpan dengan status PENDING. Silakan hubungi administrator untuk verifikasi.";
        
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Pendaftaran Berhasil</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    padding: 20px;
                }
                .success-box {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    padding: 40px;
                    text-align: center;
                    max-width: 500px;
                }
                .success-icon {
                    font-size: 60px;
                    color: #FFA500;
                    margin-bottom: 20px;
                }
                h2 {
                    color: #333;
                    margin-bottom: 20px;
                }
                p {
                    color: #666;
                    margin-bottom: 30px;
                    font-size: 16px;
                }
                .verification-note {
                    background: #fff3cd;
                    border: 1px solid #ffc107;
                    border-radius: 5px;
                    padding: 15px;
                    margin-bottom: 20px;
                    color: #856404;
                }
                .btn-group {
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                }
                a {
                    text-decoration: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    font-weight: 600;
                    transition: all 0.3s;
                }
                .btn-home {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                }
                .btn-view {
                    background: #28a745;
                    color: white;
                }
                a:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                }
            </style>
        </head>
        <body>
            <div class='success-box'>
                <div class='success-icon'>⏳</div>
                <h2>Pendaftaran Berhasil!</h2>
                <div class='verification-note'>
                    <strong>📱 Cek Telegram Anda!</strong>
                </div>
                <p>{$verification_message}</p>
                
                <div class='info' style='background: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0; border-left: 4px solid #ffc107;'>
                    ⏳ Status: <strong>Menunggu Verifikasi</strong><br>
                    📲 Link verifikasi telah dikirim ke Telegram<br>
                    ⚠️ Klik link di Telegram untuk menyelesaikan pendaftaran
                </div>

                <p><strong>Data yang didaftarkan:</strong></p>
                <p style='text-align: left; margin: 20px 0;'>
                    Nama: <strong>{$nama}</strong><br>
                    NIM: <strong>{$nim}</strong><br>
                    Email: <strong>{$email}</strong><br>
                    Telepon: <strong>{$telepon}</strong>
                </p>
                <div class='btn-group'>
                    <a href='index_form.php' class='btn-home'>Kembali ke Form</a>
                </div>
            </div>
        </body>
        </html>";
        
    } catch(PDOException $e) {
        // Jika gagal, tampilkan pesan error
        $errorMsg = $e->getMessage();
        if (strpos($errorMsg, 'UNIQUE constraint failed') !== false) {
            $errorMsg = "NIM sudah terdaftar. Silakan gunakan NIM yang berbeda.";
        }
        
        echo "<!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    padding: 20px;
                }
                .error-box {
                    background: white;
                    border-radius: 10px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                    padding: 40px;
                    text-align: center;
                    max-width: 500px;
                }
                .error-icon {
                    font-size: 60px;
                    color: #f44336;
                    margin-bottom: 20px;
                }
                h2 {
                    color: #333;
                    margin-bottom: 20px;
                }
                p {
                    color: #666;
                    margin-bottom: 30px;
                    font-size: 14px;
                }
                a {
                    text-decoration: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    font-weight: 600;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    transition: all 0.3s;
                    display: inline-block;
                }
                a:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                }
            </style>
        </head>
        <body>
            <div class='error-box'>
                <div class='error-icon'>✗</div>
                <h2>Error: Gagal Menyimpan Data</h2>
                <p>" . htmlspecialchars($errorMsg) . "</p>
                <a href='index.html'>Kembali ke Form</a>
            </div>
        </body>
        </html>";
    }
} else {
    // Jika tidak ada data POST, redirect ke halaman form
    header("Location: index.html");
    exit();
}
?>
