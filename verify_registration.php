<?php
// PASSWORDLESS AUTHENTICATION: Endpoint untuk verifikasi pendaftaran via link dari Telegram
require_once 'config.php';

// Ambil token dari URL
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    showError("Token verifikasi tidak valid.");
    exit;
}

try {
    // Cari data pendaftaran berdasarkan token (cek semua status dulu)
    $stmt = $conn->prepare("
        SELECT * FROM mahasiswa 
        WHERE verification_token = :token
    ");
    $stmt->execute([':token' => $token]);
    $mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$mahasiswa) {
        showError("Token tidak ditemukan di database.");
        exit;
    }
    
    // Jika sudah verified, tampilkan pesan sudah terverifikasi
    if ($mahasiswa['verification_status'] === 'verified') {
        showAlreadyVerified($mahasiswa);
        exit;
    }
    
    // Jika status bukan pending, ada yang salah
    if ($mahasiswa['verification_status'] !== 'pending') {
        showError("Status verifikasi tidak valid.");
        exit;
    }
    
    // Cek apakah ini request konfirmasi atau hanya preview
    $confirm = isset($_GET['confirm']) ? $_GET['confirm'] : '';
    
    if ($confirm !== 'yes') {
        // Tampilkan halaman konfirmasi dulu
        showConfirmation($mahasiswa, $token);
        exit;
    }
    
    // Update status verifikasi
    $update_stmt = $conn->prepare("
        UPDATE mahasiswa 
        SET verification_status = 'verified',
            verified_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    $update_stmt->execute([':id' => $mahasiswa['id']]);
    
    // Tampilkan halaman sukses verifikasi
    showSuccess($mahasiswa);
    
} catch(PDOException $e) {
    showError("Terjadi kesalahan: " . $e->getMessage());
}

function showSuccess($data) {
    ?>
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Verifikasi Berhasil</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .success-box {
                background: white;
                border-radius: 15px;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
                padding: 50px 40px;
                text-align: center;
                max-width: 600px;
            }
            .success-icon {
                font-size: 80px;
                color: #28a745;
                margin-bottom: 25px;
                animation: scaleIn 0.5s ease;
            }
            @keyframes scaleIn {
                0% { transform: scale(0); }
                50% { transform: scale(1.1); }
                100% { transform: scale(1); }
            }
            h1 {
                color: #28a745;
                margin-bottom: 15px;
                font-size: 32px;
            }
            h2 {
                color: #333;
                margin-bottom: 25px;
                font-size: 24px;
            }
            .data-card {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 25px;
                margin: 25px 0;
                text-align: left;
            }
            .data-row {
                display: flex;
                padding: 10px 0;
                border-bottom: 1px solid #dee2e6;
            }
            .data-row:last-child {
                border-bottom: none;
            }
            .data-label {
                font-weight: 600;
                color: #666;
                width: 150px;
            }
            .data-value {
                color: #333;
                flex: 1;
            }
            .status-badge {
                display: inline-block;
                background: #28a745;
                color: white;
                padding: 8px 20px;
                border-radius: 20px;
                font-weight: 600;
                margin: 20px 0;
            }
            .btn-group {
                margin-top: 30px;
                display: flex;
                gap: 15px;
                justify-content: center;
            }
            a {
                text-decoration: none;
                padding: 14px 35px;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s;
                font-size: 16px;
            }
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            .btn-secondary {
                background: #6c757d;
                color: white;
            }
            a:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }
        </style>
    </head>
    <body>
        <div class='success-box'>
            <div class='success-icon'>✓</div>
            <h1>Verifikasi Berhasil!</h1>
            <h2>Pendaftaran Anda Telah Diaktifkan</h2>
            
            <div class='status-badge'>✓ TERVERIFIKASI</div>
            
            <div class='data-card'>
                <div class='data-row'>
                    <div class='data-label'>Nama:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['nama']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>NIM:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['nim']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>Email:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['email']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>Telepon:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['telepon']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>Jurusan:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['jurusan']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>Waktu Verifikasi:</div>
                    <div class='data-value'><?php echo date('d/m/Y H:i:s'); ?></div>
                </div>
            </div>
            
            <p style='color: #666; margin-top: 25px;'>
                🎉 Selamat! Data pendaftaran Anda sudah aktif dan tersimpan dalam sistem.
            </p>
            
            <div class='btn-group'>
                <a href='index_form.php' class='btn-primary'>Daftar Lagi</a>
                <a href='tampil.php' class='btn-secondary'>Lihat Semua Data</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function showConfirmation($data, $token) {
    ?>
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Konfirmasi Verifikasi</title>
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
            .confirm-box {
                background: white;
                border-radius: 15px;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
                padding: 50px 40px;
                text-align: center;
                max-width: 600px;
            }
            .confirm-icon {
                font-size: 80px;
                margin-bottom: 25px;
            }
            h1 {
                color: #333;
                margin-bottom: 20px;
            }
            .data-card {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 20px;
                margin: 25px 0;
                text-align: left;
            }
            .data-row {
                display: flex;
                padding: 8px 0;
                border-bottom: 1px solid #dee2e6;
            }
            .data-row:last-child {
                border-bottom: none;
            }
            .data-label {
                font-weight: 600;
                color: #666;
                width: 120px;
            }
            .data-value {
                color: #333;
                flex: 1;
            }
            .warning {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
                text-align: left;
                border-radius: 5px;
            }
            .btn-group {
                display: flex;
                gap: 15px;
                justify-content: center;
                margin-top: 30px;
            }
            a, button {
                text-decoration: none;
                padding: 15px 40px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 16px;
                border: none;
                cursor: pointer;
                transition: all 0.3s;
            }
            .btn-confirm {
                background: #28a745;
                color: white;
            }
            .btn-cancel {
                background: #6c757d;
                color: white;
            }
            button:hover, a:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }
        </style>
    </head>
    <body>
        <div class='confirm-box'>
            <div class='confirm-icon'>❓</div>
            <h1>Konfirmasi Verifikasi Pendaftaran</h1>
            
            <p>Apakah Anda yakin ingin memverifikasi pendaftaran dengan data berikut?</p>
            
            <div class='data-card'>
                <div class='data-row'>
                    <div class='data-label'>Nama:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['nama']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>NIM:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['nim']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>Email:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['email']); ?></div>
                </div>
                <div class='data-row'>
                    <div class='data-label'>Jurusan:</div>
                    <div class='data-value'><?php echo htmlspecialchars($data['jurusan']); ?></div>
                </div>
            </div>
            
            <div class='warning'>
                ⚠️ <strong>Perhatian:</strong> Setelah verifikasi, status pendaftaran akan diaktifkan dan tidak dapat dibatalkan.
            </div>
            
            <div class='btn-group'>
                <a href='verify_registration.php?token=<?php echo urlencode($token); ?>&confirm=yes' class='btn-confirm'>
                    ✓ Ya, Verifikasi Sekarang
                </a>
                <a href='tampil.php' class='btn-cancel'>
                    ✗ Batal
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

