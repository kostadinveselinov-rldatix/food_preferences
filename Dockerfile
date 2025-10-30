# Base PHP image with Apache
FROM dunglas/frankenphp:1-php8.3

# Create a user with UID 1000 and GID 1000 (if not already exists)
RUN groupadd -g 1000 appgroup && \
    useradd -m -u 1000 -g 1000 appuser

# Copy Zscaler certificate and update certificates
COPY Zscaler.pem /usr/local/share/ca-certificates/Zscaler.crt
RUN update-ca-certificates

# Install system dependencies and PHP extensions in fewer layers
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    libxml2-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql dom bcmath sockets \
    && pecl install redis xdebug \
    && docker-php-ext-enable redis xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy PHP configuration
COPY xdebug.ini /usr/local/etc/php/conf.d/

# Set working directory
WORKDIR /app

# Copy dependency files first for better caching
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .
COPY ./config/ /config

# Make scripts executable and create necessary directories
RUN chmod +x docker-entrypoint.sh wait-for-it.sh \
    && mkdir -p /app/src/reports

# Switch to non-root user
USER appuser

# Configure FrankenPHP worker mode
ENV FRANKENPHP_CONFIG="worker ./public/index.php"

# Expose FrankenPHP default ports
EXPOSE 80 443
