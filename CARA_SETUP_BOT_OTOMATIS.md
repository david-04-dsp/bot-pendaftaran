# Cara Setup Bot Telegram Otomatis

## 🎯 Agar bot merespon otomatis saat user klik /start

Bot Telegram perlu **webhook** untuk merespon otomatis. Ada 2 cara:

---

## ✅ Cara 1: Menggunakan ngrok (GRATIS & MUDAH)

### Langkah 1: Download & Install ngrok
1. Buka: https://ngrok.com/download
2. Download untuk Windows
3. Extract file `ngrok.exe` 
4. Pindahkan ke folder: `C:\ngrok\ngrok.exe`

### Langkah 2: Jalankan ngrok
Buka PowerShell/CMD baru (JANGAN tutup server PHP):

```powershell
C:\ngrok\ngrok.exe http 8000
```

Atau jika sudah di PATH:
```powershell
ngrok http 8000
```

### Langkah 3: Copy URL ngrok
Ngrok akan menampilkan:
```
Forwarding  https://xxxx-xxx-xxx.ngrok-free.app -> http://localhost:8000
```

**Copy URL** yang https (contoh: `https://1234-56-78.ngrok-free.app`)

### Langkah 4: Setup Webhook
1. Buka browser: http://localhost:8000/setup_webhook.php
2. Paste URL ngrok + `/telegram_bot.php`
   - Contoh: `https://1234-56-78.ngrok-free.app/telegram_bot.php`
3. Klik "Set Webhook"

### Langkah 5: Test Bot
1. Buka Telegram
2. Kirim `/start` ke bot
3. **Bot langsung balas otomatis!** 🎉

---

## ✅ Cara 2: Tanpa ngrok (Alternatif Cepat)

Jika tidak mau install ngrok, gunakan **Polling** (bot cek pesan sendiri):

Saya akan buat file `bot_polling.php` yang bisa dijalankan di terminal dan bot akan otomatis merespon!

---

## 📝 Mau pakai cara yang mana?

**Cara 1 (ngrok):** Lebih profesional, seperti bot production
**Cara 2 (polling):** Lebih mudah, tidak perlu install apa-apa

Ketik pilihan Anda atau saya buatkan polling sekarang?
