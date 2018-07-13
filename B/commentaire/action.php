<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher',	'cmd',	'event')))	{
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
		$idCommentaire	=	$_POST['idcom'];
		$tableId	=	$_POST["table"];
		$idFiche	=	$_POST['idfiche'];
		if	($_POST["CMT_commentaire"]	==	"")	{
				$checkRequired	=	"bad";
		}	else	{
				$commentaire	=	adn_quote_smart($_POST['CMT_commentaire']);
		}
}	else	{
		$action	=	"del";
		$idCommentaire	=	$_GET["idcom"];
		$tableId	=	$_GET["table"];
		$idFiche	=	$_GET["idfiche"];
}

// Tables ou les commentaires sont activés
switch	($tableId)	{
		case	"ACS":
				$table	=	"t_acces_acs";
				$repertoire	=	"acces";
				break;
		case	"ADH":
				$table	=	"t_adherent_adh";
				$repertoire	=	"adherent";
				break;
		case	"CMD":
				$table	=	"t_commande_cmd";
				$repertoire	=	"commande";
				break;
		case	"EVEN":
				$table	=	"t_evenement_even";
				$repertoire	=	"evenement";
				break;
}

/*	* ******************************************************** */
/*                Exécution des actions                    */
/*	* ******************************************************** */

if	($checkRequired	==	"bad")	{
		$_SESSION['message_user']	=	"bad_post";
}	else	{
		switch	($action)	{
				case	"maj":
						$query	=	"UPDATE t_commentaire_cmt SET CMT_commentaire='$commentaire' WHERE CMT_id=$idCommentaire";
						adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
						break;
				case	"add":
						$query1	=	"INSERT INTO t_commentaire_cmt (CMT_commentaire) VALUES ('$commentaire')";
						$check	=	adn_mysql_query($query1,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));

						if	($check)	{
								// récupération du nouvel identifiant
								$idCommentaire	=	mysql_insert_id();

								$query2	=	"UPDATE $table SET FK_CMT"	.	$tableId	.	"_id='$idCommentaire' WHERE "	.	$tableId	.	"_id=$idFiche";
								adn_mysql_query($query2,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
						}
						break;
				case	"del":
						$query1	=	"DELETE FROM t_commentaire_cmt WHERE CMT_commentaire='"	.	mysql_real_escape_string($idCommentaire)	.	"'";
						$check	=	adn_mysql_query($query1,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));

						if	($check)	{
								$query2	=	"UPDATE $table SET FK_CMT"	.	$tableId	.	"_id=NULL WHERE "	.	$tableId	.	"_id=$idFiche";
								adn_mysql_query($query2,	$connexion,	array('del_ok',	'maj_ko'),	array('message_user',	'message_debug'));
						}
						break;
		}
}

$page	=	"../"	.	$repertoire	.	"/result.php?pageNum="	.	$pageNum;
adn_myRedirection($page);
?>
