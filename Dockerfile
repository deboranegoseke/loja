# syntax=docker/dockerfile:1

########################
# 1) Builder: Composer #
########################
FROM composer:2 AS vendor
WORKDIR /app

# Ajuda a evitar warning do Composer em root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copie apenas manifestos (melhor cache)
COPY composer.json composer.lock ./

# Instala vendors (ignora reqs de extensões só no BUILDER)
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

# Se seu app tiver autoload dev que gere arquivos, você pode rodar:
# RUN composer dump-autoload --no-dev --classmap-authoritative


#################################
# 2) Runtime: PHP + Apache (8.2) #
#################################
# Use a variante *bookworm* (mais estável/corrigida que a default)
FROM php:8.2-apache-bookworm

# Mantém a imagem limpa e com o mínimo de pacotes
# - Instala ferramentas básicas e certificados
# - Instala DEPENDÊNCIAS DE BUILD em meta pacote ".build-deps" e remove depois
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        ca-certificates \
        git \
        unzip; \
    apt-get install -y --no-install-recommends --virtual .build-deps \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" gd pdo_mysql; \
    # remove cabeçalhos/dev após compilar as extensões
    apt-get purge -y --auto-remove .build-deps; \
    rm -rf /var/lib/apt/lists/*

# Apache servindo a pasta public
RUN a2enmod rewrite \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copia o código e os vendors do estágio builder
COPY . .
COPY --from=vendor /app/vendor ./vendor

# Permissões mínimas
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
