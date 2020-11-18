<?php
header("Content-type: text/html; charset=UTF-8");
session_start();

require ("param/param.inc.php");
$bdd = new PDO("mysql:host=".MYHOST.";dbname=".MYDB,MYUSER,MYPASS);
$bdd->query("SET NAMES utf8");
$bdd->query("SET CHARACTER SET 'utf8'");

if(isset($_SESSION['IdUser']))
{//Si une session est en cours
    $getIdUser = intval($_SESSION['IdUser']);
    
    $reqUser = $bdd-> prepare('SELECT IdUser, MailUser, TelephoneUser, DescriptionUser, MotDePasseUser
    FROM USERS 
    WHERE IdUser=?
    GROUP BY IdUser');
    $reqUser->execute(array($getIdUser));
    $userInfo = $reqUser->fetch();
    //On prépare une requête SQL afin de sélectionner les données d'un utilisateur en fonction de identifiant
    
    $reqUserVehicule = $bdd-> prepare('SELECT ModeleVehicule,ImmatriculationVehicule,CouleurVehicule,IdVehicule
    FROM VEHICULES
    WHERE IdUser=?');
    $reqUserVehicule->execute(array($getIdUser));
    $userVehiculeInfo = $reqUserVehicule->fetch();
    //On prépare une requête SQL afin de sélectionner les données d'un véhicule en fonction de l'identifiant de l'utilisateur
    
    $reqUserMusiques = $bdd-> prepare('SELECT GROUP_CONCAT(NomGenre)
    FROM MUSIQUES
    INNER JOIN CHOISIT ON MUSIQUES.IdGenre=CHOISIT.IdGenre
    INNER JOIN USERS ON CHOISIT.IdUser=USERS.IdUser
    WHERE USERS.IdUser=?
    GROUP BY USERS.IdUser');
    $reqUserMusiques->execute(array($getIdUser));
    $userMusiquesInfo = $reqUserMusiques->fetch();
    //On prépare une requête SQL afin de sélectionner les goût musicaux d'un utilisateur en fonction de son l'identifiant
    
    if(isset($_POST['mettreAJourProfil']))
    {//Si l'utilisateur envoie le formulaire de modification du profil
        if(isset($_POST['motDePasseActuel']) AND !empty($_POST['motDePasseActuel']))
        {//Si l'utilisateur a remplie le champ motDePasseActuel
            $motDePasseActuel = sha1($_POST['motDePasseActuel']);
            $reqmdp=$bdd->prepare("SELECT MotDePasseUser FROM USERS WHERE IdUser=?");
            $reqmdp-> execute(array($getIdUser));
            $motDePasseUser= $reqmdp->fetch();
            if($motDePasseActuel==$userInfo['MotDePasseUser'])
            {//Si le motDePasseActuel et le bon mot de passe
                if(isset($_POST['newMotDePasse']) AND !empty($_POST['newMotDePasse']) AND isset($_POST['newMotDePasseConfirm']) AND !empty($_POST['newMotDePasseConfirm']))
                {//Si l'utilisateur a remplie son nouveau mot de passe et la confirmation de celui ci
                    if((preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])#', $_POST['newMotDePasse'])) && (strlen($_POST['newMotDePasse'])>7))
                    {//Si ces 2 derniers sont identiques
                        if(strcmp($_POST['newMotDePasse'], $_POST['newMotDePasseConfirm']) == 0){   
                        $newMotDePasse = sha1($_POST['newMotDePasse']);
                        $newMotDePasseConfirm = sha1($_POST['newMotDePasseConfirm']);
                        $insertMotDePasse=$bdd->prepare("UPDATE USERS SET MotDePasseUser = ? WHERE IdUser = ?");
                        $insertMotDePasse->execute(array($newMotDePasse,$_SESSION['IdUser']));
                        //On effectue une requête SQL afin de modifier le mot de passe
                        }
                        else
                        {
                            $erreur="Votre mot de passe de confirmation et votre mot de passe sont différent.";
                        }
                    }
                    else
                    {
                        $erreur = "Votre nouveau mot de passe doit contenir au moins 8 caractères dont au moins 1 chiffre et 1 majuscule et 1 miniscule."; 
                    }  
                }
                else
                {
                    $erreur = "Veuillez saisir votre nouveau mot de passe et la confirmation de ce nouveau mot de passe.";
                }
            }
            else{
                $erreur ="Votre mot de passe actuel n'est pas correct.";
            }
        }
        
        if(isset($_POST['newMail']) AND !empty($_POST['newMail']))
        {//Si l'utilisateur a remplie le champ newMail
            $newEmail = htmlspecialchars($_POST['newMail']);
            if(filter_var($newEmail, FILTER_VALIDATE_EMAIL))
            {//La fontion filter_var() permet de tester le format de l'adresse mail
                $reqmail=$bdd->prepare("SELECT * FROM USERS WHERE MailUser=?");
                $reqmail-> execute(array($newEmail));
                $mailExist=$reqmail->rowCount();
                //On effectue une requête SQL afin de compter le nombre de ligne de la TABLE USERS qui a pour e-mail la variable $newEmail
                $reqmailUser=$bdd->prepare("SELECT MailUser FROM USERS WHERE IdUser=?");
                $reqmailUser-> execute(array($_SESSION['IdUser']));
                $adresseMailActuelle = $reqmailUser->fetch();
                //On effectue une requête SQL afin de le mail de l'utilisateur courant
                if($mailExist==0 || ($mailExist==1 && $newEmail==$adresseMailActuelle['MailUser']))
                {//Si cette nouvelle adresse mail n'existe pas ou qu'elle existe mais qu'elle est possédée par l'utilisateur courant
                    $insertMail=$bdd->prepare("UPDATE USERS SET MailUser = ? WHERE IdUser = ?");
                    $insertMail->execute(array($newEmail,$_SESSION['IdUser']));
                    //On effectue une requête SQL afin de modifier le mail
                }
                else
                {
                    $erreur = "Adresse mail déjà utilisée.";
                }
            }
            else{
                $erreur ="Format d'adresse mail non valide";
            }
        }

        if(isset($_POST['newTelephone']) AND !empty($_POST['newTelephone']))
        {//Si l'utilisateur a remplie le champ newTelephone
        $newTelephone = htmlspecialchars($_POST['newTelephone']);
            if (preg_match("/0[0-9]{9}/", $newTelephone))
                {//Si le numéro de téléphone vérifie le format prédéfinit
                    $insertTelephone=$bdd->prepare("UPDATE USERS SET TelephoneUser = ? WHERE IdUser = ?");
                    $insertTelephone->execute(array($newTelephone,$_SESSION['IdUser']));
                    //On effectue une requête SQL afin de modifier le numéro de telephone
                }
                else
                {
                    $erreur= "Votre numero de telephone n'a pas un format valide.";
                }
        }



        if(isset($_POST['newDescription']) AND !empty($_POST['newDescription']))
        {//Si l'utilisateur a remplie le champ newDescription
                $newDescription = htmlspecialchars($_POST['newDescription']);
                $insertDescription=$bdd->prepare("UPDATE USERS SET DescriptionUser = ? WHERE IdUser = ?");
                $insertDescription->execute(array($newDescription,$_SESSION['IdUser']));
                //On effectue une requête SQL afin de modifier la description
        }

        if(isset($_POST['newVehicule']) AND !empty($_POST['newVehicule']))
        {//Si le champ newVehicule est remplie
            if(isset($_POST['newImmatriculation']) AND !empty($_POST['newImmatriculation']))
            {//Si le champ newImmatriculation est remplie
                if(isset($_POST['newCouleur']) AND !empty($_POST['newCouleur']))
                {//Si le champ newCouleur est remplie
                    $newVehicule = htmlspecialchars($_POST['newVehicule']);
                    $newImmatriculation = htmlspecialchars($_POST['newImmatriculation']);
                    $newCouleur = htmlspecialchars($_POST['newCouleur']);
                    $insertNewVehicule = $bdd->prepare("INSERT INTO VEHICULES (ImmatriculationVehicule, ModeleVehicule, CouleurVehicule,IdUser) VALUES(?,?,?,?)");
                    $insertNewVehicule->execute(array($newImmatriculation, $newVehicule, $newCouleur, $getIdUser));
                    //On effectue une requête SQL afin d'insérer un nouveau véhicule'
                }
                else{
                    $erreur = "Veuillez spécifier la couleur de votre véhicule.";
                }
            }
            else{
                $erreur = "Veuillez spécifier l'immatriculation' de votre véhicule.";
            }
        }
        
        foreach($_POST as $paramName=>$paramValue)
        {//On créé un tableau contenant le nom de la variable $_POST associé à sa valeur
            if (mb_substr($paramName,0,13) == "IdVehiculeSup")
            {//Si un des name commence par "IdVehiculeSup"
                $idVehiculeSup=mb_substr($paramName,13);
                //La variable $idVehiculeSup contient alors la valeur du reste du name, soit l'identifiant du véhicule sélectionné
                $supprimerCovoit = $bdd->prepare('DELETE FROM COVOIT WHERE IdVehicule=?');
                $supprimerCovoit->execute(array($idVehiculeSup));
                //On effectue une requête SQL afin de supprimer les covoturages liés au véhicule choisit
                $supprimerVehicule = $bdd->prepare('DELETE FROM VEHICULES WHERE IdVehicule=?');
                $supprimerVehicule->execute(array($idVehiculeSup));
                //On effectue une requête SQL afin de supprimer le véhicule choisit
            }
        }
        
        $suppChoisit= $bdd->prepare("DELETE FROM CHOISIT WHERE IdUser=?");
        $suppChoisit->execute(array($getIdUser));
        //On prépare une requête SQL qui permet de supprimer une ligne de la table CHOISIT qui associe un gout musical et un utiisateur
        foreach ($_POST as $paramName=>$paramValue)
         {//On créé un tableau contenant le nom de la variable $_POST associé à sa valeur
             if (mb_substr($paramName,0, 7) == "idGenre")
             {//Si un des name commence par "idGenre"
                $idGenre = mb_substr($paramName,7);
                //La variable $idGenre contient alors la valeur du reste du name, soit l'identifiant du genre musical sélectionné
                $insertChoisit= $bdd->prepare("INSERT INTO CHOISIT VALUES(?,?)");
                $insertChoisit->execute(array($getIdUser, $idGenre));
                //On effectue une requête SQL afin d'insérer une nouvelle ligne dans la table CHOISIT
             }
         }
        
        if(!isset($erreur))
        {//Si il n'y a pas d'erreur
            $bdd = null;
            header('Location: profil.php?IdUser='.$_SESSION['IdUser']);
            //On redirige l'utilisateur vers son profil
        }
    }
          
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Editer mon profil - fest'</title>
    <meta name="description" content="Modifiez vos données personnelles, et ajoutez ou supprimez vos séhicules à votre guise !" />
    <meta name="author" content="L'équipe Fest" />
    <link rel="stylesheet" href="css/style.css" type="text/css" />
    <link rel="stylesheet" href="css/styleEditerProfil.css" type="text/css" />
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
        <img class="imgHeader" src="images/cloche.png" alt="Icône cloche de notifications">
        <a href="connexion.php" class="iconesDroite"><img class="imgHeader2" src="images/utilisateur.png" alt="Icône profil utilisateur"></a>
        <?php
            }
            ?>
    </header>

    <main class="editionProfil">
        <div class="boxEditer">
            <h1>Editer mon profil</h1>

            <div>
                <?php
            if(isset($erreur))
            {
                echo "<div class='erreur'>$erreur</div>";
            }
            ?>
            </div>
            <form method="POST">

                <div class="boxDonneesPersonnelles">
                    <h2>Modifier mes données personnelles :</h2>
                    <div class="boxMail">
                        <label for="newMail">Ma nouvelle adresse mail :</label>
                        <input type="text" name="newMail" id="newMail" value="<?php echo $userInfo['MailUser']; ?>" />
                    </div>
                    <div class="boxMotDePasse">
                        <div class="elementsMotDePasse">
                            <label for="motDePasseActuel">Mon mot de passe actuel :</label>
                            <input type="password" name="motDePasseActuel" id="motDePasseActuel" />
                        </div>
                        <div class="elementsMotDePasse">
                            <label for="newMotDePasse">Mon nouveau mot de passe :</label>
                            <input type="password" name="newMotDePasse" id="newMotDePasse" />
                        </div>
                        <div class="elementsMotDePasse">
                            <label for="newMotDePasseConfirm">Confirmation de mon nouveau mot de passe :</label>
                            <input type="password" name="newMotDePasseConfirm" id="newMotDePasseConfirm" />
                        </div>
                    </div>
                    <div class="boxTelephone">
                        <label for="newTelephone">Mon nouveau numéro de téléphone :</label>
                        <input type="text" name="newTelephone" id="newTelephone" value="<?php echo $userInfo['TelephoneUser']; ?>" />
                    </div>
                    <div class="boxDescription">
                        <label for="newDescription">Ma nouvelle description :</label>
                        <input type="text" name="newDescription" id="newDescription" value="<?php echo $userInfo['DescriptionUser']; ?>" />
                    </div>
                </div>
                <h2>Véhicules:</h2>
                <fieldset class="boxVehicules">

                    <legend>Ajouter un véhicule :</legend>

                    <div>
                        <label for="newVehicule">Modèle :</label>
                        <input type="text" name="newVehicule" id="newVehicule" />
                    </div>
                    <div>
                        <label for="newImmatriculation">Immatriculation :</label>
                        <input type="text" name="newImmatriculation<?php $newImmatriculation ?>" id="newImmatriculation" />
                    </div>
                    <div>
                        <label for="newCouleur">Couleur :</label>
                        <input type="text" name="newCouleur" id="newCouleur" />
                    </div>
                </fieldset>
                <fieldset class="supprimerUnVehicule">
                    <legend class="legendeVehicule">Supprimer un véhicule (sélectionnez les véhicules à supprimer):</legend>
                    <?php
                    while($userVehiculeInfo != false) 
                    {//Tant qu'il existe un véhicule
                        $modeleVehicule=$userVehiculeInfo['ModeleVehicule'];
                        $immatriculationVehicule=$userVehiculeInfo['ImmatriculationVehicule'];
                        $couleurVehicule=$userVehiculeInfo['CouleurVehicule'];
                        $idVehicule=$userVehiculeInfo['IdVehicule'];
                        echo "<div class='boxSupprimerVehicule'>
                        <input type='checkbox' name='IdVehiculeSup".$idVehicule."' id='IdVehiculeSup".$idVehicule."' value='".$modeleVehicule.$immatriculationVehicule.$couleurVehicule."'>
                        <label for='IdVehiculeSup".$idVehicule."'>"."Modèle : ".$modeleVehicule."<br>Immatriculation : ".$immatriculationVehicule."<br>Couleur : ".$couleurVehicule."</label>
                        </div>\r\n";
                        $userVehiculeInfo = $reqUserVehicule->fetch();
                        //Ligne suivante
                    }
                ?>
                </fieldset>
                <fieldset>
                    <legend>Modifier vos goûts musicaux :</legend>
                    <?php
                    $sql = "select * from MUSIQUES";
                    $stmt = $bdd->prepare($sql);
                    $stmt->execute() ;
                    //On effectue une requête SQL afin de sélectionner tous les genres de musique présent dans la base de données
                    $ligne = $stmt->fetch();
                    //On sélectionne la première ligne
                    while($ligne != false) 
                    {//Tant qu'il existe toujours un genre de musique
                        $nomGenre=$ligne['NomGenre'];
                        $idGenre=$ligne['IdGenre'];
                        $reqChoisit = $bdd->prepare('SELECT * FROM CHOISIT WHERE IdUser=? AND IdGenre=?');
                        $reqChoisit->execute(array($getIdUser,$idGenre));
                        //On effectue une requête SQL afin de savoir si la ligne de la table CHOISIT qui associe l'id du genre courant et l'id de l'utilisateur courant existe
                        $choisitInfo = $reqChoisit->fetch();
                        //Première ligne
                        if($choisitInfo != false)
                        {//Si cette ligne existe
                            echo "<div class='genreMusique'><input type='checkbox' name='idGenre".$idGenre."' id='idGenre".$idGenre."' value='".$nomGenre."' checked='checked'><label for='idGenre".$idGenre."'>".$nomGenre."</label></div>\r\n";
                            //On affiche le nom de genre courant qui a pour name 'idGenre' suivie de l'id du genre courant en étant checker
                        }
                        else
                        {
                            echo "<div class='genreMusique'><input type='checkbox' name='idGenre".$idGenre."' id='idGenre".$idGenre."' value='".$nomGenre."'><label for='idGenre".$idGenre."'>".$nomGenre."</label></div>\r\n";
                            //On affiche le nom de genre courant qui a pour name 'idGenre' suivie de l'id du genre courant
                        }
                        $ligne = $stmt->fetch();
                        //Ligne suivante
                    }    
                ?>
                </fieldset>
                <input type="submit" name="mettreAJourProfil" class="edition" value="Mettre à jour mon profil" />

            </form>

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
}
else
{
    $bdd = null ;
    header("Location: connexion.php");
}
?>
