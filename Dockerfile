FROM cyberduck/php-fpm-laravel:8.2

COPY . /var/www

WORKDIR /var/www

RUN composer install --no-progress --no-interaction --no-dev -o

RUN chown -R www-data:www-data /var/www/storage
RUN chown -R www-data:www-data /var/www/bootstrap/cache

RUN php artisan optimize && \
    php artisan clear-compiled && \
    php artisan key:generate --force && \
    php artisan cache:clear && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan storage:link

EXPOSE 9000

