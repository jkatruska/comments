FROM php:8.0.12-fpm-alpine

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install pdo pdo_pgsql

RUN apk add yarn

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN wget https://github.com/symfony-cli/symfony-cli/releases/download/v5.4.1/symfony-cli_5.4.1_aarch64.apk

RUN apk add --allow-untrusted symfony-cli_5.4.1_aarch64.apk

RUN mkdir "/app"

WORKDIR '/app'

CMD symfony server:start --no-tls
