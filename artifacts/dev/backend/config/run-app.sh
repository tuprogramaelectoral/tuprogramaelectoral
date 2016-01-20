#!/bin/bash

# Instalar dependencias
composer install --working-dir=/var/www --no-interaction --quiet
composer install --working-dir=/var/www/apps/backend --no-interaction --quiet
composer install --working-dir=/var/www/packages/domain --no-interaction --quiet
composer install --working-dir=/var/www/packages/infrastructure --no-interaction --quiet

# Actualizar base de datos
php /var/www/apps/backend/app/console data:load --regenerate /var/www/data

rm -fr /tmp/*
chown -R www-data:www-data /var/www

exec apache2-foreground
