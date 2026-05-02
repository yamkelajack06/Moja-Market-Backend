FROM php:8.2-apache

# Enable Apache modules needed for CORS and Routing
RUN a2enmod headers rewrite

# Copy your source code to the container
COPY . /var/www/html/

# Ensure the web server has permission to read the files
RUN chown -R www-data:www-data /var/www/html