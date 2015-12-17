FROM aleherse/tuprogramaelectoral:backend-base

RUN apt-get -yqq install php5-xdebug

COPY config/default.conf /etc/apache2/sites-available/000-default.conf
COPY config/php.ini /etc/php5/cli/conf.d/90-app.ini
COPY config/php.ini /etc/php5/apache2/conf.d/90-app.ini
COPY config/run-app.sh /usr/local/sbin/run-app
RUN chmod 755 /usr/local/sbin/run-app

WORKDIR /var/www

ENV XDEBUG_CONFIG 'idekey=PhpStorm'

CMD ["run-app"]
