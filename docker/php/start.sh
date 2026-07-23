#!/bin/bash

set -ex
cd /usr/src/fieryvoid

# PHP logging
touch /usr/local/var/log/php-errors.log
chown www-data:www-data /usr/local/var/log/php-errors.log
tail -n 0 -f /usr/local/var/log/php-errors.log &

# Initial files
rsync -a --delete --exclude-from /usr/src/current/.dockerignore /usr/src/current/ /usr/src/fieryvoid

# Composer (vendor holds phpab only). Deliberately NOT regenerating
# source/autoload.php here: the committed map is authoritative, so local
# testing exercises exactly what will be deployed. Regenerate explicitly
# with ./autoload.sh (or scripts\fvbuild.ps1) - see AUTOLOAD_GENERATOR_PLAN.md.
#
# No selfupdate: the Dockerfile already ships the current composer.phar, and a
# boot-time selfupdate REWRITES this phar in the background. autoload.sh reads
# the same phar (php /usr/src/fieryvoid/composer.phar install) when fvbuild runs
# right after boot, so the rewrite tore the phar mid-read -> "Class
# GithubActionError not found" garbage, needing a second run. See start.sh race.
bash -c "php composer.phar install --no-progress --no-suggest" &

php-fpm &

# App log
touch /tmp/fieryvoid.log
chown www-data:www-data /tmp/fieryvoid.log
tail -n 0 -f /tmp/fieryvoid.log &

# File sync
inotifywait -qmr -e create -e modify -e delete --exclude "___$" --format "%w%f" /usr/src/current/ @/usr/src/current/vendor @/usr/src/current/.git @/usr/src/current/.idea | stdbuf -o0 sed -l1 "s|/usr/src/current/||" | xargs -L1 -I {} sh -c 'rsync -a --delete --exclude-from /usr/src/current/.dockerignore /usr/src/current/ /usr/src/fieryvoid/'

