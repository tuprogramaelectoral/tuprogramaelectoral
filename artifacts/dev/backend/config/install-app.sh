#!/bin/bash

# Install dependencies
composer self-update
composer install --working-dir=/var/www --no-interaction
composer install --working-dir=/var/www/apps/backend --no-interaction
composer install --working-dir=/var/www/packages/domain --no-interaction
composer install --working-dir=/var/www/packages/infrastructure --no-interaction

# Update database
php /var/www/apps/backend/app/console data:load --regenerate /var/www/data
