FROM ubuntu
MAINTAINER Ihor Zakutynsky ["zakutynsky@gmail.com"]

COPY web /web
COPY composer.json composer.json
COPY conf/tools.conf /etc/nginx/sites-available/tools.conf
COPY conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN apt-get update
RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
RUN echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | tee /etc/apt/sources.list.d/mongodb.list
RUN apt-get update
RUN apt-get install -y mongodb-10gen

RUN mkdir -p /data/db

RUN apt-get update && apt-get install -y \
        git \
        curl \
        vim \
        wget \
        nginx \
        php-fpm \
        php-curl \
        php-pear\
        php7.0-dev \
        openssl \
        libssl-dev \
        libcurl4-openssl-dev \
        pkg-config \
        libsasl2-dev \
        libpcre3-dev \
        php-mongodb \
        supervisor

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN pecl install mongodb
RUN composer install --no-interaction

RUN ln -s /etc/nginx/sites-available/tools.conf /etc/nginx/sites-enabled/tools.conf

EXPOSE 8080

CMD ["/usr/bin/supervisord"]