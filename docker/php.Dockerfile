FROM php:8.4-fpm-alpine

RUN apk add --no-cache zip pkgconf nginx \
    libpng libpng-dev \
    libjpeg-turbo libjpeg-turbo-dev \
    freetype freetype-dev \
    sqlite-dev zlib-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_sqlite \
    && apk del pkgconf libpng-dev libjpeg-turbo-dev freetype-dev sqlite-dev zlib-dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction

COPY . .

# Create storage directories and set permissions
RUN mkdir -p storage/app/public storage/framework/views storage/framework/cache storage/framework/sessions bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod -R 755 /var/www/html/app /var/www/html/routes /var/www/html/config /var/www/html/database /var/www/html/resources /var/www/html/public

# Create uploads directory
RUN mkdir -p public/storage \
    && chown -R www-data:www-data public/storage

# Configure PHP-FPM to listen on localhost
RUN sed -i 's/^;listen = 127.0.0.1:9000/listen = 127.0.0.1:9000/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^;listen.allowed_clients = 127.0.0.1/listen.allowed_clients = 127.0.0.1/' /usr/local/etc/php-fpm.d/www.conf

# Remove trustProxies (causes TypeError with PHP 8.4 + Symfony IpUtils when behind reverse proxy)
RUN sed -i "/trustProxies/d" /var/www/html/bootstrap/app.php

# Configure Nginx
RUN echo 'server { \
    listen 80; \
    server_name _; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        fastcgi_param REMOTE_ADDR $remote_addr; \
        fastcgi_param X-Forwarded-For $proxy_add_x_forwarded_for; \
        fastcgi_param X-Forwarded-Proto $scheme; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/http.d/default.conf

# Create start script
RUN printf '#!/bin/sh\nphp-fpm -D\nnginx -g "daemon off;"\n' > /start.sh && chmod +x /start.sh

EXPOSE 80
ENTRYPOINT ["/start.sh"]
