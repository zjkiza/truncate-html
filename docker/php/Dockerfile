ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-cli

ARG SYSTEM_TIMEZONE=UTC
ARG APP_ENV=dev
ARG PHP_XDEBUG_ENABLE=1
ARG PHP_XDEBUG_MODE=debug
ARG PHP_XDEBUG_REMOTE_PORT=9003
ARG PHP_XDEBUG_REMOTE_ADDRESS=host.docker.internal
ARG PHP_XDEBUG_IDEKEY=PHPSTORM
ARG PHP_IDE_CONFIG="serverName=localhost"
ARG UID=1000
ARG GID=1000

ENV TZ=${SYSTEM_TIMEZONE} \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer \
    PATH=/composer/vendor/bin:$PATH \
    PHP_XDEBUG_ENABLE=${PHP_XDEBUG_ENABLE} \
    PHP_XDEBUG_MODE=${PHP_XDEBUG_MODE} \
    PHP_XDEBUG_REMOTE_PORT=${PHP_XDEBUG_REMOTE_PORT}\
    PHP_XDEBUG_REMOTE_ADDRESS=${PHP_XDEBUG_REMOTE_ADDRESS} \
    PHP_XDEBUG_IDEKEY=${PHP_XDEBUG_IDEKEY} \
    PHP_IDE_CONFIG=${PHP_IDE_CONFIG} \
    SYSTEM_TIMEZONE=${SYSTEM_TIMEZONE}

RUN apt-get update --fix-missing && apt-get update && apt-get install -y \
    git \
    zip \
    nano \
    gettext \
    openssl \
     && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug

RUN docker-php-ext-install pcntl

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
 && echo ${TZ} > /etc/timezone

COPY docker/php/conf /tmp
RUN chmod +x /tmp/configure.sh && /tmp/configure.sh && rm -rf /tmp/*

#   Holds a raised container
COPY docker/php/cmd.sh /usr/local/bin/cmd.sh
RUN chmod +x /usr/local/bin/cmd.sh

# -o allows non-unique IDs: the host UID/GID may already exist in the image (e.g. gid 20 on macOS).
RUN groupadd -o -g ${GID} app \
 && useradd -o -u ${UID} -g app -m app

# COMPOSER_HOME points to /composer, which must be writable by the unprivileged user (cache, global packages).
RUN mkdir -p /composer \
 && chown -R app:app /composer

USER app

WORKDIR /www

# at least one of the files must exist, that is, thanks to the "*" not all the listed files
COPY --chown=app:app composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader
COPY --chown=app:app . .
RUN composer dump-autoload --optimize

CMD ["/usr/local/bin/cmd.sh"]