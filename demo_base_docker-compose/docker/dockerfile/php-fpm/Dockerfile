#on part de l'image php
FROM debian
#variables d'environnement pour configurer le proxy pour ce conteneur, configuration obsolète il faut indiquer le proxy dans le fichier de docker config.jso
#ENV http_proxy "http://proxy.univ-lemans.fr:3128"
#ENV https_proxy "https://proxy.univ-lemans.fr:3128"

#on met à jour la machine et on installe ce qui manque
#En production pas besoin de vim, en mode dev c'est quand même plus pratique pour débugguer
#après installation il faut faire un service apache2 reload (ce qui est le cas à la fin du fichier)
#pour pouvoir installer le module intl il faut installer le package libicu-dev
#pour l'installation de xdebug et de apcu il faut définir le proxy à la main

# Installation utilitaires
RUN apt-get update -yq \
&& apt-get install vim -yq \
&& apt-get install git -yq \
&& apt-get install wget gnupg2 ca-certificates apt-transport-https -yq

# Installation PHP + extensions
#php7.3-curl -> pb installation
RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add - \
&& echo "deb https://packages.sury.org/php/ stretch main" | tee /etc/apt/sources.list.d/php.list \
&& apt-get update -yq \
&& apt-get install php7.3 php7.3-apcu php7.3-fpm php7.3-intl php7.3-mbstring php7.3-mysql php7.3-xdebug php7.3-xml -yq \
&& apt-get clean -y \
&& phpenmod pdo_mysql

# Configuration PHP (FPM, CLI) + extensions
RUN mkdir /run/php/
COPY config/etc/php/7.3/fpm/php-fpm.conf /etc/php/7.3/fpm/php-fpm.conf
COPY config/etc/php/7.3/fpm/pool.d/www.conf /etc/php/7.3/fpm/pool.d/www.conf

COPY config/etc/php/7.3/mods-available/php-custom.ini /etc/php/7.3/mods-available/php-custom.ini
RUN ln -s /etc/php/7.3/mods-available/php-custom.ini /etc/php/7.3/fpm/conf.d/php-custom.ini
RUN ln -s /etc/php/7.3/mods-available/php-custom.ini /etc/php/7.3/cli/conf.d/php-custom.ini

# La config de xdebug est automatiquement ajoutée dans fpm et cli
COPY config/etc/php/7.3/mods-available/xdebug.ini /etc/php/7.3/mods-available/xdebug.ini


WORKDIR /var/www/html

EXPOSE 9000
EXPOSE 9001

RUN service php7.3-fpm start

CMD php-fpm7.3 -F