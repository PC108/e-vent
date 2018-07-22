<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
ini_set('html_errors',	0);	// Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
error_reporting(E_ALL);
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('../../librairie/php/code_adn/TraitementCSV.php');
require_once('_requete.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$param_jrevent	=	" ="	.	$_GET['jreven_id'];
if	($_GET['tyheb_id']	==	"nul")	{	$param_tyheb	=	" IS NULL";	}	else	{	$param_tyheb	=	" ="	.	$_GET['tyheb_id'];	}
if	($_GET['tyres_id']	==	"nul")	{	$param_tyres	=	" IS NULL";	}	else	{	$param_tyres	=	" ="	.	$_GET['tyres_id'];	}

$query	=	queryInscrits($param_jrevent,	$param_tyheb, $param_tyres,	$connexion);
$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*              Formatage du CSV                    */
/*	* ******************************************************** */
header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="inscrits_'	.	date("Ymd_Hi")	.	'.csv"');

$chaine	=	"Inscription;Informations Personnelles;;;;;;Communication;;;;Adresse;;;;;Sangha;;Benevolat;;;;"	.	adn_getStrRetour();
echo	(utf8_decode($chaine));

$chaine	=	"Cotisation;Nom;Prénom;Identifiant;Genre;Langue;Type tarif;Email;Newsletter;Télephone;Portable;Adresse 1;Adresse 2;Code postal;Ville;Pays;Ordination;Nom de Dharma;Bénevolat;Profession;Disponibilités;Compétences;"	.	adn_getStrRetour();
echo	(utf8_decode($chaine));

while	($row	=	mysql_fetch_object($result))	{
// Ajout des données
		if	($row->ADH_ordination)	{
				$ordination	=	"x";
		}	else	{
				$ordination	=	"";
		}

		if	($row->ADH_benevolat)	{
				$benevolat	=	"x";
		}	else	{
				$benevolat	=	"";
		}

		if	(!is_null($row->NEWS_email))	{
				$news	=	"x";
		}	else	{
				$news	=	"";
		}

		$queryCmpt	=	"SELECT CMPT_nom_fr FROM t_competence_cmpt LEFT JOIN tj_adh_cmpt ON TJ_CMPT_id=CMPT_id
        WHERE TJ_ADH_id=$row->ADH_id";
		$res	=	mysql_query($queryCmpt,	$connexion)	or	die(mysql_error());
		$strCmpt	=	"";
		while	($cmpt	=	mysql_fetch_object($res))	{
				$strCmpt	.=	str_replace(";",	"",	$cmpt->CMPT_nom_fr	.	" / ");
		}
		rtrim($strCmpt,	" / ");
		
		$cot	=	adn_checkData($row->ADH_annee_cotisation);
		$nom	=	adn_checkData($row->ADH_nom);
		$prenom	=	adn_checkData($row->ADH_prenom);
		$id	=	adn_checkData($row->ADH_identifiant);
		$genre	=	adn_checkData($row->ADH_genre);
		$langue	=	adn_checkData($row->NEWS_langue);
		$tytar	=	adn_checkData($row->TYTAR_nom_fr	.	" ("	.	$row->TYTAR_ratio	.	"%)");
		$mail	=	adn_checkData($row->NEWS_email);
		$tel	=	adn_checkData($row->ADH_telephone);
		if	($row->ADH_portable	!=	null)
				$port	=	adn_checkData($row->ADH_portable);	else
				$port	=	"";
		$adr1	=	adn_checkData($row->ADH_adresse1);
		$adr2	=	adn_checkData($row->ADH_adresse2);
		$zip	=	adn_checkData($row->ADH_zip);
		$ville	=	adn_checkData($row->ADH_ville);
		$pays	=	adn_checkData($row->PAYS_nom_fr);
		$dharma	=	adn_checkData($row->ADH_nom_dharma);
		$profession	=	adn_checkData($row->ADH_profession);
		$dispo	=	adn_checkData($row->ADH_disponibilite);

		$chaine	=	"$cot;$nom;$prenom;$id;$genre;$langue;$tytar;$mail;$news;$tel;$port;$adr1;$adr2;$zip;$ville;$pays;$ordination;$dharma;$benevolat;$profession;$dispo;$strCmpt;"	.	adn_getStrRetour();
		echo	(utf8_decode($chaine));
}
exit;
?>