#!/bin/bash

composer run-script --working-dir=/var/www/ --no-dev post-update-cmd
rm -fr /tmp/*

exec apache2-foreground
