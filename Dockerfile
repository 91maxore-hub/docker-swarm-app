FROM php:8.2-apache

# Aktivera Apache mod_rewrite (ej nödvändigt men bra att ha)
RUN a2enmod rewrite

# Kopiera alla filer
COPY . /var/www/html/

# Öppna port 80 (standard)
EXPOSE 80