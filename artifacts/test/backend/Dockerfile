FROM aleherse/tuprogramaelectoral:backend-dev

COPY . /var/www
RUN chown -R www-data:www-data /var/www
WORKDIR /var/www

USER www-data
RUN composer install --working-dir=/var/www --no-interaction --quiet &&\
    composer install --working-dir=/var/www/apps/backend --no-interaction --quiet &&\
    composer install --working-dir=/var/www/packages/dominio --no-interaction --quiet &&\
    composer install --working-dir=/var/www/packages/infraestructura --no-interaction --quiet

USER root
CMD ["apache2-foreground"]
