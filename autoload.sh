#!/bin/sh
# Regenerates source/autoload.php (the class map) from the source tree using
# phpab (theseer/autoload - the repo's only composer dependency).
#
# Run from the repo root inside the php container:
#   docker exec -w /usr/src/current fieryvoid-php-1 sh autoload.sh
# or via the host-side wrapper:  scripts\fvbuild.ps1 -Autoload
# Design + migration notes: AUTOLOAD_GENERATOR_PLAN.md
#
# Optional argument: output path (default source/autoload.php).
# fvbuild.ps1 -Check uses it to generate to a temp file and byte-compare.
#
# Exclusions - keep one comment per exclude, with the reason:
#
#   autoload.php             the output file itself
#
#   *_old.php                superseded ship FILES whose class names collide
#                            with their live versions (kirishiacWarrior_old,
#                            kirishiacLordship_old, Altarus_old, Balvarus_old,
#                            ChoukaRaiderHeresy_old, ChoukaRaiderOathbreaker_old).
#                            Also catches source/autoload_old.php (the frozen
#                            pre-generator map, reference only).
#                            NOTE: *_old/ DIRECTORIES stay scanned - they hold
#                            classes that old saved games still load.
#
#   customs/layoutTest.php   dev scratch; duplicate VelraxSivrinGunship
#
# To keep a class on disk but OUT of the map, add another -e line with a
# reason comment. Do NOT use that to retire a playable ship - set
# variantOf='NONE' in the ship file instead: that hides it from the lobby
# while old games containing it keep loading.
#
# phpab fails hard if the same class name is declared in two scanned files.
# That is deliberate. Resolve the collision (delete/rename the impostor, or
# exclude the losing file here with a reason) - never hand-edit the map.

set -e

OUT="${1:-source/autoload.php}"

if [ ! -x vendor/bin/phpab ]; then
    echo "phpab missing - installing composer dependencies..."
    # Prefer a real composer install; the image's self-updated phar
    # (/usr/src/fieryvoid/composer.phar) over the repo-root one, which is
    # ancient Composer 1 and cannot read the current lock file.
    if command -v composer >/dev/null 2>&1; then
        composer install --no-progress --no-interaction
    elif [ -f /usr/src/fieryvoid/composer.phar ]; then
        php /usr/src/fieryvoid/composer.phar install --no-progress --no-interaction
    else
        php composer.phar install --no-progress --no-interaction
    fi

    # Guard: a composer install can fail partway (e.g. a torn phar read if the
    # boot-time install/selfupdate is racing this one) yet still exit 0-ish and
    # leave no phpab. Fail here with a clear message instead of the next line
    # blowing up on a missing binary.
    if [ ! -x vendor/bin/phpab ]; then
        echo "autoload.sh: composer install finished but vendor/bin/phpab is still missing." >&2
        echo "autoload.sh: this is usually transient - re-run. If it persists, run 'php composer.phar install' by hand to see the real error." >&2
        exit 1
    fi
fi

vendor/bin/phpab \
    -e 'autoload.php' \
    -e '*_old.php' \
    -e '*/customs/layoutTest.php' \
    -o "$OUT" \
    source
