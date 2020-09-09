FROM php:7.4.3-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN apk add pcre-dev ${PHPIZE_DEPS} \
  && pecl install xdebug-2.8.0 \
  && docker-php-ext-enable xdebug \
  && apk del pcre-dev ${PHPIZE_DEPS}

WORKDIR /var/www/html

COPY start.sh /usr/local/

RUN chmod +x /usr/local/start.sh

ENTRYPOINT ["/usr/local/start.sh"]