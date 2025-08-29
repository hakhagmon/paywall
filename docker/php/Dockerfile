# docker/php/Dockerfile
FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nano \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


# Рабочая директория
WORKDIR /var/www

# Копируем composer.json и устанавливаем зависимости
COPY composer.json composer.json
#COPY composer.lock composer.lock

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-progress --no-suggest

# Копируем остальные файлы
COPY . .

# Установка прав
RUN chown -R www-data:www-data /var/www

# Запуск
USER www-data