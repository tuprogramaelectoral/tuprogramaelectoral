#!/bin/bash

cd /var/www/apps/frontend/
cp app/scripts/config.prod.js app/scripts/config.js
grunt build
cp app/scripts/config.dev.js app/scripts/config.js
