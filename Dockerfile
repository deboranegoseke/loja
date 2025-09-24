# ---------- estágio 1: instalar vendors (Composer) ----------
FROM composer:2 AS vendor
WORKDIR /app

# copia os manifests (sem o código) para cachear melhor
COPY composer.json composer.lock ./

# instala vendors sem dev; ignoramos pdo_pgsql só no build de vendors
# (no container final a extensão será instalada de verdade)
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader \
    --ignore-platform-req=ext-pdo_pgsql

# ---------- estágio 2: runtime PHP + Apache ----------
FROM php:8.2-apache

# libs e extensões necessárias
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git \
 && docker-php-ext-install pdo pdo_pgsql \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# copia seu app
COPY . .

# copia a pasta vendor do estágio composer
COPY --from=vendor /app/vendor ./vendor

# Apache serve a pasta public/
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# permissões mínimas
RUN chown -R www-data:www-data storage bootstrap/cache

# porta exposta
EXPOSE 80

# comando padrão do Apache
CMD ["apache2-foreground"]
