FROM php:8.1-apache

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql

# Habilitar mod_rewrite para Apache
RUN a2enmod rewrite

# Copiar configuración personalizada de PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copiar configuración de Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación desde src
COPY src/ /var/www/html/

# Establecer permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/assets/uploads \
    && mkdir -p /var/www/html/assets/uploads/avatares \
    && mkdir -p /var/www/html/assets/uploads/caratulas \
    && chmod -R 777 /var/www/html/assets/uploads

# Exponer puerto 80
EXPOSE 80

# Comando para iniciar Apache
CMD ["apache2-foreground"]