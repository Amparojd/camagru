FROM php:8.2-apache

# Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    sendmail \
    sendmail-cf \
    mailutils \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configurar PHP y sendmail
RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini

# Configuración única de sendmail
RUN echo "define(\`LOCAL_DOMAIN', \`localhost')dnl" > /etc/mail/sendmail.mc && \
    echo "define(\`SMART_HOST',\`[127.0.0.1]')dnl" >> /etc/mail/sendmail.mc && \
    echo "FEATURE(\`local_procmail')dnl" >> /etc/mail/sendmail.mc && \
    echo "FEATURE(\`always_add_domain')dnl" >> /etc/mail/sendmail.mc && \
    echo "MASQUERADE_AS(\`localhost')dnl" >> /etc/mail/sendmail.mc && \
    echo "MAILER(\`local')dnl" >> /etc/mail/sendmail.mc && \
    echo "MAILER(\`smtp')dnl" >> /etc/mail/sendmail.mc && \
    m4 /etc/mail/sendmail.mc > /etc/mail/sendmail.cf

# Configurar el DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Actualizar la configuración de Apache
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Script de inicio
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]