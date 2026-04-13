#!/bin/bash
PORT=${PORT:-80}

echo "=== DEBUG ==="
echo "PORT variable: $PORT"
echo "============="

# Fix MPM conflict at runtime
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Run migrations
php /app/Migrate.php

# Fix Apache port binding
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/*:80/*:$PORT/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port $PORT"
exec apache2-foreground