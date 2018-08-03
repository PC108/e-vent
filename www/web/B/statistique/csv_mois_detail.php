<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/TraitementCSV.php');
include('_requete_mois.php');
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
if	(isset($_GET	['date_sql']))	{
			$date_sql	=	$_GET['date_sql'];
}	else	{
			adn_myRedirection('ca_mois_liste.php');
			exit;
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

//Execution de la requete mainQueryCommandes
$query_c	=	mainQueryCommandes($connexion,	$date_sql);
$RS_c	=	mysql_query($query_c,	$connexion)	or	die(mysql_error());
$nbre_rows_c	=	mysql_num_rows($RS_c);

//Execution de la requete mainQuerySommeCommandes
$query_sum_c	=	mainQuerySommeCommandes($connexion,	$date_sql);
$RS_sum_c	=	mysql_query($query_sum_c,	$connexion)	or	die(mysql_error());
$rows_sum_c	=	mysql_fetch_object($RS_sum_c);

/*	* ******************************************************** */
/*              Formatage du CSV                    */
/*	* ******************************************************** */
header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="statistique_mois_'	.	$date_sql	.	'.csv"');

$chaine	=	"Référence commande;Nom;Prénom;Mode de paiement;Nombre achat(s);Cotisation;Don;Evénement;Hebergement;Restauration;Somme Achats;Remise;Total Commande;Remboursement;Chiffre d'affaire;Encaissement;";
$chaine	.=	adn_getStrRetour();
echo	(utf8_decode($chaine));

if	($nbre_rows_c	>	0)	{
			while	($row_c	=	mysql_fetch_object($RS_c))	{
						$ref	=	adn_checkData($row_c->CMD_ref);
						$nom	=	adn_checkData($row_c->ADH_nom);
						$prenom	=	adn_checkData($row_c->ADH_prenom);
						$mdp	=	adn_checkData($row_c->MDPAY_nom_fr);
						$nb_achats	=	adn_checkData($row_c->NbreAchats);

						if	($row_c->Cotisation	!=	null)
									$cotisation	=	adn_checkData($row_c->Cotisation);	else
									$cotisation	=	"Pas de cotisation";

						if	($row_c->Don	!=	null)
									$don	=	adn_checkData($row_c->Don);	else
									$don	=	" Pas de don";

						if	($row_c->Evenement	!=	null)
									$event	=	adn_checkData($row_c->Evenement);	else
									$event	=	" Pas d'événement";

						if	($row_c->Hebergement	!=	null)
									$heber	=	adn_checkData($row_c->Hebergement);	else
									$heber	=	" Pas d'hébergement";

						if	($row_c->Restauration	!=	null)
									$resto	=	adn_checkData($row_c->Restauration);	else
									$resto	=	" Pas de restauration";

						$T_achats	=	adn_checkData($row_c->TotalAchats);
						$remise	=	adn_checkData($row_c->CMD_remise);
						$T_cmd	=	adn_checkData($row_c->totalCommande);
						$T_remb	=	adn_checkData($row_c->TotalRemb);
						$ca	=	adn_checkData($row_c->chiffreAffaire);
						$encaissement	=	adn_checkData($row_c->CMD_encaissement);

						$chaine	=	"$ref;$nom;$prenom;$mdp;$nb_achats;$cotisation;$don;$event;$heber;$resto;$T_achats;$remise;$T_cmd;$T_remb;$ca;$encaissement;";
						$chaine	.=	adn_getStrRetour();
						echo	(utf8_decode($chaine));
			}

			if	($rows_sum_c->TotalCotisation	!=	null)
						$T_cot	=	adn_checkData($rows_sum_c->TotalCotisation);	else
						$T_cot	=	" Pas de cotisation";

			if	($rows_sum_c->TotalDon	!=	null)
						$T_don	=	adn_checkData($rows_sum_c->TotalDon);	else
						$T_don	=	" Pas de don";

			if	($rows_sum_c->TotalEvenement	!=	null)
						$T_event	=	adn_checkData($rows_sum_c->TotalEvenement);	else
						$T_event	=	" Pas d'événement";

			if	($rows_sum_c->TotalHebergement	!=	null)
						$T_heber	=	adn_checkData($rows_sum_c->TotalHebergement);	else
						$T_heber	=	" Pas d'hébergement";

			if	($rows_sum_c->TotalRestauration	!=	null)
						$T_resto	=	adn_checkData($rows_sum_c->TotalRestauration);	else
						$T_resto	=	" Pas de restauration";

			$T_S_achats	=	adn_checkData($rows_sum_c->TotalSommeAchats);
			$T_S_remise	=	adn_checkData($rows_sum_c->TotalSommeRemise);
			$T_S_percu	=	adn_checkData($rows_sum_c->TotalSommePercu);
			$T_S_remb	=	adn_checkData($rows_sum_c->TotalRemboursement);
			$T_ca	=	adn_checkData($rows_sum_c->TotalChiffreAffaire);
			$T_encaissement	=	adn_checkData($rows_sum_c->TotalEncaissement);
			
			$chaine	=	"Total du mois;;;;;$T_cot;$T_don;$T_event;$T_heber;$T_resto;$T_S_achats;$T_S_remise;$T_S_percu;$T_S_remb;$T_ca;$T_encaissement;";
						$chaine	.=	adn_getStrRetour();
						echo	(utf8_decode($chaine));
}
exit;
?>

