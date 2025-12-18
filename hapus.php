<?php
// Include file konfigurasi database
require_once 'config.php';

// Cek apakah ada parameter ID
if (isset($_GET['id'])) {
    try {
        $id = $_GET['id'];
        
        // Prepared statement untuk menghapus data berdasarkan ID
        $sql = "DELETE FROM mahasiswa WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Jika berhasil dihapus, redirect ke halaman tampil
        header("Location: tampil.php?pesan=hapus_sukses");
        exit();
        
    } catch(PDOException $e) {
        // Jika gagal, redirect dengan pesan error
        header("Location: tampil.php?pesan=hapus_gagal");
        exit();
    }
} else {
    // Jika tidak ada ID, redirect ke halaman tampil
    header("Location: tampil.php");
    exit();
}

// PDO akan otomatis menutup koneksi
$conn = null;
?>
