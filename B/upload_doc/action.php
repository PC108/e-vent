<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');

$autoriseDelete	=	TRUE;	// Sécurité pour action delete via le get
$table	=	't_doceven_doceven';
$repertoireDest	=	"../../upload/doc/";

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
			$id	=	$_POST['DOCEVEN_id'];

// Vérification des champs obligatoires, plus le mail, plus les années
			if	(($_POST["DOCEVEN_nom"]	==	""))	{
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
/*              Execution des requêtes                     */
/*	* ******************************************************** */
if	($checkRequired	==	"bad")	{
			$_SESSION['message_user']	=	"bad_post";
}	else	{
			switch	($action)	{
						case	"maj":
									$query	=	adn_creerUpdate($table,	'DOCEVEN_id',	$id,	array('action',	'DOCEVEN_id',	'Submit',	'pageNum'),	$autresData,	$formatData,	$checkArrayVide);
									adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									break;
						case	"add":
									$query	=	adn_creerInsert($table,	array('action',	'DOCEVEN_id',	'Submit',	'pageNum'),	$autresData,	$formatData);
									adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
									break;
						case	"del":
									if	($autoriseDelete)	{
												// récupère le nom du fichier à supprimer (avant de supprimer l'enregistrement...)
												$query2	=	"SELECT * FROM "	.	$table	.	" WHERE DOCEVEN_id='"	.	mysql_real_escape_string($id)	.	"'";
												$RS2	=	mysql_query($query2,	$connexion)	or	die(mysql_error());
												$row_RS2	=	mysql_fetch_object($RS2);
												// et supprime le fichier
												$chemin = $repertoireDest	.	$row_RS2->DOCEVEN_file;
												if	(is_file($chemin))	{
															$check	=	unlink($repertoireDest	.	$row_RS2->DOCEVEN_file);
												} else {
															$check = true;
												}

												if	($check)	{
															// Supprime l'enregistrement
															$query	=	"DELETE FROM "	.	$table	.	" WHERE DOCEVEN_id='"	.	mysql_real_escape_string($id)	.	"'";
															$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
												}	else	{
															$_SESSION['message_user']	=	"del_ko";
												}
									}
									break;
			}
}

$page	=	"result.php?pageNum="	.	$pageNum;
adn_myRedirection($page);
?>