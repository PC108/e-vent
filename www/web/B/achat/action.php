<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('../../librairie/php/code_adn/NombreJours.php');

$table	=	't_achat_ach';
$autoriseDelete	=	FALSE;	// Sécurité pour action delete via le get

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
if	(isset($_GET['cmdid']))	{
			$action	=	"del_all";
			$cmdId	=	$_GET['cmdid'];
}	else	if	(isset($_POST['action']))	{
			$action	=	$_POST['action'];
			$id	=	$_POST['ACH_id'];

			// Vérification des champs obligatoires
			if	($_POST["ACH_remb"]	==	"")	{
						$checkRequired	=	"bad";
			}	else	{
						$formatData	=	array();
						$checkArrayVide	=	array();
			}
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
									if	(isSet($_POST['ACH_participe']))	{
												$autresData['ACH_participe']	=	1;
									}	else	{
												$autresData['ACH_participe']	=	0;
									}

									$query	=	adn_creerUpdate($table,	'ACH_id',	$id,	array('action',	'ACH_id',	'idcmd',	'Submit',	'pageNum',	'date',	'ACH_participe'),	$autresData,	$formatData,	$checkArrayVide);
									$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));

									// Mise à jour de la table log
									if	($check)	{
												$idCmd	=	$_POST['idcmd'];	//Inscrit le log au niveau de la commande car il n'y a pas de gestion des logs au niveau de l'achat.
												$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'CMD', '$idCmd', '$action')";
												adn_mysql_query($query2,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}
									break;

						case	"add":	// Inutilisé dans ce cas (a adapter pour le faire fonctionner)
									$query	=	adn_creerInsert($table,	array('action',	'ACH_id',	'Submit',	'pageNum'),	$autresData,	$formatData);
									$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
									break;

						case	"del":
									$query	=	"DELETE FROM "	.	$table	.	" WHERE ACH_id='"	.	mysql_real_escape_string($id)	.	"'";
									$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
									break;

						case	"del_all":
									$query	=	"DELETE FROM "	.	$table	.	" WHERE FK_CMD_id='"	.	mysql_real_escape_string($cmdId)	.	"'";
									$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
									break;
			}
}

$page	=	"../commande/result.php?pageNum="	.	$pageNum;

// TEST : désactiver adn_myRedirection($page)
//echo	$action	.	"<br/>";
//echo	$id	.	"<br/>";
adn_myRedirection($page);
?>