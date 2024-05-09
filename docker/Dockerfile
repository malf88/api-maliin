FROM php:8.2-fpm

WORKDIR /var/www
ENV CURL_CA_BUNDLE /usr/lib/ssl/certs/ca-certificates.crt
ARG USERID=1000
COPY ./ /var/www/

ENV TZ=America/Sao_Paulo
ARG BUILD_APP_ENV
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone && \
    cp /var/www/docker/$BUILD_APP_ENV/php.ini /usr/local/etc/php/php.ini
RUN apt-get update && apt-get -y upgrade && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y \
        nano \
        wget \
        libcurl4-gnutls-dev \
        libxml2-dev \
        libpng-dev \
        git \
        libzip-dev \
        supervisor \
        openssl \
        zlib1g-dev \
        libpq-dev && \
        wget  https://github.com/elastic/apm-agent-php/releases/download/v1.10.0/apm-agent-php_1.10.0_all.deb && \
        dpkg -i apm-agent-php_1.10.0_all.deb && \
     docker-php-ext-install \
        bcmath \
        calendar \
        gd \
        pgsql \
        pdo_pgsql \
        pcntl \
        zip && \
    pecl install redis && \
    docker-php-ext-enable redis &&\sed -i "s/CipherString = .*$/#CipherString/" /etc/ssl/openssl.cnf && \
    chmod +x /usr/local/bin/entrypoint.sh && \
    chown -R www-data:www-data /var/www


RUN php artisan config:clear && \
     php artisan cache:clear && \
     php artisan storage:link

# RUN if [ -z "${JWT_SECRET}" ]; then php artisan jwt:secret; fi
RUN if [ "$BUILD_APP_ENV" = "local" ]; then pecl install xdebug && docker-php-ext-enable xdebug && pear config-set php_ini /usr/local/etc/php/php.ini; fi

COPY ./docker/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/supervisor/conf.d /etc/supervisor/conf.d

RUN if [ "${CONTAINER_ROLE}" = "scheduler" ]; then service supervisor stop && service supervisor start  && supervisorctl reread && supervisorctl update && service supervisor restart horizon; fi
RUN chown -R ${USERID} /var/www
USER ${USERID}
CMD ["/usr/local/bin/entrypoint.sh"]
