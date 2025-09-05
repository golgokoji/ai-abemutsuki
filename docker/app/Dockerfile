FROM php:8.3-fpm-alpine

# 必要なライブラリ + PHPIZE_DEPS を追加
RUN apk add --no-cache git curl unzip libpng-dev icu-dev oniguruma-dev libzip-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo_mysql intl mbstring zip gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del --no-network $PHPIZE_DEPS

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 権限調整
ARG UID=1000
ARG GID=1000
RUN addgroup -g ${GID} app && adduser -G app -g "app" -s /bin/sh -D -u ${UID} app \
    && mkdir -p /var/www/html && chown -R app:app /var/www/html

USER app
WORKDIR /var/www/html
