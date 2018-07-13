<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../evenement/index.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

// AJOUTER UN LIEN 

if	(isset($_POST['arrayAdhLiens'])	&&	isset($_POST['action'])	&&	($_POST['action']	==	"add"))	{	// Vient de amis/search_result.php
		$str_valuesToAdd	=	"";
		foreach	($_POST['arrayAdhLiens']	as	$value)	{
				$str_valuesToAdd	.=	"('"	.	$_SESSION['info_adherent']['id_adh']	.	"','"	.	$value	.	"'),";
		}
		$str_valuesToAdd	=	rtrim($str_valuesToAdd,	",");
		// Ajout dans la table amis
		$query	=	"REPLACE INTO t_amis_amis (FK_ADH_id, FK_ADHAMI_id) VALUES "	.	$str_valuesToAdd;
		mysql_query($query,	$connexion)	or	die(mysql_error());
		adn_myRedirection("result.php?rep=linkok");
}


// SUPPRIMER UN LIEN
if	(isset($_GET['idAdhLien'])	&&	isset($_GET['action'])	&&	($_GET['action']	==	"del"))	{	// Vient du dialog de amis/result.php
		// Suppression dans la table amis
		$query	=	"DELETE	FROM	t_amis_amis	WHERE	FK_ADH_id="	.	$_SESSION['info_adherent']['id_adh']	.	"	AND	FK_ADHAMI_id="	.	$_GET['idAdhLien'];
		mysql_query($query,	$connexion)	or	die(mysql_error());
		adn_myRedirection("result.php");
}

// DESACTIVER UN LIEN
if	(isset($_GET['idAdhLien'])	&&	isset($_GET['action'])	&&	($_GET['action']	==	"unlink"))	{	// Vient de amis/result.php
		// Mise à jour dans la table amis
		$query	=	"UPDATE	t_amis_amis	SET ADHAMI_actif=0 WHERE	FK_ADH_id="	.	$_GET['idAdhLien']	.	"	AND	FK_ADHAMI_id="	.	$_SESSION['info_adherent']['id_adh'];
		mysql_query($query,	$connexion)	or	die(mysql_error());
		adn_myRedirection("result.php");
}

// Au cas ou aucune action ne serait initiée. A placer en dernier des pages action.php
header("Content-Type: text/html; charset=utf-8");
$msg	=	_("L'action a échouée.")	.	'<br /><a href="result.php">'	.	_("retour")	.	'</a>';
echo	$msg;
?>
