#!/bin/bash

# Start PHP web server in background
php -S 0.0.0.0:${PORT:-80} > /tmp/webserver.log 2>&1 &
echo "Web server started on port ${PORT:-80}"

# Wait a bit for web server to start
sleep 2

# Start bot in foreground
echo "Starting bot..."
php bot_polling.php
