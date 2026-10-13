# Pinned to live's exact version (11.4.5-MariaDB, FreeBSD Ports build, checked 2026-10-08). Bump it
# when live moves. From 11.0 the image has no mysql / mysqldump / mysqladmin commands: use mariadb,
# mariadb-dump and mariadb-admin.
FROM mariadb:11.4.5

ENV MYSQL_DATABASE B5CGM
ENV MYSQL_ROOT_PASSWORD fieryvoid
# A data volume made by an older MariaDB (10.3 before 2026-10-08) is upgraded in place on its first
# start: the system tables are backed up into the volume, then mariadb-upgrade runs. Tested on a copy
# of a 10.3 volume, with identical data afterwards. Does nothing on a volume that is already current.
ENV MARIADB_AUTO_UPGRADE 1
# Live's DB server reports system_time_zone CEST, time_zone SYSTEM. Any Central European zone gives
# the same clock; Warsaw is the host's. Until 2026-10-08 the local server ran on UTC, so local
# DATETIMEs written before then read two hours (one in winter) older than they really are.
ENV TZ Europe/Warsaw

# Character set and collation as on live, and live's sql_mode is the built-in default. The image's
# own config would make new utf8mb4 tables utf8mb4_uca1400_ai_ci. FV's tables all declare utf8, and
# DBManager sets the connection to utf8, so this only picks the defaults for anything new.
RUN printf '[mariadbd]\ncharacter-set-server = utf8mb4\ncollation-server = utf8mb4_unicode_ci\ncharacter-set-collations =\n' \
        > /etc/mysql/conf.d/fieryvoid.cnf \
    && echo -e "\nexport TERM=xterm" >> ~/.bashrc \
    && ln -s /usr/src/fieryvoid/db/emptyDatabase.sql /docker-entrypoint-initdb.d/1_emptyDatabase.sql

VOLUME /usr/src/current
