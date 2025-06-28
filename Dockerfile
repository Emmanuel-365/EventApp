# Stage 1: Build frontend assets
FROM node:lts-alpine as frontend_builder

WORKDIR /app_frontend

# Install git, needed for some yarn dependencies
RUN apk add --no-cache git

COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

COPY . .
RUN yarn build

# Stage 2: Setup PHP application
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
# libzip-dev, libpng-dev, libjpeg-turbo-dev, freetype-dev for GD/images
# icu-dev for intl
# supervisor for process management (optional, can use it for queue worker or cron)
RUN apk add --no-cache \
    bash \
    curl \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    supervisor \
    mysql-client \
    # For Redis CLI, if needed for debugging
    redis

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    bcmath \
    opcache \
    pdo_mysql \
    pcntl \
    exif \
    zip \
    sockets

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer globally
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Set recommended PHP.ini settings
# See https://github.com/docker-library/php/blob/master/README.md#configuration
# And https://laravel.com/docs/11.x/deployment#php-opcache
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'post_max_size = 100M'; \
    echo 'memory_limit = 256M'; \
    echo 'max_execution_time = 300'; \
} > /usr/local/etc/php/conf.d/zz-laravel-optimizations.ini


# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-plugins --no-scripts --no-dev --prefer-dist --optimize-autoloader

# Copy application code (excluding files handled by .dockerignore)
COPY . .

# Copy built frontend assets from the frontend_builder stage
COPY --chown=www-data:www-data --from=frontend_builder /app_frontend/public/build /var/www/html/public/build
COPY --chown=www-data:www-data --from=frontend_builder /app_frontend/public/hot /var/www/html/public/hot

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint script and make it executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port 9000 and set the entrypoint and default command
EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
