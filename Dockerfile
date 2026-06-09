FROM ubuntu:24.04

RUN apt-get update && apt-get install --no-install-recommends software-properties-common -y
RUN apt-get update && apt-get install build-essential -y
RUN add-apt-repository ppa:ondrej/php

#Configura o timezone pro tzdata de forma automatica
RUN export DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y tzdata --no-install-recommends
RUN ln -fs /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime
RUN dpkg-reconfigure --frontend noninteractive tzdata

#php e dependencias
RUN apt-get update && apt-get install --no-install-recommends -y php8.4 php8.4-mysql php8.4-mongodb php8.4-gd php8.4-fpm php8.4-mbstring \
    php-json php8.4-curl php8.4-xml php8.4-zip php8.4-soap \
    php8.4-phar php8.4-intl php8.4-xmlreader php8.4-ctype \
    php8.4-dev php8.4-bcmath php8.4-xdebug


RUN php8.4 -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php8.4 -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
RUN php8.4 composer-setup.php
RUN php8.4 -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer


#mysql-client for feature/backup
RUN apt-get install mysql-client -y --no-install-recommends
RUN apt-get install git -y --no-install-recommends

#start
WORKDIR /app
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
