#!/bin/bash

echo "Starting PHP web server on port $PORT..."
# Start web server in background
php -S 0.0.0.0:$PORT -t . > /tmp/webserver.log 2>&1 &
WEBSERVER_PID=$!
echo "Web server started (PID: $WEBSERVER_PID)"

# Wait for web server to be ready
sleep 3

echo "Starting Telegram bot..."
# Start bot in foreground (keeps container alive)
php bot_polling.php
