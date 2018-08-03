<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// Requete pour récupérer tous les adhérents qui ont l'état non validé et qui se sont inscris il y a plus de 7 jours.
// Le group by permet de prendre un seul id même s'il y a plusieurs entrées dans la table log.
// Il suffit qu'une seule de ces entrées soit plus vieille que les 7 jours limites pour qu'elle soit supprimée.
$query	=	"
		SELECT ADH_id, ADH_nom, ADH_prenom 
		FROM t_adherent_adh 
		WHERE FK_EADH_id = 1 
		AND EXISTS
				(SELECT LOG_idrow
				FROM t_log_log
				WHERE LOG_table = 'ADH'
				AND DATEDIFF( CURDATE( ) , LOG_date ) > 7
				AND LOG_idrow = ADH_id
				GROUP BY LOG_idrow)";
$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());

if	(isset($_GET["del"])	&&	$_GET["del"]	==	"ok")	{
		$msg	=	"deladh_ok";

		$count	=	0;
		// Requetes de suppression dans la table adherent et log
		if	(mysql_num_rows($result))	{
				// Requete pour les adhérents
				$query2	=	"DELETE FROM t_adherent_adh WHERE ADH_id IN (";
				// Requete pour les logs
				$query3	=	"DELETE FROM t_log_log WHERE LOG_table = 'ADH' AND LOG_idrow IN (";

				while	($row	=	mysql_fetch_object($result))	{
						$query2	.=	"$row->ADH_id, ";
						$query3	.=	"$row->ADH_id, ";
						$count++;

						// On supprime tous les enregistrements de l'adherent des tables jointes (contrainte SQL)
						$query5	=	"DELETE FROM tj_adh_cmpt WHERE TJ_ADH_id = $row->ADH_id";
						$result5	=	mysql_query($query5,	$connexion)	or	die(mysql_error());
				}

				$query2	=	rtrim($query2,	", ")	.	")";
				$result2	=	mysql_query($query2,	$connexion)	or	die(mysql_error());
				$query3	=	rtrim($query3,	", ")	.	")";
				$result3	=	mysql_query($query3,	$connexion)	or	die(mysql_error());
		}	else	{
				$msg	=	"deladh_noadh";
		}
		$_SESSION['message_user']	=	$msg;
		adn_myRedirection("cleanAdh.php");
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['CONFIGURATION']['L3']['nom'];

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
		<head>
				<title><?php	echo	$titre;	?></title>
				<meta NAME="author" CONTENT="www.atelierdu.net" />
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
				<link rel="icon" type="image/png" href="../_media/favicon.png" />
				<!-- JS -->
				<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
				<script type="text/javascript" src="../_shared.js"></script>
				<!-- CSS -->
				<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
				<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
				<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
		</head>
		<body>
				<?php	include("../_header.php");	?>
				<div id="contenu">
						<p>Depuis cette page, vous pouvez supprimer toutes les inscriptions de plus de 7 jours dont l'état = demande.
								<br />(Ces inscriptions n'ont pas été confirmées en cliquant sur le lien du mail automatique.)</p>
						<ul class="menu_gauche">
								<li class="boutons"><a href="?del=ok">Supprimer les inscriptions ci-dessous</a></li>
						</ul>
						<h2>Résultat de la recherche</h2>
						<?php	if	(mysql_num_rows($result))	{	?>
								<ul>
										<?php	while	($row	=	mysql_fetch_object($result))	{	?>
												<li><?php	echo	"<b>"	.	$row->ADH_nom	.	"</b> "	.	$row->ADH_prenom;	?></li>
										<?php	}	?>
								</ul>
						<?php	}	else	{	?>
								<p>Aucune inscription à supprimer.</p>
						<?php	}	?>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>