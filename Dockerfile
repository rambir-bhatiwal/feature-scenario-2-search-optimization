FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev libpq-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install and enable Redis safely
RUN if ! php -m | grep -q redis; then \
      pecl install redis && docker-php-ext-enable redis; \
    else \
      echo "Redis already installed, skipping..."; \
    fi

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html