function showAlreadyVerified($data) {
    ?>
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Sudah Terverifikasi</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .info-box {
                background: white;
                border-radius: 15px;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
                padding: 50px 40px;
                text-align: center;
                max-width: 500px;
            }
            .info-icon {
                font-size: 80px;
                color: #17a2b8;
                margin-bottom: 25px;
            }
            h1 {
                color: #17a2b8;
                margin-bottom: 20px;
            }
            p {
                color: #666;
                font-size: 18px;
                margin-bottom: 30px;
            }
            .status-badge {
                display: inline-block;
                background: #28a745;
                color: white;
                padding: 8px 20px;
                border-radius: 20px;
                font-weight: 600;
                margin: 20px 0;
            }
            a {
                display: inline-block;
                text-decoration: none;
                padding: 14px 35px;
                border-radius: 8px;
                font-weight: 600;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transition: all 0.3s;
            }
            a:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }
        </style>
    </head>
    <body>
        <div class='info-box'>
            <div class='info-icon'>ℹ️</div>
            <h1>Sudah Terverifikasi</h1>
            <div class='status-badge'>✓ AKTIF</div>
            <p>Pendaftaran <strong><?php echo htmlspecialchars($data['nama']); ?></strong> (<?php echo htmlspecialchars($data['nim']); ?>) sudah terverifikasi sebelumnya.</p>
            <p>Waktu verifikasi: <?php echo date('d/m/Y H:i:s', strtotime($data['verified_at'])); ?></p>
            <a href='tampil.php'>Lihat Data</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

function showError($message) {
    ?>
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Verifikasi Gagal</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .error-box {
                background: white;
                border-radius: 15px;
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
                padding: 50px 40px;
                text-align: center;
                max-width: 500px;
            }
            .error-icon {
                font-size: 80px;
                color: #dc3545;
                margin-bottom: 25px;
            }
            h1 {
                color: #dc3545;
                margin-bottom: 20px;
            }
            p {
                color: #666;
                font-size: 18px;
                margin-bottom: 30px;
            }
            a {
                display: inline-block;
                text-decoration: none;
                padding: 14px 35px;
                border-radius: 8px;
                font-weight: 600;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transition: all 0.3s;
            }
            a:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            }
        </style>
    </head>
    <body>
        <div class='error-box'>
            <div class='error-icon'>✗</div>
            <h1>Verifikasi Gagal</h1>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href='index_form.php'>Kembali ke Form</a>
        </div>
    </body>
    </html>
    <?php
}
?>
