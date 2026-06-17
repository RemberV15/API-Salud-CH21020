# Usar la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilitar el módulo rewrite de Apache (Vital para Slim Framework)
RUN a2enmod rewrite

# Instalar las extensiones de MySQL para usar PDO
RUN docker-php-ext-install pdo pdo_mysql

# Instalar herramientas necesarias y Composer
RUN apt-get update && apt-get install -y git unzip zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache para que apunte directamente a la carpeta public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar todo el código de la API al contenedor
COPY . /var/www/html/

# Instalar las dependencias de Slim con Composer
RUN composer install

# Dar los permisos correctos a los archivos
RUN chown -R www-data:www-data /var/www/html