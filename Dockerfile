FROM php:8.1.31-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libmariadb-dev \
    unzip \
    && docker-php-ext-install pdo_mysql mysqli \
    && docker-php-ext-enable pdo_mysql mysqli

COPY login.php /var/www/html/
COPY logout.php /var/www/html/
COPY quiz.php /var/www/html/
COPT index.php /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
