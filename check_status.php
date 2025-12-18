<?php
require_once 'config.php';

echo "=== CEK STATUS VERIFIKASI ===\n\n";

$stmt = $conn->query("SELECT id, nama, nim, verification_status, verified_at, tanggal_daftar FROM mahasiswa ORDER BY id DESC LIMIT 5");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . "\n";
    echo "Nama: " . $row['nama'] . " (" . $row['nim'] . ")\n";
    echo "Status: " . $row['verification_status'] . "\n";
    echo "Verified at: " . ($row['verified_at'] ? $row['verified_at'] : 'Belum') . "\n";
    echo "Tanggal daftar: " . $row['tanggal_daftar'] . "\n";
    echo "-------------------\n\n";
}
