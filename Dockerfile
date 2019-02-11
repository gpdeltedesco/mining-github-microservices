# Builder image: Installs dev dependencies and builds application
FROM php:7.1-cli-stretch as builder

# Install required apt dependencies (at least, unzip, for composer)
RUN apt-get update \
    && apt-get install unzip -y \
    && apt-get clean

# Install composer (from official docker image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install source files
RUN mkdir --parents /opt/ghminer
COPY . /opt/ghminer

# Install composer dependencies
RUN cd /opt/ghminer \
    && COMPOSER_ALLOW_SUPERUSER=1 \
        composer install --no-ansi --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader


# Runtime image: Installs runtime deps (git)
FROM php:7.1-cli-stretch

# Install runtime apt dependencies (git)
RUN apt-get update \
    && apt-get install git -y \
    && apt-get clean

# Install created artifacts
COPY --from=builder /opt/ghminer /opt/ghminer

# Set working directory
WORKDIR /opt/ghminer
