FROM aleherse/tuprogramaelectoral:frontend-dev

COPY . /var/www

WORKDIR /var/www/apps/frontend/
RUN npm install --silent
RUN bower install --quiet
WORKDIR /var/www
RUN mv apps/frontend/app/scripts/config.test.js apps/frontend/app/scripts/config.js
RUN chown -R www-data:www-data .

CMD ["grunt", "--gruntfile=/var/www/apps/frontend/Gruntfile.js", "serve"]
