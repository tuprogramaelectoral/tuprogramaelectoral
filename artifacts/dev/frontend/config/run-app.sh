#!/bin/bash

pushd /var/www/apps/frontend/
npm install --silent
bower install --quiet
popd

chown -R www-data:www-data /var/www

/var/www/apps/frontend/node_modules/phantomjs/bin/phantomjs --webdriver=8910 &

exec grunt --gruntfile=/var/www/apps/frontend/Gruntfile.js serve
