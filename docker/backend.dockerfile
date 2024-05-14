FROM php:8.2-fpm

# set your user name, ex: user=bernardo
ARG user=laravel
ARG uid=1000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libgd-dev \
    jpegoptim optipng pngquant gifsicle \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nano 


# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets zip intl

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

###########################################################################
# Install Redis
###########################################################################
# RUN pecl install -o -f redis \
#     &&  rm -rf /tmp/pear \
#     &&  docker-php-ext-enable redis

###########################################################################
# Install NodeJS For Build Frontend
###########################################################################

# Download and install NodeSource binary installer
# RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -

# Install specific Node.js version (replace 18 with your desired version)
# RUN apt-get install -y nodejs

# Install npm
# RUN apt-get install -y npm


###########################################################################
# Work Directory
###########################################################################
WORKDIR /var/www

# Copy custom configurations PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

USER $user
