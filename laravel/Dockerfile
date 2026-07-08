# 1. Gunakan base image PHP 8.4 FPM yang stabil
FROM php:8.4-fpm

# 2. Install dependensi sistem Linux yang dibutuhkan oleh PHP dan ekstensi grafis GD
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# 3. Konfigurasi dan install ekstensi PHP (pdo_mysql dan GD)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd

# 4. Install Node.js & NPM (Dibutuhkan agar Vite 7 dan Tailwind 4 bisa berjalan)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# 5. Salin Composer terbaru ke dalam kontainer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Tentukan folder kerja di dalam kontainer Docker
WORKDIR /var/www/html

# 7. Salin seluruh source code backend Laravel ke dalam Docker
COPY . .

# Tambahkan konfigurasi git safe directory sebelum instalasi composer
RUN git config --global --add safe.directory /var/www/html

# 8. Install dependensi PHP (Composer) dan dependensi Node.js (NPM) untuk Vite (Cukup sekali saja)
RUN composer install --no-scripts --no-autoloader \
    && composer dump-autoload \
    && npm install

# 9. Buka port 8000 untuk server Laravel, dan port 5173 untuk Vite Live Reload
EXPOSE 8000 5173