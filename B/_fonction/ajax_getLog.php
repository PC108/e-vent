<?php

//DEBUGGAGE
// header("Content-Type: text/html; charset=iso-8859-1");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");

ini_set('html_errors',	0);

/***********************************************************/
/*              Connexion DB  + autres            */
/***********************************************************/
include('../../F/_fonction/_shared_ajax.php');
require_once('../../librairie/php/code_adn/Formatage.php');

/*	* ******************************************************** */
/*              Action                    */
/*	* ******************************************************** */
if	(isset($_POST['id'])	&&	isset($_POST['table']))	{
		$id	=	$_POST['id'];
		$table	=	$_POST['table'];

// création de la requête
		$query_RS	=	"select LOG_action,LOG_date, ACS_login
    FROM t_log_log AS log
    join t_acces_acs AS acs ON
        acs.ACS_id=log.FK_ACS_id
        WHERE LOG_idrow = $id and LOG_table = '$table'
    ORDER BY LOG_date DESC";
		$RS	=	mysql_query($query_RS,	$connexion)	or	die(mysql_error());
		$NbreRS	=	mysql_numrows($RS);

		$html	=	"";

		if	(mysql_num_rows($RS)	==	0)	{
				echo	('no result !');
		}	else	{
				$html	=	"";
				while	($row	=	mysql_fetch_object($RS))	{
						if	($row->LOG_action	==	"add")	{
								$html	.=	"Créé par <b>"	.	$row->ACS_login	.	"</b> "	.	adn_afficheDateTime($row->LOG_date,	"DB_FR")	.	"<br/>";
						}	else	{
								$html	.=	"Modifié par <b>"	.	$row->ACS_login	.	"</b> "	.	adn_afficheDateTime($row->LOG_date,	"DB_FR")	.	"<br/>";
						}
				}
				ltrim($html,	"<br/>");
				echo	nl2br($html);
		}
}	else	{
		echo	('bad param !');
}
// print_r($rows);
?>