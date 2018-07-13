<?php

//simulation du  temps d'attente du serveur (2 secondes)
//sleep(1);
//DEBUGGAGE
// header("Content-Type: text/html; charset=iso-8859-1");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");
ini_set('html_errors', 0);

//Connexion a la DB
if (isset($_POST['cle']) && isset($_POST['valeur'])) {
    $cle = $_POST['cle'];
    $valeur = $_POST['valeur'];
} else {
    return (FALSE);
}

setcookie($cle, $valeur, time() + 60 * 60 * 24 * 30, '/', null, false, false);
?>