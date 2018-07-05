FROM mariadb:10.3

ENV MYSQL_DATABASE B5CGM
ENV MYSQL_ROOT_PASSWORD fieryvoid

RUN echo -e "\nexport TERM=xterm" >> ~/.bashrc \
    && echo "Europe/Helsinki" > /etc/timezone \
    && dpkg-reconfigure -f noninteractive tzdata \
    && ln -s /usr/src/fieryvoid/db/emptyDatabase.sql /docker-entrypoint-initdb.d/1_emptyDatabase.sql \
    && ln -s /usr/src/fieryvoid/db/addGameRules.sql /docker-entrypoint-initdb.d/2_addGameRules.sql

VOLUME /usr/src/current
