<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
ini_set('html_errors',	0);	// Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
error_reporting(E_ALL);
require_once('../../librairie/php/code_adn/TraitementCSV.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// Requetes
include("_requete.php");
$query	=	queryNewsletter();
if	($_GET['lg']	==	"fr")	{
			$query	.=	"HAVING NEWS_langue = 'FR'";
			$langue	=	'fr';
}	else	{
			$query	.=	"HAVING NEWS_langue = 'EN'";
			$langue	=	'en';
}
$query	.=	"ORDER BY NEWS_email ASC";
$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());

header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="Export_'	.	$langue	.	'.csv"');

//$chaine	=	"email;"	.	adn_getStrRetour();
//echo	(utf8_decode($chaine));

while	($row	=	mysql_fetch_object($result))	{
			$email	=	adn_checkData($row->NEWS_email);

			$chaine	=	"$email;"	.	adn_getStrRetour();
			echo	(utf8_decode($chaine));
}
exit;
?>