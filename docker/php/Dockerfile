FROM php:8.4-cli

ARG SYSTEM_TIMEZONE=UTC
ARG APP_ENV=dev
ARG PHP_XDEBUG_ENABLE=1
ARG PHP_XDEBUG_MODE=debug
ARG PHP_XDEBUG_REMOTE_PORT=9003
ARG PHP_XDEBUG_REMOTE_ADDRESS=host.docker.internal
ARG PHP_XDEBUG_IDEKEY=PHPSTORM
ARG PHP_IDE_CONFIG="serverName=localhost"

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
    openssh-client \
     && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug

RUN docker-php-ext-install pcntl

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
 && echo ${TZ} > /etc/timezone

#   Add GitHub to known hosts
RUN mkdir -p /root/.ssh \
 && chmod 700 /root/.ssh \
 && ssh-keyscan github.com >> /root/.ssh/known_hosts

COPY docker/php/conf /tmp
RUN chmod +x /tmp/configure.sh && /tmp/configure.sh && rm -rf /tmp/*

WORKDIR /www
COPY --chown=${LOCAL_USER} . /www

#   Holds a raised container
COPY docker/php/cmd.sh /usr/local/bin/cmd.sh
RUN chmod +x /usr/local/bin/cmd.sh

CMD ["/bin/bash", "-c", "/usr/local/bin/cmd.sh"]