#!/bin/sh
set -e

# Wait for MySQL
./wait-for-it.sh db:3306 -- echo "MySQL is up"

# Run migrations
./vendor/bin/doctrine-migrations migrate --no-interaction

# Start Apache in the foreground
exec apache2-foreground
