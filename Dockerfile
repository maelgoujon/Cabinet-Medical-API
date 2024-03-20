FROM php:7.4-apache
RUN a2enmod rewrite
RUN service apache2 restart
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli && docker-php-ext-install pdo_mysql