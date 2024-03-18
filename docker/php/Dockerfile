FROM serversideup/php:8.2-fpm-nginx-v2.2.1

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates nano \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY --chown=$PUID:$PGID . /var/www/html/

# Remove all files but keep directory structure in storage directory
RUN find /var/www/html/storage ! -type d -exec rm '{}' \;

ENTRYPOINT ["/init"]
