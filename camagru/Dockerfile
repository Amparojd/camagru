FROM php:8.2-apache

# Habilitar módulos de Apache necesarios
RUN a2enmod rewrite
RUN a2enmod headers

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Configurar el DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Actualizar la configuración de Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html