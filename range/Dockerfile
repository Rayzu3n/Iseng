FROM php:8.4-apache

RUN docker-php-ext-install pdo_sqlite \
    && a2enmod headers

COPY php.ini /usr/local/etc/php/conf.d/securelab.ini
COPY index.php /var/www/html/index.php
COPY styles.css /var/www/html/styles.css

RUN mkdir -p /var/lib/lab \
    && chown -R www-data:www-data /var/lib/lab \
    && chmod 750 /var/lib/lab

EXPOSE 80
