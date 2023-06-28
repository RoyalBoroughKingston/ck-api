# Set base image.
FROM ubuntu:22.04

# Set maintainer to Ayup Digital.
LABEL maintainer="Ayup Digital"

# Install generic software.
RUN apt update \
    && apt install -y locales nginx curl zip unzip git software-properties-common supervisor \
    && locale-gen en_GB.UTF-8

# Set needed environment variables before installing PHP.
ENV DEBIAN_FRONTEND noninteractive
ENV LANG en_GB.UTF-8
ENV LANGUAGE en_GB:en
ENV LC_ALL en_GB.UTF-8

# Install PHP.
RUN apt-add-repository -y ppa:ondrej/php \
    && apt update \
    && apt install -y php8.1-fpm php8.1-cli php8.1-gd php8.1-mysql php8.1-redis \
       php8.1-imap php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --version=2.2.3 --filename=composer \
    && mkdir /run/php

# Cleanup.
RUN apt remove -y --purge software-properties-common \
    && apt -y autoremove \
    && apt clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Redirect NGINX output to stdout and stderr.
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Copy configuration files over for installed software.
COPY etc/nginx/nginx.conf /etc/nginx/nginx.conf
COPY etc/nginx/sites-available/default /etc/nginx/sites-available/default
COPY etc/php/8.1/fpm/php-fpm.conf /etc/php/8.1/fpm/php-fpm.conf
COPY etc/supervisor/conf.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy the bootstrap script over and make executable.
COPY usr/local/bin/start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

# Expose port 80 for HTTP access.
EXPOSE 80

# Set the working directory to the project root.
WORKDIR /var/www/html

# Set default command to the bootstrap script.
CMD ["start-container"]
