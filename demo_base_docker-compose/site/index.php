<?php
header("Content-type: text/html; charset=UTF-8");
session_start();

require("param/param.inc.php");
$bdd = new PDO("mysql:host=".MYHOST.";dbname=".MYDB,MYUSER,MYPASS);
$bdd->query("SET NAMES utf8");
$bdd->query("SET CHARACTER SET 'utf8'");

//test test test
if(!isset($_SESSION['IdUser']))
{//Si une aucune session est en cours
    if(isset($_POST['formConnect']))
    {//Si l'utilisateur envoie son formulaire de connexion
        $emailConnect = htmlspecialchars($_POST['emailConnect']);
        $motDePasseConnect = sha1($_POST['motDePasseConnect']);
        //La fonction htmlspecialchars() permet de se protéger des injections SQL
        if(!empty($emailConnect) AND !empty($motDePasseConnect))
        {//Si aucun champ obligatoire n'est vide
            if(filter_var($emailConnect, FILTER_VALIDATE_EMAIL))
            {//La fontion filter_var() permet de tester le format de l'adresse mail
                $reqUser = $bdd->prepare("SELECT * FROM USERS WHERE MailUser = ? AND MotDePasseUser = ?");
                $reqUser->execute(array($emailConnect,$motDePasseConnect));
                $userExist = $reqUser->rowCount();
                //On effectue une requête SQL afin de compter le nombre de ligne de la TABLE USERS qui a pour e-mail la variable $emailConnect et pour mot de passe $motDePasseConnect
                if($userExist==1)
                {//Si il y a bien une ligne qui vérifie ces 2 conditions
                    $userInfo = $reqUser->fetch();
                    $_SESSION['IdUser']=$userInfo['IdUser'];
                    //On va chercher l'idUser correspondant
                    if($emailConnect=="adminFest@gmail.com")
                    {//Si l'adresse mail est la suivante : adminFest@gmail.com
                        $_SESSION['MailUser']=$userInfo['MailUser'];
                        //On crée une variable de session MailUser
                        //On redirige l'utilisateur vers la page de l'administrateur, en effet, une adresse mail ne peut pas être utiliser pour 2 comptes donc nous avons décider d'attribuer à l'administrateur une certaine adresse mail et un certain mit de passe
                        $bdd = null ;
                        header("Location: admin.php");
                    }
                    else
                    {//Sinon on redirige l'utilisateur vers son profil
                        header("Location: profil.php?IdUser=".$_SESSION['IdUser']);
                    }
                }
                else
                {
                    $erreur = "Adresse mail ou mot de passe incorrect";
                }
            }
            else
            {
                $erreur ="Format d'adresse mail non valide";
            }
        }
        else
        {
            $erreur = "Veuillez renseigner tous les champs";       
        }          
    }
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <title>Se connecter - fest'</title>
    <meta name="description" content="Connectez-vous pour pouvoir accéder au fonctionnalités principales du site et profiter de vos avantages de membre fest'. Si ce n’est pas déjà fait, créez-vous un compte. Fest’ n’attend que vous !" />
    <meta name="author" content="L'équipe Fest" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/styleConnexion.css">

    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body class="bodyConnexion">
    <main class="creerUnCompte">
        <div class="loginbox">
            <h1 class="titreConnexion">Connexion</h1>

            <div>
                <?php
            if(isset($erreur))
            {
                echo "<div class='erreur'>$erreur</div>";
            }
            ?>
            </div>
            <form class="formConnexion" method="POST">
                <label class="textesConnexion" for="emailConnect">Adresse mail :</label>
                <input class="containerMail mail" type="text" id="emailConnect" name="emailConnect" value="<?php if(isset($emailConnect)){echo $emailConnect;}?>" />
                <label class="textesConnexion" for="motDePasseConnect">Mot de passe :</label>
                <input class="password containerPass" type="password" name="motDePasseConnect" id="motDePasseConnect" />
                <div>
                    <input type="submit" name="formConnect" class="connexion" value="Se connecter" />
                </div>
            </form>
        </div>
    </main>
</body>

</html>
<?php
}
else
{
    if(!isset($_SESSION['MailUser']))
    {//Si une aucune session administrateur est en cours
        $bdd = null ;
        header("Location: profil.php?IdUser=".$_SESSION['IdUser']);
        //On redirige l'utilisateur vers son profil
    }
    else
    {
        $bdd = null ;
        header("Location: admin.php");
        //On redirige l'admisitrateur vers sa page d'admin
    }
}
?>
