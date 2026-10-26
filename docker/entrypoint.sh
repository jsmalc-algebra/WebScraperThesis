#!/bin/sh
set -e

echo "Waiting for MySQL..."
until mysqladmin ping -h127.0.0.1 -P3306 -uroot -p"$MYSQL_ROOT_PASSWORD" --silent; do
  sleep 2
done
echo "MySQL is up."

php bin/console importmap:install
php bin/console asset-map:compile
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction


# Fixing ownership after console commands run as root
chown -R www-data:www-data /var/www/var
chown -R www-data:www-data /var/www/public/assets || true

exec "$@"
