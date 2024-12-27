FROM php:8.3-bullseye AS php

# Install dependencies
RUN set -eux \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        libzip-dev \
        librabbitmq-dev \
        libssh-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install zip and sodium extensions
RUN set -eux \
    && docker-php-ext-install \
      zip \
      bcmath \
      sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
COPY . .
CMD ["tail", "-f", "/dev/null"]