#!/bin/bash

set -ex
cd /usr/src/fieryvoid

# PHP logging
touch /usr/local/var/log/php-errors.log
chown www-data:www-data /usr/local/var/log/php-errors.log
tail -n 0 -f /usr/local/var/log/php-errors.log &

# Initial files
rsync -a --delete --exclude-from /usr/src/current/.dockerignore /usr/src/current/ /usr/src/fieryvoid

# Composer
bash -c "php composer.phar selfupdate; php composer.phar install --no-progress --no-suggest; vendor/bin/phpab -e autoload.php -o source/autoload.php source" &

php-fpm &

# App log
touch /tmp/fieryvoid.log
chown www-data:www-data /tmp/fieryvoid.log
tail -n 0 -f /tmp/fieryvoid.log &

# File sync
inotifywait -qmr -e create -e modify -e delete --exclude "___$" --format "%w%f" /usr/src/current/ @/usr/src/current/vendor @/usr/src/current/.git @/usr/src/current/.idea | stdbuf -o0 sed -l1 "s|/usr/src/current/||" | xargs -L1 -I {} sh -c 'rsync -a --delete --exclude-from /usr/src/current/.dockerignore /usr/src/current/ /usr/src/fieryvoid/'

