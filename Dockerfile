FROM php:8.2-apache

# Outils nécessaires pour compiler l'extension MongoDB (connexion chiffrée SSL)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libssl-dev pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP : PDO MySQL (base relationnelle) et MongoDB (base NoSQL)
RUN docker-php-ext-install pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Réécriture d'URL Apache (.htaccess)
RUN a2enmod rewrite