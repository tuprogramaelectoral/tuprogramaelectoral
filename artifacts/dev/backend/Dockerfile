FROM aleherse/tuprogramaelectoral:backend-base

RUN apt-get update && apt-get install -yq install php5-xdebug

COPY config/default.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /etc/php5/cli/conf.d/90-app.ini
COPY config/php.ini /etc/php5/apache2/conf.d/90-app.ini
COPY config/install-app.sh /usr/local/sbin/install-app
RUN chmod 755 /usr/local/sbin/install-app

WORKDIR /var/www

ENV XDEBUG_CONFIG 'idekey=PHPSTORM'

CMD ["apache2-foreground"]
