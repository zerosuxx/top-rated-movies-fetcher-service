FROM php:8.0-cli-alpine

ENV COMPOSER_VERSION='2.1.8'

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --version=$COMPOSER_VERSION --filename=composer

RUN adduser --disabled-password app

USER app

WORKDIR /app
