<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/CheckEmailPHP.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/EnvoiMail.php');

/*	* ******************************************************** */
/*             Initialisation des variables               */
/*	* ******************************************************** */
$idClient	=	$_SESSION['info_client']['suffixe'];
$nomClient	=	$_SESSION['info_client']['nom'];
$autreData['FK_EADH_id']	=	4;	// inscription_grp
$mailEnvoi	=	$_SESSION['info_client']['email_from'];

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../evenement/index.php');
}

if	(strtoupper(substr(PHP_OS,	0,	3))	===	'WIN')	{
		$str_retour	=	"\r\n";
}	else	{
		$str_retour	=	"\n";
}

/*	* ******************************************************** */
/*             Vérification des variables                  */
/*	* ******************************************************** */
$checkRequired	=	"ok";

// Vérification des champs d'adresse si besoin
if	(isSet($_POST['adresses']))	{
		if	($_POST["ADH_ville"]	==	""	||	$_POST["ADH_adresse1"]	==	""	||	$_POST["ADH_zip"]	==	"")	{
				$checkRequired	=	"bad";
		}	else	{
				$formatData['ADH_ville']	=	'LOWERFIRST';
				$formatData['ADH_adresse1']	=	'LOWERFIRST';
				$formatData['ADH_adresse2']	=	'LOWERFIRST';
		}
}

// Vérification des champs obligatoires
if	(!isSet($message))	{
		if	(($_POST["ADH_email"]	==	"")	||	(!adn_checkEmailPHP($_POST['ADH_email']))	||	($_POST["ADH_nom"]	==	"")	||	($_POST["ADH_prenom"]	==	"")
										||	(!($_POST["ADH_password"]	==	""))	&&	((strlen($_POST["ADH_password"])	<	4)	||	(strlen($_POST["ADH_password"])	>	16))	||	($_POST["ADH_password"]	==	"")
										||	($_POST["ADH_password_confirm"]	==	"")	||	($_POST["ADH_password"]	!=	$_POST["ADH_password_confirm"]))	{
				$checkRequired	=	"bad";
		}	else	{
				$query	=	"SELECT *
            FROM t_adherent_adh
            WHERE ADH_nom='"	.	$_POST["ADH_nom"]	.	"' AND ADH_prenom='"	.	$_POST["ADH_prenom"]	.	"' AND ADH_email='"	.	$_POST["ADH_email"]	.	"'";
				$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());
				if	(mysql_num_rows($result))	{
						$checkRequired	=	"insGrp_ko";
				}	else	{
						$formatData['ADH_nom']	=	'UPPER';
						$formatData['ADH_prenom']	=	'LOWERFIRST';
						$formatData['ADH_email']	=	'LOWER';
						$checkArrayVide	=	array();
				}
		}
}
/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
if	($checkRequired	!=	"ok")	{
		$_SESSION['message_user']	=	$checkRequired;
		$page	=	"amis_add.php";
}	else	{
		$langue	=	$_SESSION['lang'];
		$mail	=	$_POST['ADH_email'];

		$majPar	=	getSiteUserId($langue);
		$majLe	=	date('Y-m-d H:i:s');

		$code	=	md5(uniqid());
		$autreData['ADH_lien']	=	$code;

		// Création du nouvel adhérant, et récupération de l'id
		$query	=	adn_creerInsert('t_adherent_adh',	array('action',	'ADH_id',	'Submit',	'ADH_password_confirm',	'adresses',	'id'),	$autreData,	$formatData);
		$check	=	adn_mysql_query($query,	$connexion,	array('insAdhGrp_ok',	'add_ko'),	array('message_user',	'message_debug'));
		$newId	=	mysql_insert_id();

		// Création de l'identifiant unique et mise à jour pour finaliser l'ajout
		if	($check)	{
				$idAdh	=	mb_strtoupper($_POST["ADH_prenom"][0])	.	mb_strtoupper($_POST["ADH_nom"][0])	.	"_"	.	$newId	.	"_"	.	$idClient;
				$query3	=	"UPDATE t_adherent_adh SET ADH_identifiant='$idAdh' WHERE "	.	"ADH_id=$newId";
				$check	=	adn_mysql_query($query3,	$connexion,	array('insAdhGrp_ok',	'add_ko'),	array('message_user',	'message_debug'));
		}

		// Mise à jour de la table log
		if	($check)	{
				$query5	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'ADH', '$newId', 'add')";
				$check	=	adn_mysql_query($query5,	$connexion,	array('insAdhGrp_ok',	'add_ko'),	array('message_user',	'message_debug'));
				$newLogId	=	mysql_insert_id();
		}

		// Création de la relation
		if	($check)	{
				$query	=	"INSERT INTO t_amis_amis (FK_ADH_id, FK_ADHAMI_id) VALUES ('"	.	$_SESSION['info_adherent']['id_adh']	.	"' ,'"	.	$newId	.	"')";
				$check	=	adn_mysql_query($query,	$connexion,	array('insAdhGrp_ok',	'add_ko'),	array('message_user',	'message_debug'));
		}

		// Envoie du mail
		if	($check)	{
				$titre	=	_("Inscription à e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];
				$text	=	_("Bonjour ")	.	$str_retour	.	$str_retour;
				$text	.=	$_SESSION['info_adherent']['prenom_adh']	.	' '	.	$_SESSION['info_adherent']['nom_adh']	.	' '	.	_("vous a inscrit sur e-venement.com pour")	.	" "	.	$nomClient	.	$str_retour;
				$text	.=	"http://"	.	$_SERVER['HTTP_HOST'];
				$text	.=	$str_retour	.	$str_retour;
				$text	.=	_("Votre identifiant est :")	.	" "	.	$idAdh	.	$str_retour;
				$text	.=	_("Votre mot de passe est :")	.	" "	.	$_POST['ADH_password']	.	$str_retour	.	$str_retour;
				$text	.=	_("Cordialement.")	.	$str_retour;
				$text	.=	_("L'équipe de e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];

				if	(!adn_envoiMail($_POST['ADH_email'],	$titre,	$text,	$mailEnvoi))	{
						$_SESSION['message_user']	=	"sendIdByEmail_ko";
				}
		}

		$page	=	"result.php";
}
adn_myRedirection($page);
?>