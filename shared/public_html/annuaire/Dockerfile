FROM php:8.3-apache

RUN echo "ServerName localhost" | tee -a /etc/apache2/apache2.conf

RUN apt-get update && \
    apt-get install -y \
		acl \
		nano \
		vim \
        libpq-dev \
        libsqlite3-dev \
        libaio1 \
		libsodium-dev \
        unzip \
		git \
        libgd-dev \
        wget && \
    docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite sodium gd

COPY --from=composer:2.7.9 /usr/bin/composer /usr/bin/composer

RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

WORKDIR /var/www/html

RUN mkdir /etc/ssl/certs/server
RUN mkdir /etc/ssl/private/server
RUN chown www-data /etc/ssl/certs/server/
RUN chown www-data /etc/ssl/private/server/
COPY docker-config/ssl/cert.pem /etc/ssl/certs/server/
COPY docker-config/ssl/key.pem /etc/ssl/private/server/
RUN chown www-data /etc/ssl/certs/server/cert.pem
RUN chown www-data /etc/ssl/private/server/key.pem
COPY docker-config/apache/site.conf /etc/apache2/sites-available/000-default.conf

RUN chown www-data /var/www/html/
RUN chown -R www-data .

RUN a2enmod rewrite
RUN a2enmod ssl
RUN service apache2 start

COPY ./shared/public_html/annuaire/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]