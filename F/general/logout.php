<?php

require_once('../_session.php');
require_once('../../librairie/php/code_adn/MyRedirection.php');

$langue	=	$_SESSION['lang'];

// Vérifie si on est en mode SAISIE DIRECTE et préserve ce mode si nécessaire.
$saisie = false;
if	(isset($_GET['mode']) &&	$_GET['mode']	=	"keep" && isset($_SESSION['saisie'])	)	{	$saisie	=	$_SESSION['saisie'];	}

// Si on active cette ligne, les données de la session seront sauvegardées dans uncookie et récupérées lors d'une prochaine ouverture.
// session_write_close();	
// Détruit toutes les variables de session
$_SESSION	=	array();
// Finalement, on détruit la session.
session_destroy();

// Recharge le mode SAISIE DIRECTE si nécessaire
if	($saisie)	{
			session_start();
			$_SESSION['saisie']	=	$saisie;
}

// redirection
adn_myRedirection('../evenement/index.php?lang='	.	$langue);
?>