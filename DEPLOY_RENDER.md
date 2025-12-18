# 🚀 Panduan Deploy ke Render.com

## 📋 Persiapan

### 1. Buat Akun GitHub & Push Code
```bash
# Initialize git
git init
git add .
git commit -m "Initial commit - Telegram Bot Registration System"

# Buat repository baru di GitHub, lalu:
git remote add origin https://github.com/USERNAME/REPO-NAME.git
git branch -M main
git push -u origin main
```

### 2. Daftar ke Render.com
- Buka https://render.com
- Sign up dengan GitHub account
- Authorize Render untuk akses GitHub

---

## 🔧 Setup di Render.com

### Step 1: Deploy Web Service (untuk Form)

1. **Klik "New +" → "Web Service"**
2. **Connect Repository:**
   - Pilih repository yang baru dibuat
   - Klik "Connect"

3. **Konfigurasi:**
   ```
   Name: mahasiswa-registration-web
   Environment: PHP
   Build Command: (kosongkan)
   Start Command: php -S 0.0.0.0:$PORT
   Instance Type: Free
   ```

4. **Environment Variables:**
   Tambahkan di tab "Environment":
   ```
   BOT_TOKEN = 7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU
   BASE_URL = https://mahasiswa-registration-web.onrender.com (akan muncul setelah deploy)
   ```

5. **Deploy!**
   - Klik "Create Web Service"
   - Tunggu ~5 menit untuk build & deploy
   - Setelah selesai, copy URL-nya

### Step 2: Update BASE_URL

1. Setelah web service jalan, copy URL (contoh: `https://mahasiswa-registration-web.onrender.com`)
2. Update environment variable `BASE_URL` dengan URL tersebut
3. Klik "Manual Deploy" → "Clear build cache & deploy"

### Step 3: Deploy Bot Worker

1. **Klik "New +" → "Background Worker"**
2. **Connect Repository yang sama**
3. **Konfigurasi:**
   ```
   Name: telegram-bot-worker
   Environment: PHP
   Build Command: (kosongkan)
   Start Command: php bot_polling.php
   Instance Type: Free
   ```

4. **Environment Variables:**
   ```
   BOT_TOKEN = 7927742319:AAEaqUao75k4xfAolBM0DAbin9PhiS13GHU
   BASE_URL = https://mahasiswa-registration-web.onrender.com
   ```

5. **Deploy!**

---

## ✅ Verifikasi

### Cek Web Service:
- Buka `https://YOUR-WEB-URL.onrender.com/index.html`
- Harus bisa diakses

### Cek Bot:
1. Buka Telegram
2. Kirim `/start` ke bot
3. Bot harus merespon!

### Cek Logs:
- Di dashboard Render, klik service → Tab "Logs"
- Lihat apakah ada error

---

## 🐛 Troubleshooting

### Problem: Web service error
**Solution:** 
- Cek logs di Render dashboard
- Pastikan `BASE_URL` sudah benar
- Pastikan file `index.html` ada

### Problem: Bot tidak respon
**Solution:**
- Cek logs worker
- Pastikan `BOT_TOKEN` benar
- Pastikan worker service running (bukan sleep)

### Problem: Database error
**Solution:**
- SQLite berjalan di memory/local
- Data akan reset setiap deploy
- **Recommended:** Upgrade ke PostgreSQL (gratis di Render)

---

## 📊 Database Migration (Optional - Recommended)

Render menyediakan PostgreSQL gratis! Untuk data persistent:

### 1. Buat PostgreSQL Database:
- New + → PostgreSQL
- Name: `mahasiswa-db`
- Free tier

### 2. Update config.php untuk support PostgreSQL:
```php
// Cek environment
if (getenv('DATABASE_URL')) {
    // PostgreSQL (Production)
    $conn = new PDO(getenv('DATABASE_URL'));
} else {
    // SQLite (Development)
    $conn = new PDO('sqlite:mahasiswa.db');
}
```

---

## 🎉 Selesai!

Bot Anda sekarang **online 24/7** di Render.com!

**URLs:**
- Web Form: `https://mahasiswa-registration-web.onrender.com`
- Bot Status: Cek di Render dashboard

**Free Tier Limits:**
- 750 jam/bulan
- Cold start setelah 15 menit tidak ada traffic
- Cukup untuk testing & light usage

---

## 📝 Notes

1. **Cold Start:** Free tier akan sleep setelah 15 menit tidak ada traffic
2. **Keep Alive:** Bisa pakai cron job untuk ping setiap 10 menit
3. **Upgrade:** Jika perlu always-on, upgrade ke paid plan ($7/bulan)

---

**Butuh bantuan?** Hubungi saya jika ada error saat deploy!
