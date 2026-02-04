FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar proyecto
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html
