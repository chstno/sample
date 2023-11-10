# FROM php:8.2-fpm
FROM arm64v8/php:8.2-fpm

RUN apt-get update

RUN apt-get install -y libpq-dev libsqlite3-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \ 
    && docker-php-ext-install mysqli pgsql pdo pdo_mysql pdo_sqlite pdo_pgsql \
    && docker-php-ext-enable mysqli

RUN pecl install xdebug-3.2.1 \
    && docker-php-ext-enable xdebug


RUN apt-get install -y \
       git \
       curl \
       zip \
       unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get upgrade -y