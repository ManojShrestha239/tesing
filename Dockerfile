# Build stage
FROM php:8.2-fpm-alpine AS builder

# Install build dependencies and runtime libraries
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    && apk add --no-cache \
    freetype \
    libjpeg-turbo \
    libpng \
    libzip \
    oniguruma \
    libxml2

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock /var/www/

# Install dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --no-progress \
    && composer clear-cache \
    && rm -rf /root/.composer

# Remove build dependencies after composer install
RUN apk del .build-deps

# Production stage
FROM php:8.2-fpm-alpine

# Install runtime dependencies in single layer and clean up
RUN apk add --no-cache \
    freetype \
    libjpeg-turbo \
    libpng \
    libzip \
    oniguruma \
    libxml2 \
    bash \
    netcat-openbsd \
    && rm -rf /var/cache/apk/* /tmp/*

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /var/www

# Copy vendor from builder
COPY --from=builder /var/www/vendor /var/www/vendor

# Copy only necessary application files
COPY --chown=www-data:www-data . /var/www

# Create directories and set permissions in single layer
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && rm -rf /tmp/* /var/tmp/*

EXPOSE 9000

CMD ["php-fpm"]
