<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');

$autoriseDelete	=	TRUE;	// Sécurité pour action delete via le get
$table	=	't_commande_cmd';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Gestion du numéro de page                  */
/*	* ******************************************************** */
// On garde le numéro de page courant en paramètre
// POST lorsqu'on vient de add.php(add & maj), GET quand on vient de result.php (del)
if	(isSet($_POST['pageNum']))	{
			$pageNum	=	$_POST['pageNum'];
}	elseif	(isSet($_GET['pageNum']))	{
			$pageNum	=	$_GET['pageNum'];
}

/*	* ******************************************************** */
/*             Vérification des variables                  */
/*	* ******************************************************** */
$checkRequired	=	"ok";

// récupération des variables POST avec gestion des échappements
if	(isSet($_POST['action']))	{
			$action	=	$_POST['action'];
			$id	=	$_POST['CMD_id'];

			/* Vérification des champs obligatoires */
			if	(($_POST["CMD_remise"]	==	"")	||	($_POST["CMD_encaissement"]	==	""))	{
						$checkRequired	=	"bad";
			}	else	{
						$formatData	=	array();
						$checkArrayVide	=	array();
			}

			/* Modification de la  date de confirmation */
			$autresData	=	array();
			if	($_POST["FK_ECMD_id"]	==	6)	{	// confirmée
						$dateConfirm	=	$_POST['CMD_date_confirm'];
			}	else	{
						$dateConfirm	=	"0000-00-00";
			}
			$autresData['CMD_date_confirm']	=	$dateConfirm;

}	else	{
			$action	=	"del";
			$id	=	$_GET["id"];
}

/*	* ******************************************************** */
/*                Exécution des actions                    */
/*	* ******************************************************** */
if	($checkRequired	==	"bad")	{
			$_SESSION['message_user']	=	"bad_post";
}	else	{
			$majPar	=	$_SESSION['user_info'][2];
			$majLe	=	date('Y-m-d H:i:s');
			switch	($action)	{
						case	"maj":
									$query	=	adn_creerUpdate($table,	'CMD_id',	$id,	array('action',	'CMD_id',	'Submit',	'pageNum',	'CMD_date_confirm',	'affiche_date_confirm'),	$autresData,	$formatData,	$checkArrayVide);
									$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));

									/* Mise à jour de la table log */
									if	($check)	{
												$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'CMD', '$id', '$action')";
												adn_mysql_query($query2,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}
									break;

						case	"add":	// Inutilisé dans ce cas (a adapter pour le faire fonctionner)

									$query	=	adn_creerInsert($table,	array('action',	'CMD_id',	'Submit',	'pageNum'),	$autresData,	$formatData);
									$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));

									/* Mise à jour de la table log */
									if	($check)	{
												$newId	=	mysql_insert_id();
												$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'CMD', '$newId', '$action')";
												adn_mysql_query($query2,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
									}
									break;

						case	"del":
									if	($autoriseDelete)	{

												/* Suppression de la commande */
												$query	=	"DELETE FROM "	.	$table	.	" WHERE CMD_id='"	.	mysql_real_escape_string($id)	.	"'";
												$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));

												if	($check)	{
															$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'CMD', '$id', '$action')";
															$check	=	adn_mysql_query($query2,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
												}

									}
									break;
			}
}

$page	=	"result.php?pageNum="	.	$pageNum;
if	($action	==	"add")	{	$page	.=	"&clean=1";	}

// echo	$query;
adn_myRedirection($page);
?>