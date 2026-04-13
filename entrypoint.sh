#!/bin/bash
PORT=${PORT:-80}

echo "=== DEBUG ==="
echo "PORT variable: $PORT"
echo "============="

# Run migrations first
php /app/Migrate.php

# Fix Apache port binding
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/*:80/*:$PORT/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port $PORT"
exec apache2-foreground