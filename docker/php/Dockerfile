# --- 1) Composer bağımlılıkları (PhpSpreadsheet) ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader \
        --ignore-platform-req=ext-*

# --- 2) Uygulama: PHP 8.3 + Apache ---
FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev libpng-dev \
    && docker-php-ext-install -j"$(nproc)" mysqli zip gd \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
# Varsayılan prod ayarları; dev'de compose bu dosyayı php.ini-dev ile ezer
COPY docker/php/php.ini-prod /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor

RUN mkdir -p excel && chown www-data:www-data excel
