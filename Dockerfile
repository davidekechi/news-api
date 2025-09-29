FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \ 
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    nano \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first
COPY composer.json composer.lock ./

# Install dependencies without running scripts that require Laravel to be configured
RUN composer install --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Copy environment file if it doesn't exist
COPY .env.example .env

# Generate optimized autoloader and run post-install scripts
RUN composer dump-autoload --optimize && \
    php artisan key:generate --ansi

CMD ["php-fpm"]
