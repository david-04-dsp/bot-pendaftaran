# Panduan Setup Bot Telegram

## 🤖 Informasi Bot
**Token Bot:** `8272439024:AAHiN3elpBlhM6PVs-QpP_oNpYfpd5LHrKc`

## 📋 File yang Dibuat:
1. `telegram_bot.php` - Handler untuk bot Telegram
2. `setup_webhook.php` - Setup webhook untuk bot
3. `proses.php` - Sudah diupdate dengan notifikasi Telegram

## 🚀 Cara Setup Bot (Pilih Salah Satu)

### Opsi 1: Testing Lokal dengan ngrok (Recommended)

#### Langkah 1: Install ngrok
1. Download dari: https://ngrok.com/download
2. Extract file ngrok.exe
3. (Opsional) Daftar akun gratis di ngrok untuk authtoken

#### Langkah 2: Jalankan ngrok
```powershell
# Buka terminal baru (jangan tutup terminal server PHP)
ngrok http 8000
```

Output akan seperti:
```
Forwarding  https://xxxx-xxx-xxx.ngrok-free.app -> http://localhost:8000
```

#### Langkah 3: Setup Webhook
1. Copy URL dari ngrok (contoh: `https://xxxx-xxx-xxx.ngrok-free.app`)
2. Buka browser: `http://localhost:8000/setup_webhook.php`
3. Paste URL ngrok + `/telegram_bot.php`
   - Contoh: `https://xxxx-xxx-xxx.ngrok-free.app/telegram_bot.php`
4. Klik "Set Webhook"

#### Langkah 4: Test Bot
1. Buka Telegram
2. Cari bot Anda (username yang dibuat di BotFather)
3. Klik START
4. Bot akan mengirim menu dengan tombol

---

### Opsi 2: Tanpa Webhook (Manual Testing)

Jika tidak ingin setup webhook, Anda bisa test bot dengan cara manual:

1. **Buka file `test_bot.php`** (akan dibuat di bawah)
2. Jalankan di browser untuk simulasi perintah bot
3. Bot akan mengirim link form langsung

---

### Opsi 3: Deploy ke Hosting

1. Upload semua file ke hosting dengan SSL (https)
2. Buka: `https://domain-anda.com/setup_webhook.php`
3. Masukkan URL: `https://domain-anda.com/telegram_bot.php`
4. Klik "Set Webhook"

## 📱 Cara Menggunakan Bot

### Perintah Bot:
- `/start` - Menampilkan menu utama dengan tombol
- `/daftar` - Mendapatkan link form pendaftaran
- `/data` - Melihat 5 mahasiswa terbaru
- `/statistik` - Melihat statistik pendaftaran
- `/info` - Informasi tentang bot

### Tombol Keyboard:
- 📝 Daftar Sekarang
- 📊 Lihat Data
- 📈 Statistik
- ℹ️ Info Bot

## 🔔 Notifikasi Otomatis

Untuk mendapatkan notifikasi saat ada pendaftaran baru:

1. **Dapatkan Chat ID Anda:**
   - Kirim pesan ke bot Anda
   - Buka: `https://api.telegram.org/bot8272439024:AAHiN3elpBlhM6PVs-QpP_oNpYfpd5LHrKc/getUpdates`
   - Cari `"chat":{"id":123456789}` - angka ini adalah chat ID Anda

2. **Edit file `proses.php`:**
   ```php
   $chat_id = '123456789'; // Ganti dengan chat ID Anda
   ```

3. Setiap ada pendaftaran baru, bot akan kirim notifikasi ke chat Anda!

## 🌐 Update URL Form di Bot

Edit file `telegram_bot.php` baris 7:

```php
// Jika menggunakan ngrok
define('WEB_FORM_URL', 'https://your-ngrok-url.ngrok-free.app/index.html');

// Jika di hosting
define('WEB_FORM_URL', 'https://domain-anda.com/index.html');
```

## ⚠️ Troubleshooting

### Bot tidak merespon
- Pastikan webhook sudah di-set dengan benar
- Cek URL webhook harus HTTPS
- Pastikan file `telegram_bot.php` bisa diakses publik

### Ngrok gratis expired
- URL ngrok gratis berubah setiap restart
- Setiap restart ngrok, setup webhook ulang dengan URL baru
- Atau gunakan ngrok berbayar untuk URL tetap

### Error saat set webhook
- Pastikan URL menggunakan HTTPS
- Pastikan file telegram_bot.php ada dan bisa diakses
- Cek token bot sudah benar

## 💡 Tips

1. **Testing Cepat**: Gunakan ngrok untuk testing lokal
2. **Production**: Deploy ke hosting dengan SSL/HTTPS
3. **Keamanan**: Jangan share token bot ke publik
4. **Chat ID**: Simpan chat ID admin untuk notifikasi

## 🎯 Next Steps

Setelah setup:
1. ✅ Test perintah /start di bot
2. ✅ Klik tombol "Daftar Sekarang"
3. ✅ Isi form pendaftaran
4. ✅ Cek notifikasi Telegram (jika sudah set chat_id)
5. ✅ Test perintah /data dan /statistik

---

**Selamat! Bot Telegram Anda siap digunakan! 🎉**
