FROM aleherse/tuprogramaelectoral:frontend-base

COPY config/install-app.sh /usr/local/sbin/install-app
RUN chmod 755 /usr/local/sbin/install-app

COPY config/build-app.sh /usr/local/sbin/build-app
RUN chmod 755 /usr/local/sbin/build-app

CMD ["grunt", "--gruntfile=/var/www/apps/frontend/Gruntfile.js", "serve"]
