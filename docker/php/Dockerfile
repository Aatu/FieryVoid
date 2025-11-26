FROM php:8.2-fpm

# Set bash as the default shell for better interactive experience
SHELL ["/bin/bash", "-c"]

RUN apt update && apt install -y coreutils findutils sed bash rsync inotify-tools libzip-dev \
    && ln -sf /bin/bash /bin/sh
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install zip && docker-php-ext-enable zip

# Configure bash history persistence
ENV HISTFILE=/root/.bash_history_volume/.bash_history
ENV HISTSIZE=10000
ENV HISTFILESIZE=10000
# Force immediate history saving
ENV PROMPT_COMMAND="history -a; $PROMPT_COMMAND"
# Custom prompt showing user, host, and current directory
ENV PS1='\u@\h:\w\$ '

RUN mkdir -p /root/.bash_history_volume

WORKDIR /usr/src/fieryvoid

RUN curl -sS https://getcomposer.org/composer.phar > composer.phar \
    && ln -s /usr/src/current/docker/php/php.ini /usr/local/etc/php/php.ini \
    && rm /usr/local/etc/php-fpm.d/docker.conf \
    && ln -s /usr/src/current/docker/php/fpm.conf /usr/local/etc/php-fpm.d/docker.conf \
    && mkdir -p source/server \
    && ln -s ../../docker/php/varconfig.php source/server/varconfig.php

VOLUME /usr/src/current /usr/src/fieryvoid/vendor /root/.composer

CMD /usr/src/current/docker/php/start.sh
