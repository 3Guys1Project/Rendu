FROM php:8.3-apache

# Configuration Apache
RUN echo "ServerName localhost" | tee -a /etc/apache2/apache2.conf

# Mise à jour des paquets et installation des dépendances nécessaires
RUN apt-get update && \
    apt-get install -y \
        acl \
        nano \
        vim \
        libpq-dev \
        libsqlite3-dev \
        libaio1 \
        libsodium-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        unzip \
        git \
        wget && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd pdo pdo_mysql pdo_pgsql pdo_sqlite sodium

# Installation de Composer
COPY --from=composer:2.7.9 /usr/bin/composer /usr/bin/composer

# Installation du client Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Installation de Node.js
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Définir le répertoire de travail
WORKDIR /var/www/html

# Gestion des certificats SSL
RUN mkdir /etc/ssl/certs/server
RUN mkdir /etc/ssl/private/server
RUN chown www-data /etc/ssl/certs/server/
RUN chown www-data /etc/ssl/private/server/
COPY docker-config/ssl/cert.pem /etc/ssl/certs/server/
COPY docker-config/ssl/key.pem /etc/ssl/private/server/
RUN chown www-data /etc/ssl/certs/server/cert.pem
RUN chown www-data /etc/ssl/private/server/key.pem
COPY docker-config/apache/site.conf /etc/apache2/sites-available/000-default.conf

# Définir les permissions sur le répertoire de l'application
RUN chown www-data /var/www/html/
RUN chown -R www-data .

# Activer les modules Apache
RUN a2enmod rewrite
RUN a2enmod ssl
RUN service apache2 start

# Copier le script d'entrée et donner les permissions d'exécution
COPY ./shared/public_html/annuaire/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Définir le script d'entrée
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
