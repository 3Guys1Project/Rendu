#!/bin/sh

composer install --prefer-dist --no-progress --no-interaction

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

exec apache2-foreground
