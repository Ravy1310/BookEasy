FROM php:8.3-cli

# System deps
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first (cache layer)
COPY composer.json composer.lock ./

# Install deps (no dev, optimize autoload)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    && composer dump-autoload --optimize

# Copy application code
COPY . .

# Storage permissions
RUN mkdir -p storage/framework/{cache,sessions,testing,views} \
    && mkdir -p bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
