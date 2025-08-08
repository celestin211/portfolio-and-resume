# Utilise l'image PHP 8.1 avec Apache
FROM php:8.1-apache

# Définir des variables d'environnement
ENV APP_NAME="PLATON"
ENV DEBIAN_FRONTEND=noninteractive

# Met à jour les paquets et installe les dépendances nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    locales \
    apt-utils \
    git \
    libicu-dev \
    g++ \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    libxslt-dev \
    unzip \
    vim-tiny \
    iputils-ping \
    curl && \
    curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && apt-get install -y yarn && \
    curl -sS https://getcomposer.org/installer | php -- && \
    mv composer.phar /usr/local/bin/composer && \
    docker-php-ext-configure intl && \
    docker-php-ext-install \
    pdo \
    pdo_mysql \
    opcache \
    intl \
    zip \
    calendar \
    dom \
    mbstring \
    gd \
    xsl && \
    # Nettoyer les paquets inutiles pour réduire la taille de l'image
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copie les fichiers de l'application dans le conteneur
COPY . /var/www/html

# Donner les permissions correctes
RUN chmod +x /var/www/html/bin/console

# Installe les dépendances PHP via Composer
WORKDIR /var/www/html
RUN composer install --no-interaction --optimize-autoloader

# Nettoie les fichiers temporaires pour réduire la taille de l'image
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Expose le port 80 pour l'application
EXPOSE 80

# Commande par défaut pour lancer Apache
CMD ["apache2-foreground"]
