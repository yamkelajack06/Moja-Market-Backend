# Use the official PHP-Apache image
FROM php:8.2-apache

# Copy all files from the current directory into the web root
COPY . /var/www/html/

# Expose port 80
EXPOSE 80