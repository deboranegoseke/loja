########################
# 1) Builder: Composer #
########################
FROM public.ecr.aws/docker/library/composer:2 AS vendor
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY composer.json composer.lock ./

# (opcional) debug pra ver o que a Railway está lendo
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
FROM public.ecr.aws/docker/library/php:8.2-apache-bookworm

RUN set -eux; \
    apt-get update; \
    # utilitários básicos
    apt-get install -y --no-install-recommends \
        ca-certificates \
        git \
        unzip; \
    \
    # *** LIBS DE RUNTIME QUE FICAM NA IMAGEM ***
    apt-get install -y --no-install-recommends \
        libpng16-16 \
        libjpeg62-turbo \
        libfreetype6; \
    \
    # pacotes *-dev só para compilar as extensões
    buildDeps="libpng-dev libjpeg62-turbo-dev libfreetype6-dev"; \
    apt-get install -y --no-install-recommends $buildDeps; \
    \
    # compila gd com suporte a freetype e jpeg + pdo_mysql
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" gd pdo_mysql; \
    \
    # remove SOMENTE os *-dev; mantém as libs de runtime
    apt-get purge -y --auto-remove $buildDeps; \
    rm -rf /var/lib/apt/lists/*

# Apache servindo a pasta public/
RUN a2enmod rewrite \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor

# Permissões mínimas
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
