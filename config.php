<?php
// Konfigurasi Database SQLite
$db_file = __DIR__ . '/mahasiswa.db';

try {
    // Membuat koneksi ke database SQLite
    $conn = new PDO('sqlite:' . $db_file);
    
    // Set error mode ke exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set timeout untuk database lock (15 detik)
    $conn->setAttribute(PDO::ATTR_TIMEOUT, 15);
    
    // Enable WAL mode untuk concurrent access
    $conn->exec('PRAGMA journal_mode = WAL');
    $conn->exec('PRAGMA busy_timeout = 15000');
    
    // Buat tabel jika belum ada
    $conn->exec("
        CREATE TABLE IF NOT EXISTS mahasiswa (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nama TEXT NOT NULL,
            nim TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL,
            telepon TEXT NOT NULL,
            jenis_kelamin TEXT NOT NULL,
            jurusan TEXT NOT NULL,
            alamat TEXT NOT NULL,
            telegram_username TEXT,
            verification_status TEXT DEFAULT 'pending',
            verification_token TEXT,
            verified_at DATETIME,
            tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabel untuk authentication tokens
    $conn->exec("
        CREATE TABLE IF NOT EXISTS auth_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            token TEXT NOT NULL UNIQUE,
            telegram_chat_id TEXT NOT NULL,
            telegram_username TEXT,
            phone_number TEXT,
            email TEXT,
            is_used INTEGER DEFAULT 0,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabel untuk sessions
    $conn->exec("
        CREATE TABLE IF NOT EXISTS sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_id TEXT NOT NULL UNIQUE,
            telegram_chat_id TEXT NOT NULL,
            telegram_username TEXT,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Tabel untuk menyimpan chat_id user (untuk push notification)
    $conn->exec("
        CREATE TABLE IF NOT EXISTS telegram_users (
            chat_id TEXT PRIMARY KEY,
            username TEXT,
            first_name TEXT,
            last_name TEXT,
            last_interaction DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
