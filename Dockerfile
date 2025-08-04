FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    zip unzip libpng-dev libonig-dev libxml2-dev curl git \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Activar mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar los archivos del proyecto
COPY . /var/www/html

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Configurar Apache para usar la carpeta public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
