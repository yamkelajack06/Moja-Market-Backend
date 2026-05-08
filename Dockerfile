FROM php:8.2-apache

# Enable rewrite and headers modules
RUN a2enmod rewrite headers

# Update Apache config to allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Install PostgreSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql pdо_pgsql

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html