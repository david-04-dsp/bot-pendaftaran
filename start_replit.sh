#!/bin/bash

# Get port from Replit environment
if [ -z "$PORT" ]; then
  PORT=8080
fi

echo "Starting web server on port $PORT..."
# Start PHP web server in background
php -S 0.0.0.0:$PORT -t . > /tmp/webserver.log 2>&1 &
WEBSERVER_PID=$!

echo "Web server started on port $PORT (PID: $WEBSERVER_PID)"
sleep 2

# Start bot in foreground
echo "Starting bot..."
php bot_polling.php
