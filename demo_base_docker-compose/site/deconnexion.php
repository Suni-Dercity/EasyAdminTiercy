<?php
session_start();
$_SESSION = array();
session_destroy();
//On détruit la session en cours et les variables de session associés
header('Location: connexion.php');
//On redirige l'utilisateur vers la page de connexion
?>