FROM php:7.3-apache
LABEL maintainer="edmurcardoso@gmail.com"

RUN apt-get update && apt-get install --assume-yes --fix-missing libssl-dev libxml2-dev libicu-dev libsqlite3-dev libsqlite3-0 libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev libzip-dev zlib1g-dev git unzip supervisor wget redis-server redis npm
RUN docker-php-ext-install gd intl bcmath pdo pdo_sqlite mbstring opcache soap ctype json xml tokenizer zip

RUN npm install -g laravel-echo-server

WORKDIR /var/www/html/
RUN wget https://getcomposer.org/composer.phar
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . /var/www/html/
RUN chmod 777 -R /var/www/html/storage
RUN chmod 777 -R /var/www/html/bootstrap/cache
RUN chmod 777 -R /var/www/html/database

COPY apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY php.ini /usr/local/etc/php/php.ini
RUN a2enmod rewrite 
RUN a2enmod proxy_http
RUN service apache2 restart

RUN php composer.phar self-update
RUN php composer.phar install --no-interaction --no-dev --optimize-autoloader

RUN touch storage/database.sqlite
RUN chmod 777 storage/database.sqlite
RUN chgrp -R www-data /var/www/html
RUN php prep_env.php

RUN npm install
RUN npm run gulp

COPY jobs.conf /etc/supervisor/conf.d/jobs.conf

VOLUME ["/var/www/html/storage"]

EXPOSE 80
ENTRYPOINT redis-server & \
laravel-echo-server start & \
php artisan env:ensure && \
php artisan migrate --seed --force && \
php artisan cache:clear && \
php artisan view:clear && \
php artisan optimize && \
php artisan queue:flush && \
php artisan route:cache && \
supervisord && \
docker-php-entrypoint \
&& apache2-foreground