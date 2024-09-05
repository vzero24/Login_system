# Use the official PHP-Apache image
FROM php:8.1-apache

# Set working directory in the container
WORKDIR /var/www/html

# Install necessary PHP extensions for MySQL and file uploads
RUN docker-php-ext-install mysqli

# Copy the current directory contents to the container's working directory
COPY . /var/www/html/

# Set file permissions
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html

# Enable Apache mod_rewrite (optional, but useful for routing)
RUN a2enmod rewrite

# Expose port 80 to access the web server
EXPOSE 80
