FROM php:8.3.2-fpm AS base

# Add global binary directory to PATH and make sure to re-export it
ENV PATH /composer/vendor/bin:$PATH

# Allow Composer to be run as root
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update -y && apt-get install -y libmcrypt-dev zlib1g-dev libzip-dev libpng-dev \
  && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*

RUN docker-php-ext-install zip \
    && docker-php-ext-configure bcmath \
    && docker-php-ext-install bcmath \
    && pecl install mcrypt \
    && pecl install redis \
    && pecl install xdebug \
    && docker-php-ext-enable redis xdebug mcrypt \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install gd

WORKDIR /var/www/vigrom

ENV FPM_PM_MAX_CHILDREN 20
ENV FPM_PM_START_SERVERS 3
ENV FPM_PM_MIN_SPARE_SERVERS 3
ENV FPM_PM_MAX_SPARE_SERVERS 8
ENV FPM_PM_MAX_REQUESTS 500
ENV PHP_OPCACHE 0

COPY ./docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/web-app.conf
COPY ./docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Add local web-user
ARG WEB_USER_ID=1000
ARG WEB_USER_NAME=developer
ARG WEB_GROUP_ID=1000
ARG WEB_GROUP_NAME=developer
RUN echo "Building for web user: id=${WEB_USER_ID} name=${WEB_USER_NAME} group=${WEB_GROUP_NAME}"
RUN groupadd -g ${WEB_GROUP_ID} ${WEB_GROUP_NAME}|| echo "Group exists"
RUN useradd -m -u ${WEB_USER_ID} ${WEB_USER_NAME} -g ${WEB_GROUP_NAME} || echo "Users exists"
RUN sed -i -- "s/user = www-data/user = ${WEB_USER_NAME}/g" /usr/local/etc/php-fpm.d/web-app.conf
USER ${WEB_USER_ID}

CMD ["php-fpm"]
