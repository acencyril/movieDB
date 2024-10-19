FROM php:8.1-apache

# Configurer Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    curl \
    npm \
    && apt-get clean

# Installer Node.js
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

# Installer d'autres dépendances
RUN apt-get update \
    && apt-get install -y --no-install-recommends locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

# Installer Composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Installer les extensions PHP
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql gd opcache intl zip calendar dom mbstring zip gd xsl
RUN docker-php-ext-install bcmath
RUN pecl install apcu && docker-php-ext-enable apcu
RUN docker-php-ext-install sockets

# Installer nano
RUN apt-get update \
    && apt-get install -y --no-install-recommends nano

# Définir le répertoire de travail
WORKDIR /var/www/

# Copier tous les fichiers du projet dans le conteneur
COPY . .

# Installer les dépendances npm (y compris Webpack Encore)
# Installer les dépendances npm (y compris Webpack Encore)
RUN npm install && npm install @symfony/webpack-encore --save-dev

RUN ls -la /var/www/node_modules/.bin

# Installer Webpack Encore
RUN npm install @symfony/webpack-encore --save-dev

# Ajouter le dossier npm bin au PATH
ENV PATH="/var/www/node_modules/.bin:${PATH}"
