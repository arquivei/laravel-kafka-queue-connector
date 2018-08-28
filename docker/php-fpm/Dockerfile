FROM phpdockerio/php72-fpm:latest

WORKDIR "/application"

RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.2-mysql php7.2-bcmath php7.2-gd php-zip php-mbstring \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
