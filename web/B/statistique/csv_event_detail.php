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
/*              Récupération des variables GET                   */
/*	* ******************************************************** */
if	(isset($_GET	['even_id']))	{
			$even_id	=	$_GET['even_id'];
}	else	{
			echo	"Aucun even en GET";
			exit;
}
/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

//Execution de la requête mainQueryJourEvent
$query_j	=	mainQueryJourEvent($connexion,	$even_id);
$RS_j	=	mysql_query($query_j,	$connexion)	or	die(mysql_error());
$nbre_rows_j	=	mysql_num_rows($RS_j);

//Execution de la requête QuerySommeJourEvent
$query_sum_e	=	QuerySommeJourEvent($connexion,	$even_id);
$RS_sum_e	=	mysql_query($query_sum_e,	$connexion)	or	die(mysql_error());
$rows_sum_e	=	mysql_fetch_object($RS_sum_e);


/*	* ******************************************************** */
/*              Formatage du CSV                    */
/*	* ******************************************************** */
header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="statistique_event_detail_'	.	date("Ymd_Hi")	.	'.csv"');


$chaine	=	"Jour événement;Total jours événement;Total hébergements;Total restaurations;Chiffre d'affaire;";
$chaine	.=	adn_getStrRetour();
echo	(utf8_decode($chaine));

if	($nbre_rows_j	>	0)	{
			while	($row_j	=	mysql_fetch_object($RS_j))	{
						$j_event	=	adn_checkData($row_j->date_jevent	.	" "	.	$row_j->LEVEN_nom);

						if	($row_j->TotalAchatsEvent	!=	null)
									$T_j_event	=	adn_checkData($row_j->TotalAchatsEvent	.	" pour "	.	$row_j->nbreAchatsEvent	.	" achat(s)");	else
									$T_j_event	=	adn_checkData($row_j->nbreAchatsEvent	.	" achat");

						if	($row_j->TotalAchatsHeber	!=	null)
									$T_heber	=	adn_checkData($row_j->TotalAchatsHeber	.	" pour "	.	$row_j->nbreAchatsHeber	.	" hébergement(s)");	else
									$T_heber	=	adn_checkData($row_j->nbreAchatsHeber	.	" hébergement");

						if	($row_j->TotalAchatsResto	!=	null)
									$T_resto	=	adn_checkData($row_j->TotalAchatsResto	.	" pour "	.	$row_j->nbreAchatsResto	.	" restauration(s)");	else
									$T_resto	=	adn_checkData($row_j->nbreAchatsResto	.	" restauration");

						$ca_j_event	=	adn_checkData($row_j->CAJourEvent);

						$chaine	=	"$j_event;$T_j_event;$T_heber;$T_resto;$ca_j_event;";
						$chaine	.=	adn_getStrRetour();
						echo	(utf8_decode($chaine));
			}

			if	($rows_sum_e->Somme_Event	!=	null)
						$S_event	=	adn_checkData($rows_sum_e->Somme_Event	.	" pour "	.	$rows_sum_e->Nbre_All_Event	.	" achat(s)");	else
						$S_event	=	adn_checkData("pour "	.	$rows_sum_e->Nbre_All_Event	.	" achat(s)");

			if	($rows_sum_e->Somme_Heber	!=	null)
						$S_heber	=	adn_checkData($rows_sum_e->Somme_Heber	.	" pour "	.	$rows_sum_e->Nbre_All_Heber	.	" hébergement(s)");	else
						$S_heber	=	adn_checkData("pour "	.	$rows_sum_e->Nbre_All_Heber	.	" hébergement(s)");

			if	($rows_sum_e->Somme_Resto	!=	null)
						$S_resto	=	adn_checkData($rows_sum_e->Somme_Resto	.	" pour "	.	$rows_sum_e->Nbre_All_Resto	.	" restauration(s)");	else
						$S_resto	=	adn_checkData("pour "	.	$rows_sum_e->Nbre_All_Resto	.	" restauration(s)");

			$S_ca_j_event	=	adn_checkData($rows_sum_e->CAEvent);

			$chaine	=	"Total de l'événement;$S_event;$S_heber;$S_resto;$S_ca_j_event;";
			$chaine	.=	adn_getStrRetour();
			echo	(utf8_decode($chaine));
}
exit;
?>


