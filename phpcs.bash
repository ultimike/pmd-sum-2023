#!/bin/sh

docker exec -it -w '/var/www/html/' ddev-pmd-web bash -c 'phpcs'
