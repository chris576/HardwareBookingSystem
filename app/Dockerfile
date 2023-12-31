# Erste Build-Stufe: Composer-Installation und PHP-Abhängigkeiten

FROM php:8.1-fpm AS composer

RUN apt-get update && apt-get install -y \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev

# Installiere die PHP-Erweiterungen
RUN docker-php-ext-install \
    intl \
    zip \
    pdo_mysql

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

# Zweite Build-Stufe: Node.js-Installation und Yarn-Build

FROM node:latest AS node

WORKDIR /var/www/html/

COPY --from=composer /var/www/html/ .

RUN yarn install

RUN yarn encore dev


# Finale Build-Stufe: PHP-FPM-Server und Container-Konfiguration

FROM composer

WORKDIR /var/www/html/

COPY --from=node /var/www/html/ .
 
# Starte den PHP-FPM-Server
CMD ["php-fpm"]