@echo off
echo ========================================
echo   SETUP NGROK UNTUK BOT TELEGRAM
echo ========================================
echo.

REM Cek apakah ngrok ada
if exist "C:\ngrok\ngrok.exe" (
    echo [OK] ngrok ditemukan di C:\ngrok\
) else (
    echo [!] ngrok tidak ditemukan!
    echo.
    echo Download ngrok dari: https://ngrok.com/download
    echo Extract ke: C:\ngrok\
    echo.
    pause
    exit
)

echo.
echo Langkah-langkah:
echo 1. Script ini akan membuka ngrok
echo 2. Copy URL yang muncul (https://xxxx.ngrok-free.app)
echo 3. Paste URL tersebut saat diminta
echo.
pause

echo.
echo Membuka ngrok...
start cmd /k "cd C:\ngrok && ngrok.exe http 8000"

echo.
echo Tunggu sampai ngrok menampilkan URL...
timeout /t 5 /nobreak >nul

echo.
echo ========================================
set /p NGROK_URL="Paste URL ngrok Anda (contoh: https://abc123.ngrok-free.app): "

if "%NGROK_URL%"=="" (
    echo [!] URL tidak boleh kosong!
    pause
    exit
)

echo.
echo URL yang dimasukkan: %NGROK_URL%
echo.
echo Mengupdate file bot_polling.php...

REM Backup file lama
copy bot_polling.php bot_polling.php.bak >nul

REM Update URL di file PHP menggunakan PowerShell
powershell -Command "(Get-Content bot_polling.php) -replace \"define\('WEB_FORM_URL', '.*'\);\", \"define('WEB_FORM_URL', '%NGROK_URL%/index.html');\" | Set-Content bot_polling.php"

echo [OK] File berhasil diupdate!
echo.
echo ========================================
echo Sekarang:
echo 1. Stop bot jika sedang berjalan (Ctrl+C)
echo 2. Jalankan ulang: php bot_polling.php
echo 3. Test di Telegram dengan /start
echo ========================================
echo.
pause
