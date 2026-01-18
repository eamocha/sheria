FROM php:7.2.8-apache-stretch

# enable mod_rewrite & headers module
RUN a2enmod rewrite headers

# install php extensions
RUN mkdir -p /usr/share/man/man1 && \
    apt-get update && \
    apt-get install libreoffice libpng-dev libmcrypt-dev mysql-client libldap2-dev openjdk-8-jre-headless libbz2-dev libtidy-dev libicu-dev \
    libfreetype6-dev libjpeg-dev -y && \
    pecl install mcrypt-1.0.0 && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-configure gd --enable-gd-native-ttf --with-freetype-dir=/usr/include/freetype2 \
    --with-png-dir=/usr/include --with-jpeg-dir=/usr/include && \
    docker-php-ext-install ldap mysqli json mbstring bz2 tidy exif intl pdo pdo_mysql gd zip bcmath calendar

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install apt-utils libxml2-dev gnupg apt-transport-https \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Microsoft SQL Server Prerequisites
RUN apt-get update \
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/9/prod.list \
        > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get install -y --no-install-recommends \
        locales \
        apt-transport-https \
    && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
    && locale-gen \
    && apt-get update \
    && apt-get install -y --no-install-recommends unixodbc-dev

RUN ACCEPT_EULA=Y apt-get install -y msodbcsql17

RUN pecl install sqlsrv-5.8.1 pdo_sqlsrv-5.8.1 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

RUN docker-php-ext-enable mcrypt

# RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/ && \
#     cp /etc/apache2/mods-available/headers.load /etc/apache2/mods-enabled/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#custom php configuration
COPY config/custom.ini /usr/local/etc/php/conf.d/
