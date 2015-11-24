FROM aleherse/tuprogramaelectoral:backend-base

COPY artifacts/prod/backend/config/default.conf /etc/apache2/sites-available/000-default.conf
COPY artifacts/prod/backend/config/php.ini /etc/php5/cli/conf.d/90-app.ini
COPY artifacts/prod/backend/config/php.ini /etc/php5/apache2/conf.d/90-app.ini
COPY artifacts/prod/backend/config/run-app.sh /usr/local/sbin/run-app
RUN chmod 755 /usr/local/sbin/run-app

COPY apps/backend /var/www
COPY packages/dominio /var/www/vendor/dominio
COPY packages/infraestructura /var/www/vendor/infraestructura
RUN rm /var/www/web/app_dev.php
RUN rm /var/www/web/config.php
RUN chown -R www-data:www-data /var/www

WORKDIR /var/www

CMD ["run-app"]
