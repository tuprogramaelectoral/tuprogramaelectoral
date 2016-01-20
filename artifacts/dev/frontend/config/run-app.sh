#!/bin/bash

pushd /var/www/apps/frontend/
npm install --silent
bower install --quiet
popd

chown -R www-data:www-data /var/www

exec grunt --gruntfile=/var/www/apps/frontend/Gruntfile.js serve
