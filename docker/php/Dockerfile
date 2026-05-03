FROM php:8.5-fpm-alpine3.23

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apk add --no-cache \
    curl \
    autoconf \
    build-base \
    bash \
    git \
    pcre-dev \
    linux-headers \
    libzip-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    icu-dev

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apk --no-cache add shadow && \
    groupmod -o -g ${GROUP_ID} www-data && \
    usermod -o -u ${USER_ID} -g www-data www-data

RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& chmod +x /usr/local/bin/composer

WORKDIR /var/www

USER www-data

CMD ["php-fpm"]
