# Docker Setup Guide for FieldMA 2.0

This guide walks you through running the project with Docker on macOS, including first-time setup, daily use, common commands, and troubleshooting. It’s tailored to the provided docker-compose.yml, Dockerfile, entrypoint.sh, and nginx.conf.

## Prerequisites

-   macOS with Docker Desktop (v4.x or newer)
-   Free ports locally: 80 (app), 3306 (MySQL), 8081 (phpMyAdmin)
-   Git and this repository checked out

Notes for Apple Silicon (M1/M2/M3):

-   MySQL service is pinned to `platform: linux/amd64` for compatibility. Docker Desktop uses emulation; it’s fine but slightly slower.

## Stack overview

Services from `docker-compose.yml`:

-   app (PHP 8.2 FPM): runs Laravel, installs Composer deps, runs migrations/seeds
    -   Container name: `fieldma_2_app`
    -   Port: 9000 inside the network (not exposed directly)
-   nginx: serves HTTP traffic to the app
    -   Container name: `fieldma_2_nginx`
    -   Port: 80 → http://localhost/
-   mysql: MySQL 5.7 with a persistent named volume `mysql_data`
    -   Container name: `fieldma_2_mysql`
    -   Port: 3306 → 127.0.0.1:3306
-   phpmyadmin: Web UI for MySQL
    -   Container name: `fieldma_2_phpmyadmin`
    -   Port: 8081 → http://localhost:8081

Volumes and mounts:

-   Code is bind-mounted into `/var/www` inside app/nginx
-   MySQL data persisted in the named volume `mysql_data`

## Environment variables

`docker-compose.yml` sets these for the app container (and they should match your `.env`):

-   DB_HOST=mysql
-   DB_DATABASE=fieldma_2_db
-   DB_USERNAME=fieldma_2_user
-   DB_PASSWORD=fieldma_2_pass

The entrypoint will copy `.env.example` to `.env` on first run if `.env` is missing. You can adjust DB\_\* in `.env` if you change compose values.

## First-time setup (fresh clone)

The entrypoint script automates most of the initial Laravel setup for you.

1. Optional: Pull base images (faster first build)

```sh
# optional, pulls base images referenced by the compose file
docker compose pull
```

2. Build images

```sh
# build all services using the Dockerfile for the app service
docker compose build
```

3. Start the stack

```sh
# start in the background
docker compose up -d
```

What happens on first boot (entrypoint.sh inside the app container):

-   Installs Composer dependencies if `/var/www/vendor` is missing
-   Creates `.env` from `.env.example` if missing
-   Sets permissions on `storage` and `bootstrap/cache`
-   Generates the app key: `php artisan key:generate --force`
-   Runs migrations with seeders: `php artisan migrate --seed --force`
-   Creates the storage symlink: `php artisan storage:link`
-   Clears and caches config: `php artisan optimize:clear` and `php artisan config:cache`

4. Open the app and database UI

-   App: http://localhost/
-   phpMyAdmin: http://localhost:8081 (server: `mysql`, user: `fieldma_2_user`, pass: `fieldma_2_pass`)

5. Stop/Start later

```sh
# stop containers but keep them around
docker compose stop

# start them again
docker compose start
```

## Daily workflow

-   Start the stack: `docker compose up -d`
-   Work in your editor on the host; code updates are bind-mounted into the container
-   Run framework commands via the app container (examples below)
-   Tail logs when debugging
-   Stop or restart as needed

## Common commands (with explanations)

Stack lifecycle

```sh
# Build or rebuild images (e.g., after changing Dockerfile)
docker compose build

# Start containers in the background
docker compose up -d

# Stop containers (keep volumes and networks)
docker compose stop

# Stop and remove containers, networks (keep volumes)
docker compose down

# Stop and remove everything including named volumes (wipes MySQL data)
docker compose down -v

# See container status
docker compose ps
```

Logs and shell access

```sh
# Follow logs for a specific service
docker compose logs -f app
# or
docker compose logs -f nginx

# Open an interactive shell in the app container
docker exec -it fieldma_2_app bash

# Run a one-off command in the app container
docker exec -it fieldma_2_app php -v
```

Laravel and Composer

