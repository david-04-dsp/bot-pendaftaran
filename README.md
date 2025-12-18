# Web Form Sederhana dengan Database

Aplikasi web form sederhana untuk pendaftaran mahasiswa menggunakan HTML, PHP, dan SQLite.

## 📋 Fitur

- Form input data mahasiswa (Nama, NIM, Email, Telepon, Jenis Kelamin, Jurusan, Alamat)
- Simpan data ke database SQLite (tidak perlu MySQL server)
- Tampilkan semua data mahasiswa dalam tabel
- Hapus data mahasiswa
- Desain responsif dengan gradient background
- **Database otomatis dibuat saat pertama kali diakses**

## 🛠️ Teknologi yang Digunakan

- **HTML5** - Struktur halaman web
- **CSS3** - Styling dengan gradient dan animasi
- **PHP** - Backend dan koneksi database
- **SQLite** - Database file-based (tidak perlu server MySQL)

## 📁 Struktur File

```
percobaan/
├── index.html      # Halaman form input data
├── config.php      # Konfigurasi koneksi database
├── proses.php      # Proses simpan data ke database
├── tampil.php      # Halaman menampilkan data
├── hapus.php       # Proses hapus data
├── database.sql    # File SQL untuk membuat database dan tabel
└── README.md       # Dokumentasi
```

## 🚀 Cara Instalasi

### 1. Persiapan Lingkungan

Pastikan Anda sudah menginstall:
- **PHP** (bisa via XAMPP atau PHP standalone)
- Web browser (Chrome, Firefox, Edge, dll)

**TIDAK PERLU MySQL/phpMyAdmin** - Database SQLite otomatis dibuat!

### 2. Setup Aplikasi (MUDAH!)

1. Copy semua file ke folder htdocs:
   ```
   C:\xampp\htdocs\percobaan\
   ```

2. **SELESAI!** Tidak perlu setup database manual.

### 3. Jalankan Aplikasi

**Opsi 1: Menggunakan XAMPP**
1. Buka XAMPP Control Panel
2. Start **Apache** saja (MySQL TIDAK diperlukan)
3. Buka browser: `http://localhost/percobaan/index.html`

**Opsi 2: Menggunakan PHP Built-in Server**
1. Buka PowerShell/Command Prompt
2. Masuk ke folder aplikasi:
   ```powershell
   cd "C:\Users\david\OneDrive\Desktop\IV-GASAL\Teknologi Multimedia\percobaan"
   ```
3. Jalankan server:
   ```powershell
   php -S localhost:8000
   ```
4. Buka browser: `http://localhost:8000/index.html`

### 4. Database Otomatis!

- Database `mahasiswa.db` akan otomatis dibuat saat pertama kali mengakses aplikasi
- Tabel juga otomatis dibuat
- **Tidak perlu import SQL manual!**

## 📖 Cara Penggunaan

### Menambah Data Mahasiswa:
1. Buka halaman `index.html`
2. Isi semua field yang tersedia
3. Klik tombol **"Simpan Data"**
4. Data akan tersimpan dan Anda akan diarahkan ke halaman sukses

### Melihat Data Mahasiswa:
1. Klik tombol **"Lihat Data"** di halaman form
2. Atau akses langsung `tampil.php`
3. Semua data mahasiswa akan ditampilkan dalam tabel

### Menghapus Data Mahasiswa:
1. Buka halaman `tampil.php`
2. Klik tombol **"Hapus"** pada data yang ingin dihapus
3. Konfirmasi penghapusan
4. Data akan terhapus dari database

## 🗄️ Struktur Database

**File Database:** `mahasiswa.db` (SQLite)

**Tabel:** `mahasiswa`

| Field | Type | Keterangan |
|-------|------|------------|
| id | INTEGER PRIMARY KEY | Auto increment |
| nama | TEXT | Nama lengkap mahasiswa |
| nim | TEXT UNIQUE | NIM mahasiswa (unik) |
| email | TEXT | Email mahasiswa |
| telepon | TEXT | Nomor telepon |
| jenis_kelamin | TEXT | 'Laki-laki' atau 'Perempuan' |
| jurusan | TEXT | Jurusan mahasiswa |
| alamat | TEXT | Alamat lengkap |
| tanggal_daftar | DATETIME | Tanggal pendaftaran otomatis |

## 🎨 Fitur Desain

- Gradient background (ungu-biru)
- Box shadow untuk efek kedalaman
- Hover effects pada tombol
- Responsive design untuk mobile
- Badge warna untuk jenis kelamin
- Animasi smooth pada interaksi

## ⚠️ Troubleshooting

### File mahasiswa.db tidak terbuat
- Pastikan folder aplikasi memiliki permission write
- Coba jalankan dengan PHP built-in server

### Error: "Call to undefined function..."
- Pastikan PHP sudah terinstall dengan benar
- Cek versi PHP: `php -v` (minimal PHP 5.3+)

### Halaman blank atau tidak ada tampilan
- Pastikan Apache atau PHP server sudah berjalan
- Cek console browser (F12) untuk error JavaScript
- Pastikan mengakses via `localhost`, bukan membuka file langsung

### Error saat menyimpan data
- Cek apakah NIM sudah ada (NIM harus unik)
- Pastikan semua field terisi

## 📝 Catatan Penting

- ✅ **Tidak perlu MySQL server** - menggunakan SQLite
- ✅ **Database otomatis dibuat** saat pertama kali akses
- ✅ **Lebih aman** dengan PDO prepared statements
- ✅ **Portable** - bisa dibawa tanpa konfigurasi ulang
- ✅ File database: `mahasiswa.db` akan muncul di folder aplikasi

## 👨‍💻 Pengembangan Lebih Lanjut

Fitur yang bisa ditambahkan:
- [ ] Edit/Update data mahasiswa
- [ ] Pencarian data
- [ ] Filter berdasarkan jurusan
- [ ] Export data ke Excel/PDF
- [ ] Login system untuk admin
- [ ] Pagination untuk data banyak
- [ ] Validasi input lebih ketat
- [ ] Upload foto mahasiswa

## 📄 Lisensi

Proyek ini bebas digunakan untuk keperluan pembelajaran.

---

**Dibuat dengan ❤️ untuk pembelajaran Web Programming**
