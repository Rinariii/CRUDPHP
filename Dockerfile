FROM php:8.2-apache

# Install ekstensi mysqli
RUN docker-php-ext-install mysqli

# Copy file aplikasi ke dalam container
COPY . /var/www/html/

EXPOSE 80