```sh
# Run artisan commands
docker exec -it fieldma_2_app php artisan migrate

# Clear caches
docker exec -it fieldma_2_app php artisan optimize:clear

# Rebuild config cache
docker exec -it fieldma_2_app php artisan config:cache

# Install/update PHP dependencies
docker exec -it fieldma_2_app composer install
# or
docker exec -it fieldma_2_app composer update
```

Testing

```sh
# Run Laravel test suite
docker exec -it fieldma_2_app php artisan test
# or PHPUnit directly
docker exec -it fieldma_2_app vendor/bin/phpunit
```

Database

```sh
# Connect from host using mysql client
mysql -h 127.0.0.1 -P 3306 -u fieldma_2_user -p fieldma_2_db
# password: fieldma_2_pass

# Reset database by removing containers and the named volume (destructive)
docker compose down -v
```

phpMyAdmin

-   URL: http://localhost:8081
-   Server/Host: `mysql`
-   Username: `fieldma_2_user`
-   Password: `fieldma_2_pass`

## Nginx details

-   Nginx serves from `/var/www/public`
-   Server name in `nginx.conf` is `fieldma.manojshrestha.tech`, but for local development you can just use http://localhost/
-   If you want to use the server name locally, add a hosts entry like:

```sh
# Optional: map the dev domain to localhost
sudo sh -c 'echo "127.0.0.1 fieldma.manojshrestha.tech" >> /etc/hosts'
```

## Assets (optional)

The stack doesn’t include a Node service. If you need to rebuild frontend assets locally, run on the host:

```sh
# optional, if you’re using Vite or have asset scripts
npm ci
npm run build
```

## Troubleshooting

-   Port 80 or 8081 already in use
    -   Change the ports in `docker-compose.yml` or stop the conflicting service
-   Apple Silicon slowness with MySQL
    -   MySQL runs under `linux/amd64` emulation; acceptable but slower. Consider MySQL 8 arm64 if you’re comfortable upgrading (not covered here)
-   Permissions issues on storage/cache
    -   The entrypoint sets ownership and `chmod 775`; if you still see issues:

```sh
docker exec -it fieldma_2_app bash -lc 'chown -R www-data:www-data storage bootstrap/cache && chmod -R 775 storage bootstrap/cache'
```

-   Composer memory errors
    -   Set a higher memory limit when needed:

```sh
docker exec -it fieldma_2_app bash -lc 'COMPOSER_MEMORY_LIMIT=-1 composer install'
```

-   First run migrations failed
    -   Ensure DB\_\* in `.env` match compose
    -   Reset database and retry:

```sh
docker compose down -v && docker compose up -d --build
```

-   See what the entrypoint is doing
    -   Tail the app logs: `docker compose logs -f app`

## Production notes (non-exhaustive)

-   This compose setup is for local development
-   DB credentials are development defaults; don’t reuse in production
-   Add HTTPS termination, proper env management, secrets, backups, and monitoring before deploying

## Reference

-   Containers
    -   app: `fieldma_2_app`
    -   nginx: `fieldma_2_nginx`
    -   mysql: `fieldma_2_mysql`
    -   phpmyadmin: `fieldma_2_phpmyadmin`
-   Ports
    -   App: 80 → http://localhost/
    -   MySQL: 3306 → 127.0.0.1:3306
    -   phpMyAdmin: 8081 → http://localhost:8081
-   Default DB credentials (compose):
    -   DB_HOST: mysql
    -   DB_DATABASE: fieldma_2_db
    -   DB_USERNAME: fieldma_2_user
    -   DB_PASSWORD: fieldma_2_pass

## Stop and Remove All Docker Resources

To stop all running containers, remove containers, networks, images, volumes, and any orphaned resources created by `docker-compose`, use:

```sh
docker compose down --rmi all --volumes --remove-orphans
```

**Explanation:**

-   `--rmi all`: Removes all images used by any service.
-   `--volumes`: Removes named volumes declared in the `volumes` section of the Compose file and anonymous volumes attached to containers.
-   `--remove-orphans`: Removes containers for services not defined in the Compose file.

This command is useful for a complete cleanup of your Docker environment related to this project.
