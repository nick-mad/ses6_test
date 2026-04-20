#!/bin/sh
set -e

export DB_HOST=${DB_HOST:-localhost}
export DB_PORT=${DB_PORT:-5432}
export DB_USER=${DB_USER:-postgres}
export DB_PASS=${DB_PASS:-}
export DB_NAME=${DB_NAME:-release_notification}

# Wait for PostgreSQL
echo "Waiting for PostgreSQL at $DB_HOST:$DB_PORT..."
MAX_TRIES=15
TRIES=0
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" > /dev/null 2>&1 || [ $TRIES -eq $MAX_TRIES ]; do
    sleep 1
    TRIES=$((TRIES + 1))
done

if [ $TRIES -eq $MAX_TRIES ]; then
    echo "PostgreSQL is not ready after $MAX_TRIES attempts — continuing anyway"
else
    echo "Database is ready — creating database if it doesn't exist"
    PGPASSWORD="$DB_PASS" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d postgres -tc "SELECT 1 FROM pg_database WHERE datname = '$DB_NAME'" | grep -q 1 || \
    PGPASSWORD="$DB_PASS" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d postgres -c "CREATE DATABASE \"$DB_NAME\"" || true

    echo "Running migrations"
    php vendor/bin/phinx migrate -e local
fi

# Setup cron job
if [ -n "$TZ" ] && [ -f "/usr/share/zoneinfo/$TZ" ]; then
    ln -snf "/usr/share/zoneinfo/$TZ" /etc/localtime && echo "$TZ" > /etc/timezone
fi

# Ensure logs directory exists and is writable (must be before cron setup and start)
touch /var/www/logs/cron.log
chmod 0666 /var/www/logs/cron.log

#disable cron
#env | grep -v ' ' | sed 's/^\([^=]*\)=\(.*\)$/export \1="\2"/' > /var/www/.env.sh
#chown www-data:www-data /var/www/.env.sh
#echo "* * * * * root . /var/www/.env.sh; cd /var/www && /usr/local/bin/php /var/www/bin/console.php subscription:scan >> /var/www/logs/cron.log 2>&1" > /etc/cron.d/subscription-cron
#chmod 0644 /etc/cron.d/subscription-cron
# Start cron daemon
#/usr/sbin/cron

exec "$@"
