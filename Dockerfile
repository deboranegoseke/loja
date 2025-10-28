########################
# 1) Builder: Composer #
########################
FROM public.ecr.aws/docker/library/composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./

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

# Gera autoload já otimizado (não roda scripts artisan)
RUN composer dump-autoload --optimize --no-scripts

#################################
# 2) Runtime: PHP + Apache 8.2  #
#################################
FROM public.ecr.aws/docker/library/php:8.2-apache-bookworm

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        ca-certificates git unzip \
        libpng16-16 libjpeg62-turbo libfreetype6; \
    buildDeps="libpng-dev libjpeg62-turbo-dev libfreetype6-dev"; \
    apt-get install -y --no-install-recommends $buildDeps; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" gd pdo_mysql bcmath exif pcntl; \
    apt-get purge -y --auto-remove $buildDeps; \
    rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

ENV PORT=8080
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

WORKDIR /var/www/html

# Copia o projeto e o vendor da build anterior
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=vendor /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

RUN php artisan config:clear || true \
 && php artisan cache:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

# Executa migrations (sem travar o deploy)
CMD php artisan migrate --force || true && apache2-foreground

EXPOSE ${PORT}
