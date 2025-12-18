# 🔐 Passwordless Authentication via Telegram - Konsep Sederhana

## Overview
Sistem autentikasi **tanpa password** yang menggunakan verifikasi via push notification Telegram. User mengisi form terlebih dahulu, kemudian menerima notifikasi untuk verifikasi.

## Cara Kerja

### 1. User Mengisi Form Pendaftaran
- Akses form di: `http://localhost:8000/index_form.php`
- Form terbuka untuk umum (tidak perlu login/authentication terlebih dahulu)
- User mengisi data:
  - Nama
  - NIM
  - Email
  - Telepon
  - Jenis Kelamin
  - Jurusan
  - Alamat
  - **Telegram Username** (opsional) - bisa username (@david) atau nama (David)

### 2. Submit Form
- Data tersimpan ke database dengan status `pending`
- System generate `verification_token` unik
- System mencari `chat_id` user dari Telegram berdasarkan username/nama

### 3. Push Notification Dikirim
Sistem otomatis mengirim push notification ke Telegram user dengan:
- 📝 Ringkasan data yang didaftarkan
- 🔗 Link verifikasi unik
- ⏰ Link valid 24 jam

### 4. User Verifikasi
- User klik link di Telegram
- Browser membuka halaman verifikasi
- Status berubah dari `pending` → `verified`
- Timestamp `verified_at` dicatat

## Keuntungan Konsep Ini

### ✅ Tanpa Password
- User tidak perlu mengingat password
- Tidak ada proses registrasi account
- Cocok untuk pasien rumah sakit (orang sakit tidak perlu ribet)

### ✅ Keamanan Two-Factor
- Verifikasi via kepemilikan Telegram/HP
- Token unik per pendaftaran
- Link sekali pakai

### ✅ User Experience Sederhana
1. Isi form → Submit
2. Buka Telegram → Klik link
3. Selesai!

## Files yang Terlibat

### 1. `index_form.php` - Form Pendaftaran
```php
// Form terbuka tanpa session check
// Ada field telegram_username (opsional)
<input type="text" name="telegram_username" placeholder="@username atau David (opsional)">
```

### 2. `proses.php` - Processing Form
```php
// Simpan data dengan status 'pending'
// Generate verification_token
// Kirim push notification via sendVerificationToTelegram()
```

### 3. `verify_registration.php` - Endpoint Verifikasi
```php
// Terima token dari URL: ?token=abc123...
// Update status → 'verified'
// Set verified_at timestamp
// Tampilkan halaman sukses
```

### 4. Database Schema
```sql
CREATE TABLE mahasiswa (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nama TEXT NOT NULL,
    nim TEXT UNIQUE NOT NULL,
    email TEXT NOT NULL,
    telepon TEXT NOT NULL,
    jenis_kelamin TEXT NOT NULL,
    jurusan TEXT NOT NULL,
    alamat TEXT NOT NULL,
    telegram_username TEXT,           -- Username/nama Telegram
    verification_status TEXT DEFAULT 'pending',  -- pending | verified
    verification_token TEXT,           -- Token unik untuk verifikasi
    verified_at DATETIME,              -- Waktu verifikasi
    tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP
)
```

## Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│  1. USER AKSES FORM                                         │
│     http://localhost:8000/index_form.php                    │
│     - Tidak perlu login                                     │
│     - Langsung isi form                                     │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│  2. SUBMIT FORM → proses.php                                │
│     - Data tersimpan (status: PENDING)                      │
│     - Generate verification_token                           │
│     - Cari chat_id dari Telegram                            │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│  3. PUSH NOTIFICATION KE TELEGRAM                           │
│     🔐 Verifikasi Pendaftaran                               │
│     Halo David!                                             │
│     Anda baru saja melakukan pendaftaran dengan data:       │
│     📝 NIM: 123456                                          │
│     📧 Email: david@email.com                               │
│     📱 Telepon: 08123456789                                 │
│                                                             │
│     ⚠️ Klik link untuk verifikasi:                          │
│     🔗 http://localhost:8000/verify_registration.php?token=...│
└─────────────────┬───────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│  4. USER KLIK LINK                                          │
│     - Browser buka verify_registration.php                  │
│     - Validasi token                                        │
│     - Update status → 'verified'                            │
│     - Set verified_at = now()                               │
└─────────────────┬───────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────────────────┐
│  5. HALAMAN SUKSES                                          │
│     ✓ Verifikasi Berhasil!                                  │
│     ✓ TERVERIFIKASI                                         │
│     Data pendaftaran Anda telah diaktifkan                  │
└─────────────────────────────────────────────────────────────┘
```

## Cara Test

### 1. Pastikan Bot & Server Running
```bash
# Terminal 1: Bot Polling
C:\xampp\php\php.exe bot_polling.php

