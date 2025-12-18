<?php
// Include file konfigurasi database
require_once 'config.php';

// Query untuk mengambil semua data mahasiswa
$sql = "SELECT * FROM mahasiswa ORDER BY tanggal_daftar DESC";
$stmt = $conn->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_rows = count($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        h2 {
            color: #333;
            font-size: 28px;
        }

        .btn {
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-delete {
            background: #f44336;
            color: white;
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-delete:hover {
            background: #da190b;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .info-box p {
            color: #555;
            font-size: 14px;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: #f8f9fa;
        }

        td {
            color: #555;
            font-size: 14px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-male {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-female {
            background: #fce4ec;
            color: #c2185b;
        }

        .badge-verified {
            background: #d4edda;
            color: #155724;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 10px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📊 Data Mahasiswa</h2>
            <a href="index_form.php" class="btn btn-primary">+ Tambah Data Baru</a>
        </div>

        <div class="info-box">
            <p><strong>Total Data:</strong> <?php echo $num_rows; ?> mahasiswa terdaftar</p>
        </div>

        <div class="table-container">
            <?php if ($num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Status</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Jenis Kelamin</th>
                        <th>Jurusan</th>
                        <th>Alamat</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach($result as $row): 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <?php 
                            $status = isset($row['verification_status']) ? $row['verification_status'] : 'pending';
                            $status_text = $status == 'verified' ? '✓ Terverifikasi' : '⏳ Pending';
                            $badge_class = $status == 'verified' ? 'badge-verified' : 'badge-pending';
                            ?>
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </td>
                        <td><strong><?php echo htmlspecialchars($row['nim']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['telepon']); ?></td>
                        <td>
                            <span class="badge <?php echo $row['jenis_kelamin'] == 'Laki-laki' ? 'badge-male' : 'badge-female'; ?>">
                                <?php echo $row['jenis_kelamin']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['jurusan']); ?></td>
                        <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_daftar'])); ?></td>
                        <td class="actions">
                            <a href="hapus.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-delete" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                               Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>📭 Belum ada data mahasiswa. Silakan tambah data baru.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
// PDO akan otomatis menutup koneksi saat script selesai
$conn = null;
?>
