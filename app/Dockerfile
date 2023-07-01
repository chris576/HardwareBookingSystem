# Basisimage
FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev

# Installiere die PHP-Erweiterungen
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    zip

WORKDIR /var/www/html/

COPY . .

# Installiere Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installiere die Composer-Abhängigkeiten
RUN composer install

# Generiere den Autoloader
RUN composer dump-autoload --optimize

# Setze die Dateiberechtigungen
RUN chown -R www-data:www-data ./var

# Exponiere den Container-Port
EXPOSE 9000

# Starte den PHP-FPM-Server
CMD ["php-fpm"]