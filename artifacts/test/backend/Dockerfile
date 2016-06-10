FROM aleherse/tuprogramaelectoral:backend-dev

WORKDIR /var/www
COPY . /var/www
RUN chown -R www-data:www-data /var/www

ENV DATABASE_HOST db.tuprogramaelectoral
ENV DATABASE_NAME vspdb
ENV DATABASE_USER root
ENV DATABASE_PASSWORD admin
ENV SYMFONY_SECRET ThisTokenIsNotSoSecretChangeIt

USER www-data
RUN composer install --working-dir=/var/www --no-interaction --quiet &&\
    composer install --working-dir=/var/www/apps/backend --no-interaction --quiet &&\
    composer install --working-dir=/var/www/packages/domain --no-interaction --quiet &&\
    composer install --working-dir=/var/www/packages/infrastructure --no-interaction --quiet
RUN php apps/backend/app/console cache:warmup --env test


USER root
CMD ["apache2-foreground"]
