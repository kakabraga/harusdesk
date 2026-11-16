# Usa a imagem oficial do PHP 8.4 com FPM
FROM php:8.4-fpm

# Atualiza pacotes e instala dependências do sistema necessárias
# para compilar as extensões PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instala extensões nativas do PHP necessárias pro Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    bcmath \
    fileinfo \
    opcache

# Instala a extensão Redis via pecl e ativa
RUN pecl install redis \
    && docker-php-ext-enable redis

# Copia o binário do Composer da imagem oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia configuração customizada do PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copia os arquivos do Composer primeiro para aproveitar cache de camadas
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Copia o código da aplicação
COPY . .

# Gera o autoload otimizado e garante estrutura de diretórios do Laravel
RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && mkdir -p /var/www/.config/psysh && chmod -R 777 /var/www/.config

USER www-data

EXPOSE 8080

CMD ["sh", "-c", "if [ -n \"$RAILWAY_ENVIRONMENT\" ] || [ -n \"$PORT\" ]; then php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}; else php-fpm; fi"]
