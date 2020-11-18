# Structure de projet full Symfony

---

Projet configuré à partir de la pile Docker pour les projets écrite par Rémi Venant, <[remi.venant@univ-lemans.fr](mailto://remi.venant@univ-lemans.fr)>*  

---

# Généralités

Ce projet fournit une structure de base pour des projets de développement d'application web reposant sur Symfony coté *backend* et coté *frontend*, et reposant sur des bases de données SQL et NoSQL orientées Document.

Ce projet est composé de 5 conteneurs :
- un conteneur mariadb ;
- un conteneur mongodb ; 
- un conteneur serveur web (nginx) ;
- un conteneur symfony (php-fpm+symfony+composer) ;
- un conteneur phpmyadmin ;

Le code source de l'application web est contenu dans le dossier /site. 
Ce dossier a été "peuplé" avec la commande suivante exécutée dans le dossier /var/www/ du conteneur symfony: 
<pre><code>composer create-project symfony/website-skeleton ./site
</code></pre>

Il a été également généré un site de base pour des applications symfony de type API. Ce site est présent dans le dossier ./api. Ce dossier a été généré avec la commande suivante : 
<pre><code>composer create-project symfony/skeleton api
</code></pre>

**Si vous souhaitez partir sur cette base, il faut alors renommer le dossier ./site en ce que vous voulez et renommer le dossier ./api en ./site**

Le fichier ./site/.env est vide. Les variables d'environnement sont définies dans le fichier ./docker/environment/dev|prod/symfony.env

En mode dev, le serveur web est accessible à l'adresse : http://127.0.0.1:8080. 

En mode prod, le serveur web est accessible à l'adresse : http://127.0.0.1:80. 

Le script bash ./dcTool permet de gérer les deux configurations possibles : dev ou prod. Et il permet de gérer la configuration du proxy lorsqu'on est dans le réseau de l'IUT. 
il faut ajouter l'option -lmp après dev ou prod pour configurer le proxy pour la pile Docker. 
In fine, la commande exécutée est une commande `docker-compose`

## Commandes utiles

Pour exécuter les commandes indiquées dans cette section, si vous êtes sous Windows clic droit sur le dossier racine de ce projet puis Git bash. 

**ATTENTION** : si vous souhaitez utiliser cette pile Docker pour plusieurs projets sur un même ordinateur, pour s'y retrouver entre les différents conteneurs et volumes générés et afin d'éviter d'éventuels conflits entre les différents projets, il est fortement conseillé d'indiquer dans chacune des commandes `./dcTool` (option -p) le nom du projet.   

### lancement pile Docker
<pre><code>./dcTool dev -p webApp1 up 
</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 up 
</code></pre>


* Crée le réseau virtuel et les différents volumes gérés par Docker (s'ils n'existent pas déjà).
* Lance les différents conteneurs.
* Tous les conteneurs affichent leur sortie dans le terminal courant. Le terminal courant étant occupé, un autre terminal doit être utilisé pour des commandes parallèles (ex.: arrêt de la pile).

**RQ** : au démarrage du conteneur symfony un composer install est exécuté. Le temps d'installation peut être long, dès que les lignes suivantes apparaissent : 

<pre><code>symfony_1     | [15-Sep-2020 09:18:18] NOTICE: fpm is running, pid 1
symfony_1     | [15-Sep-2020 09:18:18] NOTICE: ready to handle connections</code></pre>

votre site web est opérationnel. 

### arrêt pile Docker
<pre><code>./dcTool dev -p webApp1 down 
</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 down 
</code></pre>

* Stoppe et supprime les différents conteneurs ainsi que le réseau virtuel, 
* les volumes sont conservés (cela évite la ré-installation des bundles de symfony et la ré-initialisation des bdd).
<pre><code>./dcTool dev -p webApp1 down -v
</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 down -v 
</code></pre>

* Stoppe et supprime les différents conteneurs, le réseau virtuel, et les volumes.

### vérification de l'état de la pile

<pre><code>./dcTool dev -p webApp1 ps
</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 ps 
</code></pre>

Lorsque la pile est lancée, tous les conteneurs devraient être "up". 

### installation des dépendances 
<pre><code>./dcTool dev -p webApp1 run --rm symfony composer require symfony/maker-bundle --dev
</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 run --rm symfony composer require symfony/maker-bundle --dev
</code></pre>


### connexion sur le conteneur symfony (le conteneur doit être lancé)
<pre><code>./dcTool dev -p webApp1 exec symfony /bin/bash
</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 exec symfony /bin/bash
</code></pre>

À la différence de la commande précédente, cette commande exécute la commande "/bin/bash" au sein du conteneur symfony de la pile Docker (remarquez la différence de commande docker-compose ; "run" préalablement et "exec" ici). Il est donc nécessaire que la pile soit lancée au préalable.

### quelques commandes composer 

Avec symfony, de nombreuses commandes vous sont proposées ( ex. : créer un contrôleur, une entité gérée par doctrine...). Ces commandes sont tout à fait exécutables via Docker.

Par exemple, les commandes suivantes permettent de créer une entité, préparer la migration de schéma, effectuer la migration ou encore créer le schéma en BDD. 
<pre><code>./dcTool dev -p webApp1 run --rm symfony php bin/console make:entity 
./dcTool dev -p webApp1 run --rm symfony php bin/console make:migration
./dcTool dev -p webApp1 run --rm symfony php bin/console doctrine:migrations:migrate
./dcTool dev -p webApp1 run --rm symfony php bin/console doctrine:schema:create
</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 run --rm symfony php bin/console make:entity 
./dcTool dev -lmp -p webApp1 run --rm symfony php bin/console make:migration
./dcTool dev -lmp -p webApp1 run --rm symfony php bin/console doctrine:migrations:migrate
./dcTool dev -lmp -p webApp1 run --rm symfony php bin/console doctrine:schema:create
</code></pre>


## Accès à la base de données Mariadb

Cela se fait via PhpMyAdmin, à l'adresse suivante : http://127.0.0.1:8888

Les paramétrages de connexion sont indiquées dans les fichiers d'environnement : 

* ./docker/environment/dev|prod/mysql.env
* ./docker/environment/dev|prod/phpmyadmin.env

## Accès à la base de données MongoDB

Si une instance de PhpMyAdmin vous est fournie en mode dev, il n'y a pas d'équivalent pour MongoDB.

Vous pouvez toutefois vous connecter à MongoDB en ligne de commande :

<pre><code>./dcTool dev -p webApp1 exec mongodb mongo</code></pre>
si on est connecté dans le réseau filaire de l'IUT
<pre><code>./dcTool dev -lmp -p webApp1 exec mongodb mongo
</code></pre>

Les paramétrages de connexion sont indiquées dans le fichier d'environnement : 

* ./docker/environment/dev|prod/mongo.env

## Mise en place et Test rapide d'un nouveau projet Symfony

- Vérifiez que Docker soit bien installé et lancé sur votre machine
- Vérifiez que vous ayez les droits de manipuler docker (ex.: `docker ps`)
- Vérifiez qu'aucun programme sur votre machine ne soit déjà à l'écoute du port 80, 8080 et 8888
- Clonez le dépôt : `git clone https://github.com/IUT-LAVAL-MMI/supportCoursDocker.git`
- Copiez le dossier du dépôt ./demo_base_symfony où vous voulez sur votre ordinateur
- Renommez ce dossier comme vous voulez
- Placez vous dans ce nouveau dossier
- Lancez l'application en mode développement `./dcTool dev -p maNewApp up` ou `./dcTool dev -lmp -p maNewApp up`
- Attendez que l'ensemble des conteneurs soient correctement lancés. Normalement, lorsque l'application sera prête, la commande `./dcTool dev ps` ou `./dcTool dev -lmp ps` devrez vous indiquer que les conteneurs http, mongodb, mysqldb, symfony sont lancés (*Up*) . Le premier lancement est généralement long (rapatriement des images, construction de l'image symfony, installation des dépendances des application *backend*)
- Une fois l'ensemble des conteneurs lancés, vous devriez pouvoir tester l'application depuis votre navigateur à l'adresse [http://localhost:8080](http://localhost:8080)
