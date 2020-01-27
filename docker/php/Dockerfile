FROM php:7.3-fpm

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        librabbitmq-dev

# Install amqp
RUN pecl install amqp \
    && docker-php-ext-enable amqp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql

# install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Error log
RUN echo "log_errors = On" >> /usr/local/etc/php/conf.d/error.ini \
    && echo "error_log = /var/log/php-fpm/error.log" >> /usr/local/etc/php/conf.d/error.ini \
    && mkdir /var/log/php-fpm

WORKDIR /var/www/html

CMD composer install --no-interaction -n --optimize-autoloader \
    && chown -R www-data: /var/www/html/var/ \
    && php /var/www/html/bin/console doctrine:migrations:migrate --no-interaction \
    && php-fpm
