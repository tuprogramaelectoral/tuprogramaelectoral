FROM ubuntu:15.10

RUN apt-get -yqq update && \
    echo "Europe/Madrid" > /etc/timezone && \
    ln -s -f /usr/share/zoneinfo/Europe/Madrid /etc/localtime && \
    locale-gen es_ES.UTF-8 && \
    apt-get -yqq install vim curl apache2 git \
    php5 php5-cli php5-pgsql php5-sqlite php5-intl php5-mcrypt php5-apcu php5-curl && \
    a2enmod rewrite vhost_alias headers expires filter && \
    rm -fr /var/www/html

RUN curl -sS https://getcomposer.org/installer | php &&\
    mv composer.phar /usr/local/bin/composer

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid
EXPOSE 80

COPY config/apache2-foreground.sh /usr/local/sbin/apache2-foreground
RUN chmod 755 /usr/local/sbin/apache2-foreground

CMD ["apache2-foreground"]
