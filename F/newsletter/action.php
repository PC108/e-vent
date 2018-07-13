<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CheckEmailPHP.php');


/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
if	(isset($_POST['email'])	&&	adn_checkEmailPHP($_POST['email']))	{
		$mail	=	$_POST['email'];
		$langue	=	strtoupper($_SESSION['lang']);
		// Recherche des email dans la table newsletter correspondant à celui passé par l'utilisateur
		$query	=	"SELECT * FROM t_newsletter_news WHERE NEWS_email = '$mail'";
		$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());

		if	(isset($_POST['sign-in']))	{
				if	(!mysql_num_rows($result))	{
						// Si on veut s'inscrire et qu'on a pas d'email en BD
						$query2	=	"INSERT INTO t_newsletter_news (NEWS_email, NEWS_langue) VALUES ('$mail', '$langue')";
						$check	=	adn_mysql_query($query2,	$connexion,	array('addNews_ok',	'add_ko'),	array('message_user',	'message_debug'));
						$newId	=	mysql_insert_id();
				}	else	{
						$_SESSION['message_user']	=	"addNews_ko";
				}
		}	else	{
				if	(!mysql_num_rows($result))	{
						$_SESSION['message_user']	=	"delNews_ko";
				}	else	{
						// Si on veut se désinscrire et que l'email est en BD
						$query3	=	"DELETE FROM t_newsletter_news WHERE NEWS_email = '$mail'";
						$check	=	adn_mysql_query($query3,	$connexion,	array('delNews_ok',	'del_ko'),	array('message_user',	'message_debug'));
				}
		}
}	else	{
		$_SESSION['message_user']	=	"bad_post";
}

if	($_POST['chemin'])	{
		adn_myRedirection($_POST['chemin']);
}

// Au cas ou aucune action ne serait initiée. A placer en dernier des pages action.php
header("Content-Type: text/html; charset=utf-8");
$msg	=	_("L'action a échouée.")	.	'<br /><a href="add.php">'	.	_("retour")	.	'</a>';
echo	$msg;
?>