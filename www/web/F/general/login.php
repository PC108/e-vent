<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/EnvoiMail.php');

/*	* ******************************************************** */
/*             Initialisation des variables               */
/*	* ******************************************************** */
$mailEnvoi	=	$_SESSION['info_client']['email_from'];

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$chemin	=	$_POST['chemin'];

if	(isset($_POST['key'])	&&	$_POST['key']	==	"UYyjiuUIYTuiHUYt67yYYfrEfghGhytuGrt5eeRDfcdedryFHgfijKlkjIUYygyteGcgfc")	{	// CAS DU BASCULEMENT EN MODE SAISIE
			$_SESSION['saisie']	=	$_POST['username'];
}	else	if	(isset($_POST['email_lostpwd']))	{	// CAS D'OUBLI DES IDENTIFIANTS
			$checkIdentification	=	"sendId_ok";
			// Récupération de toutes les données de tous les adhérents correspondant à l'email.
			$mail	=	$_POST['email_lostpwd'];
			$query	=	"SELECT * FROM t_adherent_adh WHERE ADH_email = '$mail'";
			$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			if	(mysql_num_rows($result))	{
						if	(strtoupper(substr(PHP_OS,	0,	3))	===	'WIN')	{
									$str_retour	=	"\r\n";
						}	else	{
									$str_retour	=	"\n";
						}
						$str_retour	=	"\n";
						// récupère l'url du site
						$url	=	"http://"	.	$_SERVER['HTTP_HOST'];

						// contenu de l'email
						$titre	=	_("Récupération de vos identifiants de e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];
						$text	=	_("Bonjour ")	.	$str_retour	.	$str_retour;
						$text	.=	_("Voici les identifiants associés à l'adresse")	.	" "	.	$mail	.	$str_retour	.	$str_retour;
						while	($row	=	mysql_fetch_object($result))	{
									$text	.=	_("Identifiant")	.	" : "	.	$row->ADH_identifiant	.	" / "	.	_("Mot de passe")	.	" : "	.	$row->ADH_password	.	$str_retour	.	$str_retour;
						}
						$text	.=	$str_retour;
						$text	.=	_("Merci d'utiliser ces identifiants pour vous connecter au site.")	.	$str_retour;
						$text	.=	$url	.	$str_retour	.	$str_retour;

						$text	.=	_('Cordialement.')	.	$str_retour;
						$text	.=	_("L'équipe de e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];

						if	(!adn_envoiMail($mail,	$titre,	$text,	$mailEnvoi))	{
									$checkIdentification	=	"sendIdByEmail_ko";
						}
			}	else	{
						$checkIdentification	=	"checkId_ko";
			}
}	else	{	// CAS DE L'IDENTIFICATION
			// Initialisation des variables
			$checkIdentification	=	"checkUser_ko";

			// récupération des variables POST
			// utilisation de adn_quote_smart pour la protection contre les injections sql
			$user	=	adn_quote_smart($_POST['user']);
			$pwd	=	adn_quote_smart($_POST['pwd']);

			// création du recordset
			$query_rs1	=	"
				SELECT *,
				(SELECT TYTAR_ratio FROM t_typetarif_tytar WHERE TYTAR_id = FK_TYTAR_id) AS TYTAR_ratio
				FROM t_adherent_adh WHERE ADH_identifiant = '$user'";
			$rs1	=	mysql_query($query_rs1,	$connexion)	or	die(mysql_error());
			$nbreRows_rs1	=	mysql_num_rows($rs1);

			if	($nbreRows_rs1	!=	0)	{
						$row	=	mysql_fetch_object($rs1);
						//Si $User existe, verifie le mot de passe
						if	($row->ADH_password	==	$pwd)	{
									if	($row->FK_EADH_id	>	1)	{

												// Si le mot de passe existe, crée la session de l'adhérent et du bénéficiare (le même par défaut)
												initSessionAdh($row);
												initSessionBenef($row);


												if	($row->FK_EADH_id	==	2)	{
															$_SESSION['message_user']	=	"insEtape2_wait";
															adn_myRedirection("../adherent/add.php");
												}
												adn_myRedirection($chemin);
									}	else	{
												$checkIdentification	=	"checkIns_ko";
									}
						}	else	{
									$checkIdentification	=	"checkPwd_ko";
						}
			}
}
$_SESSION['message_user']	=	$checkIdentification;
adn_myRedirection($chemin);
?>