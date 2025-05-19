FROM php:8.4-fpm AS base

USER root

RUN apt-get update && apt-get install -y \
    curl \
    libxml2-dev \
    libpng-dev \
    libonig-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . /var/www

FROM base AS dev

ARG USER_ID
ARG GROUP_ID

RUN groupadd -g ${GROUP_ID} appuser && \
    useradd -l -u ${USER_ID} -g appuser appuser && \
    install -d -m 0755 -o appuser -g appuser /home/appuser

RUN chown -R appuser:appuser /var/www
USER appuser

