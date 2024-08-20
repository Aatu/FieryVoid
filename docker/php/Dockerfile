FROM php:8.2-fpm

RUN apt update && apt install -y coreutils findutils sed bash rsync inotify-tools
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /usr/src/fieryvoid

RUN curl -sS https://getcomposer.org/composer.phar > composer.phar \
    && ln -s /usr/src/current/docker/php/php.ini /usr/local/etc/php/php.ini \
    && rm /usr/local/etc/php-fpm.d/docker.conf \
    && ln -s /usr/src/current/docker/php/fpm.conf /usr/local/etc/php-fpm.d/docker.conf \
    && mkdir -p source/server \
    && ln -s ../../docker/php/varconfig.php source/server/varconfig.php

VOLUME /usr/src/current /usr/src/fieryvoid/vendor /root/.composer

CMD /usr/src/current/docker/php/start.sh
