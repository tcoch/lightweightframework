FROM php:8.1-apache AS upstream

FROM upstream AS base_image

# -- Linux basic packages install
# Needed to activate PHP extensions later on
RUN apt-get update && apt install --no-install-recommends -y \
	libcurl4-openssl-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    unzip

# -- Install necessary PHP extensions
RUN docker-php-ext-install curl zip \
    && docker-php-ext-enable curl zip
RUN docker-php-source delete

# -- Composer install
RUN apt install git -y # Git is mandatory for composer to work
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# -- php.ini definition
# Activate output buffering
RUN echo "output_buffering = On" >> /usr/local/etc/php/conf.d/lightweightframework.ini
# Switch apache configuration towards /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-enabled/000-default.conf
RUN sed -i '/<\/VirtualHost>/i\
            <Directory /var/www/html/public>\n\
                    AllowOverride All\n\
                    Require all granted\n\
            </Directory>\n' /etc/apache2/sites-enabled/000-default.conf

# -- Activate mod_rewrite (required for .htaccess)
RUN a2enmod rewrite

# -- Clean it up
RUN apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/ /tmp/* /var/tmp/*

FROM base_image AS lightweightframework_dev_image

# -- Install XDEBUG
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN docker-php-source delete

# -- Activate xDebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/lightweightframework.ini
