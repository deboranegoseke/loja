########################
# 1) Builder: Composer #
########################
FROM public.ecr.aws/docker/library/composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./

# Instala dependências PHP (sem exigir extensões ausentes)
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
FROM public.ecr.aws/docker/library/php:8.2-apache-bookworm

# Instala pacotes essenciais e extensões PHP necessárias ao Laravel
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

# Configura Apache para servir a pasta "public"
RUN a2enmod rewrite \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Porta dinâmica para o Railway
ENV PORT=8080
RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

# Copia o projeto Laravel e as dependências do Composer
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor

# Gera autoload otimizado (sem executar scripts do Laravel)
RUN composer dump-autoload --optimize --no-scripts

# Corrige permissões de pastas de cache
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Limpa e prepara caches do Laravel
RUN php artisan config:clear || true \
 && php artisan cache:clear || true \
 && php artisan route:clear || true \
 && php artisan view:clear || true

# Executa migrations automaticamente ao iniciar (sem travar em erro)
CMD php artisan migrate --force || true && apache2-foreground

EXPOSE ${PORT}
