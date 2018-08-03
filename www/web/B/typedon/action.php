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
$table	=	't_typedon_tydon';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'cmd')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

// Vérification si la cotisation est activée
if	($configAppli['MENU']['don']	==	"non")	{
		$_SESSION['message_user']	=	"don_ko";
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
		$id	=	$_POST['TYDON_id'];

		if	(isSet($_POST['TYDON_visible']))	{
				$autresData['TYDON_visible']	=	1;
		}	else	{
				$autresData['TYDON_visible']	=	0;
		}

// Vérification des champs obligatoires, plus le mail, plus les années
		if	(($_POST["TYDON_nom_fr"]	==	"")	||	($_POST["TYDON_nom_en"]	==	""))	{
				$checkRequired	=	"bad";
		}	else	{
				// $formatData['TYDON_nom_fr'] = 'LOWERFIRST';
				// $formatData['TYDON_nom_en'] = 'LOWERFIRST';
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
						$query	=	adn_creerUpdate($table,	'TYDON_id',	$id,	array('action',	'TYDON_id',	'Submit',	'TYDON_visible',	'pageNum'),	$autresData,	$formatData,	$checkArrayVide);
						adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
						break;
				case	"add":
						$query	=	adn_creerInsert($table,	array('action',	'TYDON_id',	'Submit',	'TYDON_visible',	'pageNum'),	$autresData,	$formatData);
						adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
						break;
				case	"del":
						if	($autoriseDelete)	{
								$query	=	"DELETE FROM "	.	$table	.	" WHERE TYDON_id='"	.	mysql_real_escape_string($id)	.	"'";
								adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
						}
						break;
		}
}

$page	=	"result.php?pageNum="	.	$pageNum;
adn_myRedirection($page);
?>