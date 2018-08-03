<?php
require_once('../_session.php');
require_once('../../librairie/php/code_adn/MyRedirection.php');

// Si on active cette ligne, les données de la session seront sauvegardées dans uncookie et récupérées lors d'une prochaine ouverture.
// session_write_close();	
// Détruit toutes les variables de session
$_SESSION	=	array();
// Finalement, on détruit la session.
session_destroy();

adn_myRedirection('login.php');
?>