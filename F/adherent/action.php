<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CheckEmailPHP.php');

$autoriseDelete	=	FALSE;	// Sécurité pour action delete via le get
$table	=	't_adherent_adh';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../evenement/index.php');
}

/*	* ******************************************************** */
/*             Vérification des variables                  */
/*	* ******************************************************** */
$checkRequired	=	"ok";

// récupération des variables POST avec gestion des échappements
$id	=	$_POST['ADH_id'];
$eadh	=	$_POST['FK_EADH_id'];

// Mise des valeurs pour les checkbox à choix unique (boolean)
if	(isSet($_POST['ADH_benevolat']))	{
			$autresData['ADH_benevolat']	=	1;
			if	($_POST["ADH_profession"]	==	"")	{
						$checkRequired	=	"bad";
			}	else	{
						$autresData['ADH_profession']	=	$_POST['ADH_profession'];
						$autresData['ADH_disponibilite']	=	$_POST['ADH_disponibilite'];
			}
}	else	{
			$autresData['ADH_benevolat']	=	0;
			$autresData['ADH_profession']	=	"";
			$autresData['ADH_disponibilite']	=	"";
}

if	(isSet($_POST['ADH_ordination']))	{
			$autresData['ADH_ordination']	=	1;
}	else	{
			$autresData['ADH_ordination']	=	0;
}

if	(isSet($_POST['NEWS_email']))	{
			$newsletter	=	1;
}	else	{
			$newsletter	=	0;
}

// Vérification des champs obligatoires, plus le mail, plus les années
if	(($_POST["ADH_email"]	==	"")	||	($_POST["ADH_langue"]	==	"")	||	(!adn_checkEmailPHP($_POST['ADH_email']))	||	($_POST["ADH_adresse1"]	==	"")	||	($_POST["ADH_zip"]	==	"")
								||	($_POST["ADH_ville"]	==	"")	||	($_POST["ADH_telephone"]	==	"")
								||	(!($_POST["ADH_annee_naissance"]	==	""))	&&	((intval($_POST["ADH_annee_naissance"])	<	1900)	||	intval(($_POST["ADH_annee_naissance"])	>	date("Y")))
								||	(!($_POST["ADH_password"]	==	""))	&&	((strlen($_POST["ADH_password"])	<	4)	||	(strlen($_POST["ADH_password"])	>	16))	||	($_POST["ADH_password"]	==	"")
								||	($_POST["ADH_password_confirm"]	==	"")	||	($_POST["ADH_password"]	!=	$_POST["ADH_password_confirm"]))	{
			$_SESSION['message_user']	=	"bad_post";
			$page	=	"add.php";
			adn_myRedirection($page);
}	else	{
			$formatData['ADH_ville']	=	'LOWERFIRST';
			$formatData['ADH_nom_dharma']	=	'LOWERFIRST';
			$formatData['ADH_profession']	=	'LOWERFIRST';
			$formatData['ADH_adresse1']	=	'LOWERFIRST';
			$formatData['ADH_adresse2']	=	'LOWERFIRST';
			$formatData['ADH_email']	=	'LOWER';
			$checkArrayVide	=	array();

			$majPar	=	getSiteUserId($langue);
			$majLe	=	date('Y-m-d H:i:s');

			$newMail	=	$_POST['ADH_email'];
			$oldMail	=	$_POST['old_email'];	// A comparer pour la mise à jour de la table Newsletter
			$langue	=	$_POST['ADH_langue'];

			/*				* ******************************************************** */
			/*              Execution des requêtes                     */
			/*				* ******************************************************** */
			// Cas où c'est la première fois qu'il complète le formulaire
			if	($eadh	==	2	||	$eadh	==	4)	{	// Passe l'état de l'adhérent en 3 = complet
						$autresData['FK_EADH_id']	=	3;
						$_SESSION['info_adherent']['etat_adh']	=	3;
						$remplaceMessage	=	"insEtape2_ok";
			}
			// Mise à jour de la table adhérent
			$query1	=	adn_creerUpdate($table,	'ADH_id',	$id,	array('action',	'ADH_id',	'Submit',	'ADH_ordination',	'ADH_benevolat',	'NEWS_email',	'CMPT_id',	'ADH_password_confirm',	'old_email',	'FK_EADH_id'),	$autresData,	$formatData,	$checkArrayVide);
			$check	=	adn_mysql_query($query1,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));

			// Suppression des compétences associés à l'adhérent dans la table jointe
			if	($check)	{
						$query2	=	"DELETE FROM tj_adh_cmpt WHERE TJ_ADH_id='$id'";
						$check	=	adn_mysql_query($query2,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
			}

			// Mise à jour de la table jointe des compétences et adhérents
			if	(isSet($_POST['CMPT_id'])	&&	$autresData['ADH_benevolat']	==	1)	{
						foreach	($_POST['CMPT_id']	as	$value)	{
									if	($check)	{
												$query3	=	"INSERT INTO tj_adh_cmpt (TJ_ADH_id, TJ_CMPT_id) VALUES ('$id' ,'$value')";
												$check	=	adn_mysql_query($query3,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}
						}
			}

			// Mise à jour de la table t_newsletter_news
			if	($check)	{
						$query4	=	"SELECT * FROM t_newsletter_news WHERE NEWS_email = '$oldMail'";
						$result	=	mysql_query($query4,	$connexion)	or	die(mysql_error());
						if	(!mysql_num_rows($result))	{
									if	($newsletter)	{
												//ADD
												$query5	=	"INSERT INTO t_newsletter_news (NEWS_email, NEWS_langue) VALUES ('$newMail', '$langue')";
												$check	=	adn_mysql_query($query5,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}
						}	else	{
									if	($newsletter)	{
												//MAJ
												$query5	=	"UPDATE t_newsletter_news SET NEWS_email='$newMail', NEWS_langue='$langue' WHERE NEWS_email = '$oldMail'";
												$check	=	adn_mysql_query($query5,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}	else	{
												//DEL
												$query5	=	"DELETE FROM t_newsletter_news WHERE NEWS_email = '$oldMail'";
												$check	=	adn_mysql_query($query5,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}
						}
			}

// Mise à jour de la table log
			if	($check)	{
						$query6	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'ADH', '$id', 'maj')";
						adn_mysql_query($query6,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
			}

			// Mise à jour de l'information du ratio dans la session s'il a été changé dans le formulaire
			// Permet de calculer les prochains achats en tenant compte du nouveau ratio.
			if	($_SESSION['info_adherent']['id_adh']	==	$_SESSION['info_beneficiaire']['id_benef'])	{
						$newTypeTarif = $_POST['FK_TYTAR_id'];
						$query1	=	"SELECT TYTAR_ratio FROM t_typetarif_tytar WHERE TYTAR_id = $newTypeTarif";
						$result1	=	mysql_query($query1,	$connexion)	or	die(mysql_error());
						$row	=	mysql_fetch_object($result1);
						$_SESSION['info_beneficiaire']['ratio_tarif']	=	$row->TYTAR_ratio;
			}
}
$page	=	"../adherent/add.php?maj=1";	// maj=1 : Permet de réactualiser la session
if	(isset($remplaceMessage))	{	$_SESSION['message_user']	=	$remplaceMessage;	}
adn_myRedirection($page);

// Au cas ou aucune action ne serait initiée. A placer en dernier des pages action.php
header("Content-Type: text/html; charset=utf-8");
$msg	=	_("L'action a échouée.")	.	'<br /><a href="add.php">'	.	_("retour")	.	'</a>';
echo	$msg;
?>