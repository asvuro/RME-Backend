FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        libzip-dev libicu-dev libonig-dev \
    && docker-php-ext-install \
        pdo_mysql bcmath mbstring intl zip opcache pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY docker/loadtest/opcache.ini /usr/local/etc/php/conf.d/zz-loadtest-opcache.ini

WORKDIR /var/www

RUN sed -i 's/^listen = .*/listen = 127.0.0.1:9001/' /usr/local/etc/php-fpm.d/docker.conf \
    && sed -i 's/^pm.max_children = .*/pm.max_children = 20/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^pm.start_servers = .*/pm.start_servers = 4/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 2/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 8/' /usr/local/etc/php-fpm.d/www.conf
