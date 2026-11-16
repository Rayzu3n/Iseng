FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && a2enmod headers \
    && rm -rf /var/lib/apt/lists/*

COPY php.ini /usr/local/etc/php/conf.d/securelab.ini
COPY index.php /var/www/html/index.php
COPY styles.css /var/www/html/styles.css

RUN mkdir -p /var/lib/lab \
    && chown -R www-data:www-data /var/lib/lab \
    && chmod 750 /var/lib/lab

EXPOSE 80
