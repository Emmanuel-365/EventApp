#!/bin/bash
set -e

# Wait for DB to be ready - simple check, consider more robust solutions for production
# Timeout after 60 seconds
echo "Waiting for database connection..."
timeout 60 bash -c 'until mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
  echo "Database is unavailable - sleeping"
  sleep 1
done'
echo "Database is up - executing command"

# Run Laravel optimizations if in production or not explicitly disabled
if [[ "$APP_ENV" == "production" ]] || [[ "$OPTIMIZE_APP" == "true" ]]; then
  echo "Running Laravel optimizations..."
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  # php artisan event:cache # Uncomment if using event discovery and caching
fi

# Run migrations for the central database
echo "Running central migrations..."
php artisan migrate --force

# After central migrations

# Tenant setup
# If SETUP_TENANTS_ON_STARTUP is true, the custom tenants:setup command is run.
# This command is expected to handle database creation, migration, and seeding for tenants.
# If AUTO_MIGRATE_TENANTS is true (and SETUP_TENANTS_ON_STARTUP is not),
# then only migrations for existing tenants are run using the stancl/tenancy default command.
if [[ "$SETUP_TENANTS_ON_STARTUP" == "true" ]] && [[ -f "app/Console/Commands/SetupTenants.php" ]]; then
  echo "Running initial tenant setup (creation, migration, seeding via tenants:setup)..."
  php artisan tenants:setup
elif [[ "$AUTO_MIGRATE_TENANTS" == "true" ]]; then
  echo "Running migrations for existing tenants (via tenants:migrate)..."
  # This will only migrate existing tenants. It won't create or seed them if they don't have a DB yet.
  # The `tenants:setup` command is more comprehensive for initial setup.
  php artisan tenants:migrate --force
else
  echo "Skipping automatic tenant setup/migration. Manual execution might be required for tenants."
fi

# Symlink storage if it doesn't exist and if APP_ENV is not production to avoid issues with existing links
# In production, storage link should be created as part of the deployment process.
if [ ! -L "/var/www/html/public/storage" ] && [ "$APP_ENV" != "production" ]; then
  echo "Linking storage directory..."
  php artisan storage:link
else
  echo "Storage link already exists or in production mode (skipping auto-link)."
fi

# Start PHP-FPM or execute the passed command
echo "Starting PHP-FPM or executing command: $@"
exec "$@"
