-- Buat Database
CREATE DATABASE IF NOT EXISTS db_mahasiswa;

-- Gunakan Database
USE db_mahasiswa;

-- Buat Tabel Mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    jenis_kelamin ENUM('Laki-laki', 'Perempuan') NOT NULL,
    jurusan VARCHAR(50) NOT NULL,
    alamat TEXT NOT NULL,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Data Sample (Opsional)
INSERT INTO mahasiswa (nama, nim, email, telepon, jenis_kelamin, jurusan, alamat) VALUES
('Ahmad Rizki', '2021001', 'ahmad.rizki@email.com', '081234567890', 'Laki-laki', 'Teknik Informatika', 'Jl. Merdeka No. 10, Jakarta'),
('Siti Nurhaliza', '2021002', 'siti.nur@email.com', '081234567891', 'Perempuan', 'Sistem Informasi', 'Jl. Sudirman No. 20, Bandung');
