FROM php:8.1.31-apache

WORKDIR /var/www/html

COPY backend.php /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
---
FROM php:8.1.31-apache

WORKDIR /var/www/html

COPY index.php /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
