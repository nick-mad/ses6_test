FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libicu-dev \
    libpq-dev \
    postgresql-client \
    curl \
    cron \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip intl pdo_pgsql sockets

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=ghcr.io/roadrunner-server/roadrunner:2024.3.5 /usr/bin/rr /usr/bin/rr

WORKDIR /var/www
COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev

RUN chown -R www-data:www-data /var/www/tmp /var/www/logs /var/www/db

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["rr", "serve", "-c", ".rr.yaml"]

HEALTHCHECK --interval=30s --timeout=10s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:8080/ || exit 1