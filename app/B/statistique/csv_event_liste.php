<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/TraitementCSV.php');
include('_requete_event.php');
ini_set('html_errors',	0);	// Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
error_reporting(E_ALL);

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'stat')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

//Execution de la requete QueryEvent pour récupérer tous les événements de la BDD
$query_e	=	QueryEvent($connexion);
$RS_e	=	mysql_query($query_e,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*              Formatage du CSV                    */
/*	* ******************************************************** */
header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="statistique_event_'	.	date("Ymd_Hi")	.	'.csv"');


$chaine	=	"Nom événement;Nombre jours événements;Somme jours événements;Somme hébergements;Somme restaurations;Chiffre d'affaire;";
$chaine	.=	adn_getStrRetour();
echo	(utf8_decode($chaine));

while	($row	=	mysql_fetch_object($RS_e))	{
			$even_id	=	$row->EVEN_id;
			//Execution de la requête QuerySommeJourEvent pour le calcul des sommes pour chaque JourEvent 
			$query_sum_e	=	QuerySommeJourEvent($connexion,	$even_id);
			$RS_sum_e	=	mysql_query($query_sum_e,	$connexion)	or	die(mysql_error());
			$rows_sum_e	=	mysql_fetch_object($RS_sum_e);

			$nom_event	=	adn_checkData($row->EVEN_nom_fr);
			$nb_j_event	=	adn_checkData($row->NbreJour);

			if	($rows_sum_e->Somme_Event	!=	null)
						$sum_j_event	=	adn_checkData($rows_sum_e->Somme_Event);	else
						$sum_j_event	=	adn_checkData($rows_sum_e->Nbre_All_Event	.	" achat");

			if	($rows_sum_e->Somme_Heber	!=	null)
						$sum_heber	=	adn_checkData($rows_sum_e->Somme_Heber);	else
						$sum_heber	=	adn_checkData($rows_sum_e->Nbre_All_Heber	.	" hébergement");

			if	($rows_sum_e->Somme_Resto	!=	null)
						$sum_resto	=	adn_checkData($rows_sum_e->Somme_Resto);	else
						$sum_resto	=	adn_checkData($rows_sum_e->Nbre_All_Resto	.	" restauration");

			$ca	=	adn_checkData($rows_sum_e->CAEvent);

			$chaine	=	"$nom_event;$nb_j_event;$sum_j_event;$sum_heber;$sum_resto;$ca;";
			$chaine	.=	adn_getStrRetour();
			echo	(utf8_decode($chaine));
}
exit;
?>