# Terminal 2: Web Server
C:\xampp\php\php.exe -S localhost:8000
```

### 2. Akses Form
- Buka: `http://localhost:8000/index_form.php`

### 3. Isi Form
- Isi semua data
- Di field Telegram Username, masukkan: `David` atau `@username_kamu`

### 4. Submit
- Klik "Daftar"
- Akan muncul halaman: "Pendaftaran Berhasil! Status: Menunggu Verifikasi"

### 5. Cek Telegram
- Buka aplikasi Telegram
- Akan ada push notification dari bot
- Klik link verifikasi

### 6. Verifikasi Selesai
- Browser buka halaman verifikasi
- Status berubah menjadi ✓ TERVERIFIKASI

### 7. Lihat Data
- Akses: `http://localhost:8000/tampil.php`
- Lihat status verifikasi di tabel

## Troubleshooting

### Push Notification Tidak Masuk
**Penyebab**: Bot tidak menemukan chat_id user

**Solusi**:
1. Chat dengan bot di Telegram: `/start`
2. Pastikan username di form sesuai dengan Telegram
3. Cek bot_polling.php masih running

### Link Verification Error
**Penyebab**: Token tidak valid atau sudah digunakan

**Solusi**:
1. Pastikan link belum pernah diklik sebelumnya
2. Cek database: `verification_status` harus `pending`
3. Submit form ulang untuk generate token baru

### Localhost URL di Telegram
**Catatan**: Link localhost hanya bisa dibuka di device yang sama dengan server

**Untuk Production**:
- Ganti localhost dengan domain/IP publik
- Atau gunakan ngrok untuk tunnel: `ngrok http 8000`

## Keamanan

### Token Security
- Token 64 karakter (32 bytes hex)
- Cryptographically random via `random_bytes()`
- Sekali pakai (tidak bisa dipakai lagi setelah verified)

### Expiry
- Default: Link valid selamanya (sampai di-verify)
- Bisa tambahkan expiry 24 jam dengan:
```php
// Di verify_registration.php
$stmt = $conn->prepare("
    SELECT * FROM mahasiswa 
    WHERE verification_token = :token 
    AND verification_status = 'pending'
    AND tanggal_daftar > datetime('now', '-24 hours')
");
```

## Kustomisasi

### Ubah Pesan Telegram
Edit fungsi `sendVerificationToTelegram()` di `proses.php`:
```php
$message = "🔐 <b>Custom Message</b>\n\n";
$message .= "Text Anda di sini...\n";
```

### Tambah Field di Form
1. Tambah input di `index_form.php`
2. Tambah kolom di database schema (`config.php`)
3. Update INSERT query di `proses.php`
4. Update tampilan di `verify_registration.php`

## Kesimpulan

Sistem passwordless authentication ini:
- ✅ **Mudah** untuk user (tidak perlu hafal password)
- ✅ **Aman** (verifikasi via Telegram ownership)
- ✅ **Praktis** untuk kasus rumah sakit (pasien tidak ribet)
- ✅ **Modern** (push notification real-time)

Cocok untuk profesor yang menekankan:
> "Orang sakit yang ke rumah sakit sudah sulit, jangan ditambah sulit hafal-hafalin username password"
