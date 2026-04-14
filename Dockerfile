FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libonig-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli mbstring curl \
    && a2enmod rewrite

RUN echo "log_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "error_log = /dev/stderr" >> /usr/local/etc/php/php.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

COPY apache.conf /etc/apache2/sites-available/000-default.conf

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]