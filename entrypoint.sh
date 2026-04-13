#!/bin/bash
PORT=${PORT:-80}

echo "=== DEBUG ==="
echo "PORT variable: $PORT"
echo "ports.conf before:"
cat /etc/apache2/ports.conf
echo "============="

sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf
sed -i "s/*:80/*:$PORT/" /etc/apache2/sites-available/000-default.conf

echo "ports.conf after:"
cat /etc/apache2/ports.conf
echo "Starting Apache on port $PORT"

exec apache2-foreground