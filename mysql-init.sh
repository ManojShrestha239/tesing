#!/bin/bash
set -e

# Wait until MySQL is truly ready (rarely hurts, even in dev)
until mysqladmin ping -h localhost --silent; do
    echo "mysql-init.sh: waiting for MySQL to start..."
    sleep 2
done

# Grant ALL PRIVILEGES on everything to the app user
mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    GRANT ALL PRIVILEGES ON *.* TO '${MYSQL_USER}'@'%' WITH GRANT OPTION;
    FLUSH PRIVILEGES;
EOSQL

echo "mysql-init.sh: granted global privileges to ${MYSQL_USER}"
