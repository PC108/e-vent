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

$sessionFiltre	=	'FiltreADH';

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
adn_checkEffaceFiltres($sessionFiltre);
$query	=	mainQueryAdherent();
$query	=	adn_creerExists($query,	$sessionFiltre,	'TJ_CMPT_id',	'EXISTS',	'tj_adh_cmpt',	'TJ_ADH_id = ADH_id');
$query	=	adn_creerFiltre($query,	$sessionFiltre,	array('check_withcmt',	'select_tri',	'submit',	'chemin_retour',	'fonction',	'TJ_CMPT_id'),	array('FK_CMTADH_id',	'NEWS_email'));
$query	=	adn_groupBy($query,	'ADH_id');
$query	=	adn_orderBy($query,	$sessionFiltre,	'ADH_id DESC');

$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*              Formatage du CSV                    */
/*	* ******************************************************** */
header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="adherent_'	.	date("Ymd_Hi")	.	'.csv"');

$chaine	=	"Inscription;;Informations Personnelles;;;;;;;;Communication;;;;Adresse;;;;;";
if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	$chaine	.=	"Sangha;;";	}
if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	$chaine	.=	"Benevolat;;;;";	}
$chaine	.=	adn_getStrRetour();
echo	(utf8_decode($chaine));

$chaine	=	"Etat;Cotisation;Nom;Prénom;Identifiant;Mot de passe;Genre;Année;Langue;Type tarif;Email;Newsletter;Télephone;Portable;Adresse 1;Adresse 2;Code postal;Ville;Pays;";
if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	$chaine	.=	"Ordination;Nom de Dharma;";	}
if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	$chaine	.=	"Bénevolat;Profession;Disponibilités;Compétences;";	}
$chaine	.=	adn_getStrRetour();
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

			$etat	=	adn_checkData($row->EADH_nom);
			$cot	=	adn_checkData($row->ADH_annee_cotisation);
			$nom	=	adn_checkData($row->ADH_nom);
			$prenom	=	adn_checkData($row->ADH_prenom);
			$id	=	adn_checkData($row->ADH_identifiant);
			$mdp	=	adn_checkData($row->ADH_password);
			$genre	=	adn_checkData($row->ADH_genre);
			$naissance	=	adn_checkData($row->ADH_annee_naissance);
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

			$chaine	=	"$etat;$cot;$nom;$prenom;$id;$mdp;$genre;$naissance;$langue;$tytar;$mail;$news;$tel;$port;$adr1;$adr2;$zip;$ville;$pays;";
			if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	$chaine	.=	"$ordination;$dharma;";	}
			if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	$chaine	.=	"$benevolat;$profession;$dispo;$strCmpt;";	}
			$chaine	.=	adn_getStrRetour();
			echo	(utf8_decode($chaine));

			// Cas pour les commentaires
			if	(isSet($_GET['com'])	&&	$_GET['com']	==	"1"	&&	$row->FK_CMTADH_id	!=	0)	{
						$chaine	=	"Commentaire : ";
						$query	=	"SELECT CMT_commentaire FROM t_commentaire_cmt WHERE CMT_id = $row->FK_CMTADH_id";
						$rs	=	mysql_query($query,	$connexion)	or	die(mysql_error());
						$res	=	mysql_fetch_object($rs);
						$chaine	.=	adn_checkData($res->CMT_commentaire);
						$chaine	.=	";"	.	adn_getStrRetour();
						echo	(utf8_decode($chaine));
			}
}
exit;
?>