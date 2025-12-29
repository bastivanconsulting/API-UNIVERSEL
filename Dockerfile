# Dockerfile developed by Bastivan Consulting
FROM php:8.4-fpm-alpine

# Branding and Metadata
LABEL maintainer="Bastivan Consulting"
LABEL description="Universal API Foundation"

# Install Dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    git \
    unzip

# Install PHP Extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql zip opcache

# Install Redis
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev $PHPIZE_DEPS

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Work Directory
WORKDIR /var/www/html

# Copy Configs (Nginx, Supervisor, PHP) - Creating placeholders
# In a real scenario, these files would be copied from a 'docker/config' folder
# For this foundation, we will inline a simple nginx config via echo in the entrypoint or assume external mount
# or we create a simple one here.

RUN echo 'server { \
    listen 80; \
    server_name localhost; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/http.d/default.conf

# Copy Source Code
COPY . .

# Install PHP Deps (Prod)
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html/logs

# Supervisor Config to run Nginx and FPM
COPY supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
