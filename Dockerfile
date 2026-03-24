FROM php:8.1-fpm-alpine

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instalar herramientas adicionales
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

# Configurar PHP
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Directorio de trabajo
WORKDIR /var/www/html

# Exponer puerto
EXPOSE 9000

# Usuario
USER www-data

CMD ["php-fpm"]