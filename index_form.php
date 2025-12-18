<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Mahasiswa</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
            margin-bottom: 0;
        }

        .radio-group input[type="radio"] {
            margin-right: 5px;
            width: auto;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-view {
            background: #28a745;
            color: white;
        }

        .btn-view:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .btn-reset {
            background: #f44336;
            color: white;
        }

        .btn-reset:hover {
            background: #da190b;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 Form Pendaftaran Mahasiswa</h2>
        <form action="proses.php" method="POST">
            <div class="form-group">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
            </div>

            <div class="form-group">
                <label for="nim">NIM:</label>
                <input type="text" id="nim" name="nim" required placeholder="Masukkan NIM">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="contoh@email.com">
            </div>

            <div class="form-group">
                <label for="telepon">Nomor Telepon:</label>
                <input type="tel" id="telepon" name="telepon" required placeholder="08xx-xxxx-xxxx">
            </div>

            <div class="form-group">
                <label>Jenis Kelamin:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="jenis_kelamin" value="Laki-laki" required> Laki-laki
                    </label>
                    <label>
                        <input type="radio" name="jenis_kelamin" value="Perempuan" required> Perempuan
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="jurusan">Jurusan:</label>
                <select id="jurusan" name="jurusan" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <option value="Teknik Informatika">Teknik Informatika</option>
                    <option value="Sistem Informasi">Sistem Informasi</option>
                    <option value="Teknologi Informasi">Teknologi Informasi</option>
                    <option value="Teknik Komputer">Teknik Komputer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" required placeholder="Masukkan alamat lengkap"></textarea>
            </div>
            <div style="background: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <p style="margin: 0; color: #1976D2; font-size: 14px;">
                    <strong>ℹ️ Notifikasi Verifikasi:</strong><br>
                    Setelah submit, Anda akan menerima push notification di Telegram untuk verifikasi pendaftaran.
                </p>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn-submit">Simpan Data</button>
                <button type="button" class="btn-view" onclick="window.location.href='tampil.php'">Lihat Data</button>
                <button type="reset" class="btn-reset">Reset</button>
            </div>
        </form>
    </div>
</body>
</html>
