<?php
header("Content-type: text/html; charset=UTF-8");
session_start();

require("param/param.inc.php");
$bdd = new PDO("mysql:host=".MYHOST.";dbname=".MYDB,MYUSER,MYPASS);
$bdd->query("SET NAMES utf8");
$bdd->query("SET CHARACTER SET 'utf8'");

if(isset($_GET['IdUser']) AND $_GET['IdUser']>0)
{//Si l'IdUser est présent dans l'url et qu'il est supérieur à 0
    
    $getIdUser = intval($_GET['IdUser']);
    
    $reqUser = $bdd-> prepare('SELECT IdUser, PrenomUser, NomUser, MailUser, TelephoneUser, DateNaissanceUser, SexeUser, DescriptionUser, PointUser 
    FROM USERS 
    WHERE IdUser=?
    GROUP BY IdUser');
    $reqUser->execute(array($getIdUser));
    $userInfo = $reqUser->fetch();
    //On prépare une requête SQL afin de sélectionner les données d'un utilisateur en fonction de identifiant
    
    $reqUserVehicule = $bdd-> prepare('SELECT ModeleVehicule,ImmatriculationVehicule,CouleurVehicule
    FROM VEHICULES
    WHERE IdUser=?');
    $reqUserVehicule->execute(array($getIdUser));
    $userVehiculeInfo = $reqUserVehicule->fetch();
    //On prépare une requête SQL afin de sélectionner les données d'un véhicule en fonction de l'identifiant de l'utilisateur
    
    $reqUserMusiques = $bdd-> prepare('SELECT NomGenre
    FROM MUSIQUES
    INNER JOIN CHOISIT ON MUSIQUES.IdGenre=CHOISIT.IdGenre
    INNER JOIN USERS ON CHOISIT.IdUser=USERS.IdUser
    WHERE USERS.IdUser=?');
    $reqUserMusiques->execute(array($getIdUser));
    $userMusiquesInfo = $reqUserMusiques->fetch();
    //On prépare une requête SQL afin de sélectionner les goût musicaux d'un utilisateur en fonction de son l'identifiant
    
    $reqCovoitConducteur = $bdd-> prepare('SELECT IdCovoit, NomEvent, PointDepartCovoit, VilleDepartCovoit, RetourCovoit, DateCovoit
    FROM COVOIT
    INNER JOIN EVENT ON COVOIT.IdEvent=EVENT.IdEvent
    WHERE COVOIT.IdUser=?');
    $reqCovoitConducteur->execute(array($getIdUser));
    $covoitConducteurInfo = $reqCovoitConducteur->fetch();
    //On prépare une requête SQL afin de sélectionner les trajets d'un utilisateur lorsque celui ci est conducteur en fonction de son l'identifiant
    
    $reqCovoitPassager = $bdd-> prepare('SELECT RESERVE.IdCovoit, NomEvent, PointDepartCovoit, VilleDepartCovoit, RetourCovoit, DateCovoit
    FROM RESERVE
    INNER JOIN COVOIT ON RESERVE.IdCovoit=COVOIT.IdCovoit
    INNER JOIN EVENT ON COVOIT.IdEvent=EVENT.IdEvent
    WHERE RESERVE.IdUser=?');
    $reqCovoitPassager->execute(array($getIdUser));
    $covoitPassagerInfo = $reqCovoitPassager->fetch();
    //On prépare une requête SQL afin de sélectionner les trajets d'un utilisateur lorsque celui ci est passager en fonction de son l'identifiant
    
    $reqUserAvantages = $bdd-> prepare('SELECT AVANTAGES.DescriptionAvantage As DescriptionAvtg, EVENT.NomEvent AS NomEvt, EVENT.DateEvent AS DateEvt, EVENT.LieuEvent AS LieuEvt, EVENT.LienEvent AS LienEvt
    FROM AVANTAGES
    INNER JOIN POSSEDE_AVANTAGE ON AVANTAGES.IdAvantage=POSSEDE_AVANTAGE.IdAvantage
    INNER JOIN EVENT ON AVANTAGES.IdEvent=EVENT.IdEvent
    WHERE POSSEDE_AVANTAGE.IdUser=?');
    $reqUserAvantages->execute(array($getIdUser));
    $userAvantageInfo = $reqUserAvantages->fetch();
    //On prépare une requête SQL afin de sélectionner les avantages d'un utilisateur en fonction de son l'identifiant
    
    $reqUserAvis = $bdd-> prepare('SELECT USERS.PrenomUser AS PrenomUserNotant , USERS.NomUser AS NomUserNotant, NOTE_AVIS.Avis AS Avis, 
    NOTE_AVIS.Note AS Note, NOTE_AVIS.DateAvis AS DateAvis
    FROM USERS 
    INNER JOIN NOTE_AVIS ON USERS.IdUser=NOTE_AVIS.IdUser_Notant 
    WHERE NOTE_AVIS.IdUser_Note=? ');
    $reqUserAvis->execute(array($getIdUser));
    $userAvisInfo = $reqUserAvis->fetch();
    //On prépare une requête SQL afin de sélectionner les notes et avis d'un utilisateur en fonction de son l'identifiant
    
?>
<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8" />
    <title>Profil - fest'</title>
    <meta name="description" content="Consultez vos informations personnelles, vos points fest’, les avis que les membres vous ont laissé ou encore les trajets que vous avez réalisé ou consultez le profil des autres covoitureurs, découvrez leurs goûts musicaux, l’historique de leurs festivals, les avis des autres membres et leurs coordonnées" />
    <meta name="author" content="L'équipe Fest" />
    <link rel="stylesheet" href="css/style.css" type="text/css" />
    <link rel="stylesheet" href="css/styleProfil.css" type="text/css" />
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
</head>

<body>
    <header class="accueil">
        <nav>
            <img class="burger" src="images/menu.png" alt="Bouton ouverture du menu">
            <div class="menuCache">
                <img class="fermerMenu" src="images/croix.png" alt="Bouton fermeture du menu">
                <a href="index.php" class="aBurger">Accueil</a>
                <?php
                if(isset($_SESSION['IdUser']) AND $_SESSION['IdUser']>0){ $getIdUser = intval($_SESSION['IdUser']);
                //Si une session est en cours, l'item deconnexion est ajouté. ?>
                <a href="deconnexion.php" class="aBurger">Déconnexion</a>
                <?php } ?>
            </div>
        </nav>
        <a href="index.php"><img class="logoHeader" src="images/logoFest.png" alt="Logo Fest'"></a>
        <?php
            if(isset($_SESSION['IdUser']) AND $_SESSION['IdUser']>0){$getIdUser = intval($_SESSION['IdUser']);
            //Si une session est en cours, la cloche et l'icone utilisateur redirigent respectivement vers la page de notifications et vers le profil de l'utilisateur. ?>
        <img src="images/cloche.png" alt="Icône cloche de notifications" class="imgHeader">
        <a href="profil.php?IdUser=<?php echo $getIdUser;?>" class="iconesDroite"><img class="imgHeader2" src="images/utilisateur.png" alt="Icône profil utilisateur"></a>
        <?php } else {
            //Sinon, la cloche et l'icone utilisateur redirigent tout les deux vers la page de connexion.?>
        <a href="notifications.php" class="iconesDroite"><img class="imgHeader" src="images/cloche.png" alt="Icône cloche de notifications"></a>
        <a href="connexion.php" class="iconesDroite"><img class="imgHeader2" src="images/utilisateur.png" alt="Icône profil utilisateur"></a>
        <?php
            }
            ?>
    </header>
    <main class="mainProfil">
        <div class="boxProfil">
        <?php
        if(isset($_SESSION['IdUser']) AND $userInfo['IdUser']==$_SESSION['IdUser'])
        {//Si une session est en cours, on affiche un essage de bienvenue
        ?>
            <h1>Bienvenue <?php echo($userInfo['PrenomUser']);?></h1>
            <div class="boutonsBasProfil">
                <a class="boutonsTextesProfil" href="editionProfil.php">Editer mon profil</a>
                <a class="boutonsTextesProfil" href="deconnexion.php">Se déconnecter</a>
            </div>
            <?php
        }
        else
        {//Sinon on propose à l'utilisateur de noter ce profil ?>
            <h1><?php echo($userInfo['PrenomUser']);?> <?php echo($userInfo['NomUser']);?></h1>
            <div class="boutonsBasProfil">
                <?php $getIdUser=intval($_GET['IdUser']); ?>
                <a class="boutonsTextesProfil" href="depotAvis.php?IdUser=<?php echo $getIdUser;?>">Noter cet utilisateur</a> 
            </div>
            <?php
        }
        ?>
            <div>
                <div class="titre3">
                    <h2><?php if(isset($_SESSION['IdUser']) AND $userInfo['IdUser']==$_SESSION['IdUser']){ echo 'Mes ' ;}?>Informations Personnelles</h2>
                </div>
                
                <div class="infos">
                    <p class="infosPersos">Prénom</p>
                    <p class><?php echo ($userInfo['PrenomUser']); ?></p>
                </div>
                <div class="infos">
                    <p class="infosPersos">Nom</p>
                    <p><?php echo ($userInfo['NomUser']); ?></p>
                </div>
                <div class="infos">
                    <p class="infosPersos">Adresse mail</p>
                    <p><?php echo ($userInfo['MailUser']); ?></p>
                </div>
                <div class="infos">
                    <p class="infosPersos">Sexe</p>
                    <p><?php echo ($userInfo['SexeUser']); ?></p>
                </div>
                <div class="infos">
                    <p class="infosPersos">Date de naissance</p>
                    <p><?php echo ($userInfo['DateNaissanceUser']); ?></p>
                </div>
                <div class="infos">
                    <p class="infosPersos">Numéro de télephone</p>
                    <p><?php echo ($userInfo['TelephoneUser']); ?></p>
                </div>
                <div class="infos">
                    <p class="infosPersos">Description</p>
                    <p><?php echo ($userInfo['DescriptionUser']); ?></p>
                </div>
                <div class="infos">
                    <p class="infosPersos">Points eco'</p>
                    <p><?php echo ($userInfo['PointUser']); ?></p>
                </div>
            </div>

            <div class="boxInfosVehicule">
                <h2><?php if(isset($_SESSION['IdUser']) AND $userInfo['IdUser']==$_SESSION['IdUser']){ echo 'Mes ' ;}?>Véhicules</h2>
                <div class="infosVehicule">
                    <?php
                    while($userVehiculeInfo != false) 
                    {//Tant qu'il existe toujours un véhicule
                        $modeleVehicule=$userVehiculeInfo['ModeleVehicule'];
                        $immatriculationVehicule=$userVehiculeInfo['ImmatriculationVehicule'];
                        $couleurVehicule=$userVehiculeInfo['CouleurVehicule'];
                        echo ("<div class='boxVehicules'><p>Modèle : ".$modeleVehicule."</p><p>Immatriculation : ".$immatriculationVehicule."</p><p>Couleur :  ".$couleurVehicule."</p></div>");
                        $userVehiculeInfo = $reqUserVehicule->fetch();
                        //Ligne suivante
                    }
                    ?>
                </div>
            </div>

            <div class="goutsMusicaux">
                <h2><?php if(isset($_SESSION['IdUser']) AND $userInfo['IdUser']==$_SESSION['IdUser']){ echo 'Mes ' ;}?>Goûts Musicaux</h2>
                <div class="boxGenres">
                    <?php while($userMusiquesInfo!=false)
                    {//Tant qu'il existe toujours un genre musical
                        echo ("<p>".$userMusiquesInfo['NomGenre']."</p>");
                        $userMusiquesInfo = $reqUserMusiques->fetch();
                        //Ligne suivante
                    } 
                    ?>
                </div>
            </div>

            <div>
                <h2 class="titre3"><?php if(isset($_SESSION['IdUser']) AND $userInfo['IdUser']==$_SESSION['IdUser']){ echo 'Mes ' ;}?>Trajets</h2>
                <h3>En tant que conducteur</h3>
                <?php while($covoitConducteurInfo!=false)
                    {//Tant qu'il existe toujours un covoiturage en tant que conducteur
                        $now = time();
                        //time() retourne l'heure courante
                        $dateCovoitConducteur = $covoitConducteurInfo['DateCovoit'].' 00:00:00';
                        //On effectue une requête SQL afin de connaître la date du covoiturage courant puis on la transforme dans le format AAAA-MM-JJ hh:mm:ss pour utiliser la fonction strtotime()
                        $date2 = strtotime($dateCovoitConducteur);
                        //On transforme le nouveau format de la date du covoiturage en seconde, dans le même format que la variable $now
                        $diffDate  = ($now - $date2);
                        //On calcule la différence de seconde entre la date d'aujourd'hui et la date du covoiturage
                        if($diffDate>0)
                        { //On affiche uniquement les covoiturages qui sont passés ?>
                            <div class="trajets">
                                <p><strong><?php echo($covoitConducteurInfo['NomEvent']);?></strong></p>
                                <p><?php echo($covoitConducteurInfo['DateCovoit']);?>
                                </p>
                                <p>Départ : <?php echo($covoitConducteurInfo['VilleDepartCovoit']);?></p>
                                <p>Aller-Retour :
                                    <?php 
                                        if($covoitConducteurInfo['RetourCovoit']==1)
                                        {
                                            echo('Oui');
                                        }
                                        else
                                        {
                                            echo('Non');
                                        }?>
                                </p>
                            </div>
                        <?php }  
                        $covoitConducteurInfo=$reqCovoitConducteur->fetch();
                        //Ligne suivante
                    }
                    ?>


                <h3>En tant que passager</h3>
                <?php while($covoitPassagerInfo!=false)
                    {//Tant qu'il existe toujours un covoiturage en tant que passager
                        $now = time();
                        //time() retourne l'heure courante
                        $dateCovoitPassager = $covoitPassagerInfo['DateCovoit'].' 00:00:00';
                        //On effectue une requête SQL afin de connaître la date du covoiturage courant puis on la transforme dans le format AAAA-MM-JJ hh:mm:ss pour utiliser la fonction strtotime()
                        $date2 = strtotime($dateCovoitPassager);
                        //On transforme le nouveau format de la date du covoiturage en seconde, dans le même format que la variable $now
                        $diffDate  = ($now - $date2);
                        //On calcule la différence de seconde entre la date d'aujourd'hui et la date du covoiturage
                        if($diffDate>0)
                        { //On affiche uniquement les covoiturages qui sont passés ?>
                            <div class="trajets">
                                <p><strong><?php echo($covoitPassagerInfo['NomEvent']);?></strong></p>
                                <p><?php echo($covoitPassagerInfo['DateCovoit']);?>
                                </p>
                                <p>Départ : <?php echo($covoitPassagerInfo['VilleDepartCovoit']);?></p>
                                <p>Aller-Retour :
                                    <?php 
                                        if($covoitPassagerInfo['RetourCovoit']==1)
                                        {
                                            echo('Oui');
                                        }
                                        else
                                        {
                                            echo('Non');
                                        }?>
                                </p>
                            </div>
                        <?php }
                        $covoitPassagerInfo=$reqCovoitPassager->fetch();
                        //Ligne suivante
                    }
                    ?>

            </div>

            <div>
                <div class="titre3">
                    <h2 class="titreAvis"><?php if(isset($_SESSION['IdUser']) AND $userInfo['IdUser']==$_SESSION['IdUser']){ echo 'Mes ' ;}?>Avis</h2>
                </div>
                <?php while($userAvisInfo!=false){ 
                //Tant qu'il existe toujours un avis ?>
                <div class="avisBox">
                    <p>"<?php echo($userAvisInfo['Avis']);?>" -
                        <?php echo($userAvisInfo['PrenomUserNotant']);?>
                        <?php echo($userAvisInfo['NomUserNotant']);?></p>
                    <p>Note :
                        <?php echo($userAvisInfo['Note']);?>/5</p>
                    <p>Noté le : <?php echo($userAvisInfo['DateAvis']);?></p>
                </div>
                <?php 
                $userAvisInfo=$reqUserAvis->fetch(); 
                //Ligne suivante
                }
                ?>
            </div>

            <?php
            if(isset($_SESSION['IdUser']) AND $userInfo['IdUser']==$_SESSION['IdUser'])
            {//Si une session est en cours on affiche les avantages de l'utilisateur
            ?>
            <div class="boxAvantages">
                <h2>Mes Avantages</h2>
                <div class="avantages">
                    <?php while($userAvantageInfo!=false)
                    { //Tant qu'il existe toujours un avantage ?>
                    <div class="avantagesBox">
                        <p>Festival : <a target='_blank' href="<?php echo($userAvantageInfo['LienEvt']);?>"><?php echo($userAvantageInfo['NomEvt']);?></a> </p>
                        <p>Date du festival : <?php echo($userAvantageInfo['DateEvt']);?></p>
                        <p>Lieu du Festival : <?php echo($userAvantageInfo['LieuEvt']);?></p>
                        <p>Description de l'avantage : <?php echo($userAvantageInfo['DescriptionAvtg']);?></p>
                    </div>
                    <?php 
                    $userAvantageInfo=$reqUserAvantages->fetch(); 
                    //Ligne suivante
                    }
                    ?>
                </div>
            </div>
            <?php
            }
        ?>
        </div>
    </main>
    <footer>
        <div class="liensFooter">
            <a href="notreService.php">Notre service</a>
            <a href="mentionsLegales.php">Mentions légales</a>
            <a href="aideFaq.php">Aide/Foire aux questions</a>
            <a href="contact.php">Contact</a>
        </div>
    </footer>
    
    <script src="js/script.js"></script>

</body>

</html>
<?php
    $bdd = null ;
}
else
{
    $bdd = null ;
    header('Location: index.php');
}
?>
