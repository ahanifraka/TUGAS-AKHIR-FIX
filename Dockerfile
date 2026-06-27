FROM php:8.2-apache

# 1. Install sistem dependensi dan ekstensi PHP
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# 2. Install Node.js (untuk compile aset Vue/Inertia)
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# 3. Enable Apache rewrite module (wajib untuk route Laravel)
RUN a2enmod rewrite

# 4. Copy semua file project ke dalam container
COPY . /var/www/html

# 5. Build aset (npm install & run build)
RUN npm install && npm run build

# 6. Pindahkan DocumentRoot Apache ke folder 'public'
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 7. Berikan akses tulis ke folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Jalankan Apache
EXPOSE 80
CMD ["apache2-foreground"]