# 🔐 Passwordless Authentication - Panduan Penggunaan

## ✅ Sistem Sudah Diterapkan!

Sistem pendaftaran mahasiswa sekarang menggunakan **Passwordless Authentication** via Telegram. User tidak perlu mengingat username atau password!

## 🎯 Cara Kerja

### Metode 1: Via Bot Telegram (Recommended)

1. **Buka Bot Telegram**
   - Kunjungi: https://t.me/percobaan_pendaftaran_bot
   - Kirim `/start` ke bot

2. **Request Access**
   - Klik tombol **"📝 Daftar Sekarang"** atau ketik `/daftar`
   - Bot akan mengirim link akses khusus untuk Anda

3. **Klik Magic Link**
   - Klik link yang dikirim bot
   - Link akan otomatis memverifikasi identitas Anda
   - Anda langsung diarahkan ke form pendaftaran

4. **Isi Form**
   - Isi form pendaftaran
   - Submit
   - Selesai!

### Metode 2: Via Web

1. **Buka halaman web**
   - Akses: http://localhost:8000 atau http://localhost:8000/request_access.php

2. **Masukkan Telegram Username**
   - Ketik username Telegram Anda (contoh: `@daviduser`)
   - Atau masukkan nomor telepon yang terdaftar di Telegram

3. **Terima Link via Telegram**
   - Sistem akan mengirim link akses ke Telegram Anda
   - Link berlaku 30 menit dan hanya bisa digunakan 1 kali

4. **Klik Link & Isi Form**
   - Klik link dari Telegram
   - Otomatis login dan akses form
   - Isi dan submit form

## 🔒 Keamanan

✅ **Tidak ada password yang perlu diingat**
✅ **Link hanya berlaku 30 menit**
✅ **Link hanya bisa digunakan 1 kali**
✅ **Session berlaku 24 jam setelah login**
✅ **Link dikirim langsung ke Telegram user**

## 📋 Fitur Authentication

- ✅ Magic Link via Telegram
- ✅ Token-based authentication
- ✅ Session management
- ✅ Auto-expire tokens & sessions
- ✅ Protected form access
- ✅ No password required

## 🚀 Menjalankan Sistem

1. **Jalankan Web Server**
   ```bash
   cd "c:\Users\david\OneDrive\Desktop\IV-GASAL\Teknologi Multimedia\percobaan"
   C:\xampp\php\php.exe -S localhost:8000
   ```

2. **Jalankan Bot Telegram**
   ```bash
   cd "c:\Users\david\OneDrive\Desktop\IV-GASAL\Teknologi Multimedia\percobaan"
   C:\xampp\php\php.exe bot_polling.php
   ```

3. **Akses Sistem**
   - Web: http://localhost:8000
   - Bot: https://t.me/percobaan_pendaftaran_bot

## 📊 Database Schema

### Tabel `auth_tokens`
Menyimpan token untuk magic link
- `token` - Token unik untuk verifikasi
- `telegram_chat_id` - ID Telegram user
- `telegram_username` - Username Telegram
- `is_used` - Apakah token sudah digunakan
- `expires_at` - Waktu kadaluarsa
- `created_at` - Waktu dibuat

### Tabel `sessions`
Menyimpan sesi user yang sudah terautentikasi
- `session_id` - ID sesi unik
- `telegram_chat_id` - ID Telegram user
- `telegram_username` - Username Telegram
- `expires_at` - Waktu kadaluarsa (24 jam)
- `created_at` - Waktu dibuat

### Tabel `mahasiswa`
Data pendaftaran mahasiswa (existing)

## 🎓 Perbedaan dengan Sistem Lama

| Aspek | Sistem Lama | Sistem Baru (Passwordless) |
|-------|-------------|---------------------------|
| **Login** | Tidak ada | Ada, via Telegram |
| **Password** | Tidak ada | Tidak perlu password |
| **Verifikasi** | Tidak ada | Ada, via magic link |
| **Keamanan** | Rendah | Tinggi |
| **Akses Form** | Siapa saja | Hanya user terverifikasi |
| **Session** | Tidak ada | Ada, 24 jam |

## 💡 Kenapa Passwordless Authentication?

Sesuai feedback dosen:
> "Orang sakit yang ke rumah sakit sudah sulit jangan ditambah sulit hafal-hafalin username password."

**Solusi:**
- ✅ Tidak perlu mengingat password
- ✅ Cukup punya akses ke Telegram
- ✅ Lebih mudah dan user-friendly
- ✅ Lebih aman dari password yang lemah
- ✅ Mencegah password sharing

## 🔧 Troubleshooting

**Q: Link tidak dikirim ke Telegram?**
A: Pastikan Anda sudah chat `/start` dengan bot terlebih dahulu

**Q: Link sudah kadaluarsa?**
A: Request link baru, link hanya berlaku 30 menit

**Q: Tidak bisa akses form?**
A: Pastikan Anda sudah klik magic link dari Telegram

**Q: Session expired?**
A: Request link baru untuk login ulang

## 📝 Notes

- Sistem ini cocok untuk aplikasi pendaftaran, booking, appointment, dll
- Tidak perlu setup email server (menggunakan Telegram)
- User harus punya akun Telegram
- Link localhost hanya bisa diakses dari komputer yang sama (untuk production gunakan ngrok atau domain)

---

**Developed for:** Tugas Teknologi Multimedia  
**Features:** Passwordless Authentication via Telegram Bot  
**Security:** Token-based + Session Management
