# syntax=docker/dockerfile:1

########################
# 1) Builder: Composer #
########################
FROM composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./

RUN echo "----- composer.json -----" && cat composer.json && echo "------------------------"

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --ignore-platform-req=ext-pdo_mysql \
    --ignore-platform-req=ext-gd \
    --ignore-platform-req=ext-pdo_pgsql

#################################
# 2) Runtime: PHP + Apache 8.2  #
#################################
FROM php:8.2-apache-bookworm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        ca-certificates git unzip; \
    apt-get install -y --no-install-recommends --virtual .build-deps \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" gd pdo_mysql; \
    apt-get purge -y --auto-remove .build-deps; \
    rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
