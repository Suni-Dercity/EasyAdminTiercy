#on part de l'image debian version buster
FROM debian:buster

# on installe quelques paquets utiles
RUN apt-get update \
  && apt-get install -y vim \
    apache2 \
    net-tools

#on modifie le fichier de configuration d'apache2
COPY site.conf /etc/apache2/sites-available/site.conf
RUN a2ensite site.conf \
    && a2dissite 000-default.conf

WORKDIR /var/www/html

CMD ["apache2ctl", "-D", "FOREGROUND"]