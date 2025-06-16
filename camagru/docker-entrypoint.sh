#!/bin/bash

# Configurar hostname
echo "127.0.0.1 localhost localhost.localdomain $(hostname)" >> /etc/hosts
echo "localhost" > /etc/mailname

# Crear directorios necesarios
mkdir -p /var/spool/mail /var/spool/mqueue
chmod 755 /var/spool/mail /var/spool/mqueue

# Configurar sendmail básico
echo "define(\`SMART_HOST',\`[127.0.0.1]')dnl
MAILER(\`local')dnl
MAILER(\`smtp')dnl" > /etc/mail/sendmail.mc

# Generar configuración
m4 /etc/mail/sendmail.mc > /etc/mail/sendmail.cf

# Iniciar sendmail
sendmail -bd
service sendmail restart

# Ejecutar comando original
exec "$@"