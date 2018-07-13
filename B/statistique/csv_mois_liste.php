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
/*              Execution des requêtes                     */
/*	* ******************************************************** */
//Pour obtenir l'affichage des mois en français
setlocale(LC_TIME,	'fra');

//Execution de la requete mainQueryDateSql pour récupérer tous les mois de la BDD
$query_d	=	mainQueryDateSql($connexion);
$RS_d	=	mysql_query($query_d,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*              Formatage du CSV                    */
/*	* ******************************************************** */
header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="statistiques_mensuelles.csv"');

$chaine	=	"Mois;Nombre cmd(s);Cotisation;Don;Evenement;Hebergement;Restauration;Somme Achats;Somme Remise;Somme Commande;Remb.;Chiffre d'affaire;Encaissement;";
$chaine	.=	adn_getStrRetour();
echo	(utf8_decode($chaine));

while	($row	=	mysql_fetch_object($RS_d))	{
			$date_sql	=	$row->date_sql;
			$date_mois	=	strftime("%B %Y",	strtotime($date_sql,	time()));

			//Execution de la requete mainQuerySommeCommandes pour le calcul des sommes pour chaque mois 
			$query_sum_c	=	mainQuerySommeCommandes($connexion,	$date_sql);
			$RS_sum_c	=	mysql_query($query_sum_c,	$connexion)	or	die(mysql_error());
			$rows_sum_c	=	mysql_fetch_object($RS_sum_c);

			$mois	=	adn_checkData(utf8_encode($date_mois));
			$nb_cmd	=	adn_checkData($rows_sum_c->Compteur);

			if	($rows_sum_c->TotalCotisation	!=	null)
						$cotisation	=	adn_checkData($rows_sum_c->TotalCotisation);	else
						$cotisation	=	"Pas de cotisation";

			if	($rows_sum_c->TotalDon	!=	null)
						$don	=	adn_checkData($rows_sum_c->TotalDon);	else
						$don	=	"Pas de don";

			if	($rows_sum_c->TotalEvenement	!=	null)
						$evenement	=	adn_checkData($rows_sum_c->TotalEvenement);	else
						$evenement	=	" Pas d'événement";

			if	($rows_sum_c->TotalHebergement	!=	null)
						$hebergement	=	adn_checkData($rows_sum_c->TotalHebergement);	else
						$hebergement	=	" Pas d'hébergement";

			if	($rows_sum_c->TotalRestauration	!=	null)
						$restauration	=	adn_checkData($rows_sum_c->TotalRestauration);	else
						$restauration	=	" Pas de restauration";

			$somme_achat	=	adn_checkData($rows_sum_c->TotalSommeAchats);
			$somme_remise	=	adn_checkData($rows_sum_c->TotalSommeRemise);
			$somme_commande	=	adn_checkData($rows_sum_c->TotalSommePercu);
			$remb	=	adn_checkData($rows_sum_c->TotalRemboursement);
			$ca	=	adn_checkData($rows_sum_c->TotalChiffreAffaire);
			$encaissement	=	adn_checkData($rows_sum_c->TotalEncaissement);

			$chaine	=	"$mois;$nb_cmd;$cotisation;$don;$evenement;$hebergement;$restauration;$somme_achat;$somme_remise;$somme_commande;$remb;$ca;$encaissement;";
			$chaine	.=	adn_getStrRetour();
			echo	(utf8_decode($chaine));
}
exit;
?>



