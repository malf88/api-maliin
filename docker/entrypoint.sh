#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}
env=${APP_ENV:-production}
if [ "$1" = "health" ] && [ "$role" = "app" ]; then
    /usr/bin/curl --fail https://api.malf88.xyz/api/health || exit 1
    exit 1
elif [ "$1" = "health" ]; then
    exit 1
fi



if [ "$role" = "app" ]; then
    php /var/www/artisan migrate --force
    exec php-fpm -F -R

elif [ "$role" = "analysis" ]; then
    php artisan analysis:start

elif [ "$role" = "queue" ]; then
    echo "Queue iniciada."
    php /var/www/artisan queue:work --queue=Analysis,default


elif [ "$role" = "scheduler" ]; then

    while [ true ]
    do
      php /var/www/artisan schedule:run --verbose --no-interaction &
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi
