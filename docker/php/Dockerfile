FROM serversideup/php:8.2-fpm-nginx-v2.2.1@sha256:6c81f2b8897e9a27ea1a36dc291a4f029fd31a6b4f7ef5916ece7f964300dc0a

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates nano \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

COPY --chown=$PUID:$PGID . /var/www/html/

# Remove all files but keep directory structure in storage directory
RUN find /var/www/html/storage ! -type d -exec rm '{}' \;

ENTRYPOINT ["/init"]
