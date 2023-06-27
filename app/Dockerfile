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

# Installieren Sie Node.js und npm
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -

RUN apt-get install -y nodejs npm

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

# Installiere Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installiere die Composer-Abhängigkeiten
RUN composer install --no-scripts --no-autoloader

# Setze die Dateiberechtigungen
RUN chown -R www-data:www-data /app/var

# Exponiere den Container-Port
EXPOSE 9000

# Starte den PHP-FPM-Server
CMD ["php-fpm"]