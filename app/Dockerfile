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

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -

RUN apt-get install -y nodejs npm
RUN npm install -g yarn

WORKDIR /var/www/html

COPY . .

# Installiere Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash

RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Installiere die Composer-Abhängigkeiten
RUN composer install

# Setze die Dateiberechtigungen
RUN chown -R www-data:www-data /var/www/html/var

RUN symfony run yarn add webpack-notifier@^1.15.0 --dev

RUN symfony run -d yarn encore dev

RUN yarn build

# Exponiere den Container-Port
EXPOSE 9000

# Starte den PHP-FPM-Server
CMD ["php-fpm"]