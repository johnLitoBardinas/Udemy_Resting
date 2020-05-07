FROM php:7.2-fpm

ENV BASE_DIR=/var/www
ENV DOCKER_DIR=./.docker/app

# Update the package registry, upgrade the packages and install libraries
RUN apt-get update && apt-get install -y git libmcrypt-dev \
    default-mysql-client default-libmysqlclient-dev \
	libgmp-dev libmagickwand-dev --no-install-recommends \
    && rm -rf /var/lib/apt/lists/* \
    && pecl install imagick \
    && pecl install mcrypt-1.0.2 \
    && docker-php-ext-enable imagick mcrypt \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install gmp

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install NodeJS
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - \
    && apt-get install nodejs -y \
    && node -v && npm -v