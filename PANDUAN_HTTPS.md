# Cara Membuat Link HTTPS untuk Bot Telegram

## 🔒 Menggunakan ngrok (Gratis & Mudah)

### Langkah 1: Download ngrok
1. Buka: https://ngrok.com/download
2. Pilih **Windows (64-bit)**
3. Download file `ngrok.zip`
4. Extract ke folder: `C:\ngrok\`

### Langkah 2: (Opsional) Daftar Akun Gratis
1. Daftar di: https://dashboard.ngrok.com/signup
2. Login dan copy **authtoken** Anda
3. Jalankan di PowerShell:
```powershell
C:\ngrok\ngrok.exe config add-authtoken YOUR_AUTHTOKEN
```

### Langkah 3: Jalankan ngrok
Buka **PowerShell baru** (jangan tutup server PHP & bot):

```powershell
cd C:\ngrok
.\ngrok.exe http 8000
```

### Langkah 4: Copy URL HTTPS
Ngrok akan menampilkan:
```
Session Status    online
Forwarding        https://abc123.ngrok-free.app -> http://localhost:8000
```

**Copy URL HTTPS** (contoh: `https://abc123.ngrok-free.app`)

### Langkah 5: Update Bot dengan URL ngrok
Buka file: `bot_polling.php`

Cari baris:
```php
define('WEB_FORM_URL', 'http://localhost:8000/index.html');
```

Ganti dengan URL ngrok Anda:
```php
define('WEB_FORM_URL', 'https://abc123.ngrok-free.app/index.html');
```

### Langkah 6: Restart Bot
1. Stop bot (Ctrl+C di terminal bot)
2. Jalankan ulang: `C:\xampp\php\php.exe bot_polling.php`

### Langkah 7: Test di Telegram
1. Ketik `/start`
2. Klik "📝 Daftar Sekarang"
3. Link HTTPS akan berfungsi dan aman!

---

## 🎯 Keuntungan HTTPS dengan ngrok:

✅ Link bisa dibuka dari Telegram langsung
✅ Aman dengan enkripsi SSL/HTTPS
✅ Bisa diakses dari HP/device lain
✅ Gratis untuk testing
✅ Tidak perlu setup SSL certificate manual

---

## ⚠️ Catatan Penting:

- **URL ngrok gratis berubah** setiap restart
- Setiap restart ngrok, update `bot_polling.php` dengan URL baru
- Untuk URL tetap, perlu akun ngrok berbayar
- Atau deploy ke hosting dengan SSL (production)

---

## 🚀 Alternatif Lain:

### 1. Localhost.run (Gratis, tanpa install)
```powershell
ssh -R 80:localhost:8000 nokey@localhost.run
```

### 2. Serveo (Gratis, tanpa install)
```powershell
ssh -R 80:localhost:8000 serveo.net
```

### 3. Deploy ke Hosting (Production)
- Heroku (gratis tier)
- Railway.app
- Vercel
- Hosting cPanel dengan SSL

---

**Saya sarankan pakai ngrok karena paling mudah dan stabil!**

Mau saya buatkan script otomatis untuk update URL ngrok?
