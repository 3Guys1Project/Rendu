#!/bin/sh

composer install --prefer-dist --no-progress --no-interaction

npm install

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

if [ "$APP_ENV" = "prod" ]; then
    npm run build

    exec apache2-foreground
else
    npm run watch & exec apache2-foreground
fi

wait
