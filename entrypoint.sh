#!/bin/bash
PORT=${PORT:-80}

echo "=== DEBUG ==="
echo "PORT variable: $PORT"
echo "============="

a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

php /app/Migrate.php

sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/*:80/*:$PORT/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port $PORT"

# Create log file and stream it
mkdir -p /var/log/apache2
touch /var/log/apache2/error.log
tail -f /var/log/apache2/error.log &

exec apache2-foreground