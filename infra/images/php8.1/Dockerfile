ARG COMPOSE_PROJECT_NAME
ARG TAG

ARG PHP_VERSION=8.1.4
ARG COMPOSER_FROM="composer-base"
ARG BASE="base"

FROM php:${PHP_VERSION}-fpm as base

ARG PHP_VERSION=8.1.4

ENV TZ=Europe/Moscow
ENV DEBIAN_FRONTEND=noninteractive
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update && apt-get install -y gcc make g++ curl \
    ca-certificates \
    tzdata \
    vim \
    nano \
    wget \
    unzip \
    git \
    bash \
    autoconf \
    libpng-dev libzip-dev libxml2-dev libpq-dev libcurl4-openssl-dev libcurl4 libonig-dev libssl-dev gnupg gnupg1 gnupg2

#RUN wget -q -O - https://packages.blackfire.io/gpg.key | apt-key add - \
#    && echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list \
#    && apt-get update && apt-get install -y blackfire-php

#COPY blackfire.ini /usr/local/etc/php/conf.d/zz-blackfire.ini

RUN docker-php-ext-configure phar \
    && docker-php-ext-configure xml \
    && docker-php-ext-configure curl \
    && docker-php-ext-configure mbstring \
    && docker-php-ext-configure opcache \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure bcmath \
    && docker-php-ext-configure calendar \
    && docker-php-ext-configure sockets \
#######
    && docker-php-ext-install phar \
    && docker-php-ext-install xml \
    && docker-php-ext-install curl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install opcache \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install calendar \
    && docker-php-ext-install sockets

#RUN pecl update-channels && pecl install grpc
#RUN docker-php-ext-enable grpc
RUN apt-get update && apt-get install -y libyaml-dev
RUN pecl update-channels && pecl install yaml
RUN docker-php-ext-enable yaml

#RUN pecl update-channels && pecl install -o -f redis pcov  \
#    &&  rm -rf /tmp/pear \
#    &&  docker-php-ext-enable redis pcov


ENV PATH $PATH:/var/www/vendor/bin:/var/www/bin

RUN php --ini

COPY php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www

RUN echo PATH="$PATH:/var/www/vendor/bin" >> /etc/profile
RUN echo 'alias artisan="php /var/www/artisan"' >> /etc/profile

ARG CURRENT_UID=1000
ARG CURRENT_GID=1000
RUN addgroup --gid $CURRENT_GID professional2
RUN adduser --shell /bin/bash --disabled-login --uid 9998 --ingroup professional2 professional2
#RUN usermod -o -u $CURRENT_UID professional2

RUN mkdir -p /home/professional2/.config/psysh && chown -R professional2 /home/professional2/


FROM base as composer-base

USER root

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN chmod +x /usr/local/bin/composer

#RUN apt-get install -y nodejs npm
#RUN npm update -g npm

COPY ssh_config /etc/ssh/ssh_config

ENTRYPOINT ["composer"]

FROM $COMPOSER_FROM as composer-local

ARG CURRENT_UID=1000
ARG CURRENT_GID=1000
RUN addgroup --gid $CURRENT_GID professional3 || true
RUN adduser --shell /bin/bash --disabled-login --gid $CURRENT_GID --uid $CURRENT_UID professional3
RUN mkdir -p /home/professional3/.config/psysh && chown -R professional3 /home/professional3/

FROM $BASE as base-local

ARG CURRENT_UID=1000
ARG CURRENT_GID=1000
RUN addgroup --gid $CURRENT_GID professional3 || true
RUN adduser --shell /bin/bash --disabled-login --gid $CURRENT_GID --uid $CURRENT_UID professional3
RUN mkdir -p /home/professional3/.config/psysh && chown -R professional3 /home/professional3/

FROM base-local as base-xdebug

#RUN echo "" > /usr/local/etc/php/conf.d/zz-blackfire.ini

RUN pecl install xdebug-3.1.3 \
    && docker-php-ext-enable xdebug

FROM base as rr

RUN curl -L https://github.com/spiral/roadrunner-binary/releases/download/v2.7.0/roadrunner-2.7.0-linux-amd64.tar.gz | tar -xz
RUN mv roadrunner-2.7.0-linux-amd64/rr /usr/local/bin/rr && chmod +x /usr/local/bin/rr

FROM base-local as rr-local

RUN curl -L https://github.com/spiral/roadrunner-binary/releases/download/v2.7.0/roadrunner-2.7.0-linux-amd64.tar.gz | tar -xz
RUN mv roadrunner-2.7.0-linux-amd64/rr /usr/local/bin/rr && chmod +x /usr/local/bin/rr


FROM base-xdebug as rr-local-xdebug

#RUN echo "" > /usr/local/etc/php/conf.d/zz-blackfire.ini

RUN curl -L https://github.com/spiral/roadrunner-binary/releases/download/v2.7.0/roadrunner-2.7.0-linux-amd64.tar.gz | tar -xz
RUN mv roadrunner-2.7.0-linux-amd64/rr /usr/local/bin/rr && chmod +x /usr/local/bin/rr

FROM base as phar

COPY kobra /usr/local/bin/kobra

RUN chmod +x /usr/local/bin/kobra

ENTRYPOINT ["/usr/local/bin/kobra"]
CMD []
