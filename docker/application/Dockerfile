FROM webdevops/php-dev:8.2

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN wget -O phive.phar "https://phar.io/releases/phive.phar" && \
    wget -O phive.phar.asc "https://phar.io/releases/phive.phar.asc" && \
    gpg --keyserver hkps://keys.openpgp.org --recv-keys 0x6AF725270AB81E04D79442549D8A98B29B2D5D79 && \
    gpg --verify phive.phar.asc phive.phar && \
    rm phive.phar.asc && \
    chmod +x phive.phar && \
    mv phive.phar /usr/local/bin/phive && \
    mkdir "/home/$APPLICATION_USER/.phive"

# configure services
RUN set -x \
    && apt-get -q update \
    && docker-service enable syslog \
    && docker-run-bootstrap \
    && docker-image-cleanup

WORKDIR /app

ARG UID
ARG GID

# Fix application uid/gid by given arguments
RUN echo "Change uid/gid of 'application' user (${UID}:${GID})" && \
    usermod -u ${UID} application && \
    groupmod -g ${GID} application && \
    usermod -g ${GID} application &&     \
    chown "$APPLICATION_USER":"$APPLICATION_GROUP" -R /app "/home/$APPLICATION_USER"
