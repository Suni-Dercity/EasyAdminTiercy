#on part de l'image apache
FROM debian
#variables d'environnement pour configurer le proxy pour ce conteneur, configuration obsolète il faut indiquer le proxy dans le fichier de docker config.jso
#ENV http_proxy "http://proxy.univ-lemans.fr:3128"
#ENV https_proxy "https://proxy.univ-lemans.fr:3128"

#on met à jour la machine et on installe ce qui manque
RUN apt-get update -yq \
&& apt-get install vim -yq \
&& apt-get install apache2 -yq \
&& apt-get clean -y

COPY site.conf /etc/apache2/sites-available/site.conf

#on active le module proxy et on active la conf du site
RUN a2enmod proxy_fcgi \
&& a2ensite site.conf \
&& a2dissite 000-default.conf

WORKDIR /var/www/html/

#ENV APACHE_RUN_USER www-data
#ENV APACHE_RUN_GROUP www-data

#RUN cd /var/www
#&& chown -R www-data:www-data html/ \
#&& chmod -R 750 html/ \
#&& chmod ug+s html/ \
#&& cd html/ \
#&& umask 027

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]