FROM gitlab.tce.mt.gov.br:5050/publico/php:8.2-fpm-latest

USER root

COPY --chown=www:www-data .docker/php-fpm/php.ini /usr/local/etc/php/php.ini
COPY --chown=www:www-data .docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

COPY . /var/www/html

RUN chown -R www:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage
RUN chown -R www:www-data /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/bootstrap/cache
RUN chown -R www:www-data /var/www/html/public
RUN chmod -R 755 /var/www/html/public

RUN ln -s /var/www/html/storage/app/public /var/www/html/public/storage

USER www

WORKDIR /var/www/html

EXPOSE 9000
