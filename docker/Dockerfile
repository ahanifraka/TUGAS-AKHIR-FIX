# Stage 1: Install composer dependencies
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --ignore-platform-reqs

# Stage 2: Build frontend assets with Vite
FROM node:22-alpine AS node_builder
WORKDIR /app

# Pass the public Turnstile site key into the Vite build (optional; runtime injection also supported)
ARG VITE_TURNSTILE_SITE_KEY
ENV VITE_TURNSTILE_SITE_KEY=${VITE_TURNSTILE_SITE_KEY}

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund --legacy-peer-deps

COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./
COPY resources ./resources
COPY public ./public

# Copy the vendor directory from the composer stage so that vite can find the ziggy package
COPY --from=composer /app/vendor ./vendor

RUN npm run build

# Stage 3: PHP-FPM app image
FROM php:8.4-fpm-alpine

# Receive the private Turnstile secret at build/run time
# ARG TURNSTILE_SECRET_KEY
# ENV TURNSTILE_SECRET_KEY=${TURNSTILE_SECRET_KEY}

WORKDIR /var/www/html

RUN apk add --no-cache \
    build-base \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    wget \
    netcat-openbsd

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        gd \
        opcache \
        intl \
        bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_children = 20" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.start_servers = 2" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.min_spare_servers = 1" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_spare_servers = 3" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_requests = 1000" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "clear_env = no" >> /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 www \
    && adduser -u 1000 -s /bin/sh -G www -D www

COPY --chown=www:www . /var/www/html

# Allow composer to work with copied repo during build
RUN git config --global --add safe.directory /var/www/html

# Bake PHP local overrides into the image
COPY --chown=www:www docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Copy entrypoint script
COPY --chown=www:www docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Install composer dependencies. This will use the copied vendor directory as a cache
# and run any necessary scripts.
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy built frontend assets from node stage
COPY --from=node_builder --chown=www:www /app/public/build /var/www/html/public/build
# Also stash a copy outside of any runtime-mounted path so we can sync into volumes on container start
COPY --from=node_builder /app/public/build /opt/public_build

# Stash public images so we can sync them into volumes on container start
COPY --chown=www:www public/images /opt/public_images

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose port 9000 and start php-fpm server
EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]