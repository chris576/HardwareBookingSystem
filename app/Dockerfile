# Basisimage
FROM php:8.1-fpm

# Installiere Abhängigkeiten
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install \
    pdo_mysql \
    pdo_pgsql \
    intl

# Setze das Arbeitsverzeichnis
WORKDIR /var/www/html

# Kopiere die Dateien der Symfony-Anwendung in den Container
COPY . .

# Installiere Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installiere die Composer-Abhängigkeiten
RUN composer install --no-scripts --no-autoloader

# Setze die Dateiberechtigungen
RUN chown -R www-data:www-data /var/www/html/var

# Exponiere den Container-Port
EXPOSE 9000

# Starte den PHP-FPM-Server
CMD ["php-fpm"]