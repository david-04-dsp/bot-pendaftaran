#!/bin/bash
# Start script untuk Render.com

echo "Starting Telegram Bot Registration System..."
echo "Environment: Production (Render.com)"
echo "PHP Version: $(php -v | head -n 1)"
echo "========================================="

# Jalankan PHP built-in server
php -S 0.0.0.0:${PORT:-8000}
