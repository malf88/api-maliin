FROM php:8.0-fpm

WORKDIR /var/www
ENV CURL_CA_BUNDLE /usr/lib/ssl/certs/ca-certificates.crt

COPY ./ /var/www/

ENV TZ=America/Sao_Paulo
ARG BUILD_APP_ENV
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY ./docker/entrypoint.sh /usr/local/bin/start

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \
    cp /var/www/docker/$BUILD_APP_ENV/php.ini /usr/local/etc/php/php.ini && \
    apt-get update && apt-get -y upgrade && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y \
        nano \
        libcurl4-gnutls-dev \
        libxml2-dev \
        libpng-dev \
        git \
        libzip-dev \
        openssl \
        libpq-dev && \
    docker-php-ext-install \
        bcmath \
        calendar \
        gd \
        pgsql \
        pdo_pgsql \
        sockets \
        zip && \
    sed -i "s/CipherString = .*$/#CipherString/" /etc/ssl/openssl.cnf && \
    chmod +x /usr/local/bin/start && \
    chown -R www-data:www-data /var/www && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan storage:link

COPY ./docker/www.conf /usr/local/etc/php-fpm.d/www.conf

USER www-data
CMD ["/usr/local/bin/start"]

# By default start up apache in the foreground, override with /bin/bash for interative.
