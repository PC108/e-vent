<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../B/commande/_requete.php');
require_once('../../B/achat/_requete.php');
require_once('../_fonction/bodyCmd.php');
require_once('../../librairie/php/code_adn/Formatage.php');

$title	=	_("Impression de la commande");

/*	* ******************************************************** */
/*              Gestion du numéro de page et de la langue                 */
/*	* ******************************************************** */

if	(isSet($_GET['pageNum']))	{
			$pageNum	=	$_GET['pageNum'];
}	else	{
			$pageNum	=	0;
}

if	(isSet($_GET['lg']))	{
			$langue	=	$_GET['lg'];
}	else	{
			$langue	=	'fr';
}

/*	* ******************************************************** */
/*              Vérification GET lien + execution des requetes                */
/*	* ******************************************************** */

// Construction de l'url de retour
switch	($_GET['retour'])	{
			case	'cmd_BO'	:
						$return_url	=	"../../B/commande/result.php";
						break;
			case	'cmd_FO'	:
						$return_url	=	"result.php";
						break;
			case	'historique'	:
						$return_url	=	"historique.php";
						break;
			case	'paypal'	:
						$return_url	=	"../../B/paypal/result.php";
						break;
			case	'stat'	:
						$return_url	=	"../../B/statistique/ca_mois_detail.php?date_sql="	.	$_GET['date_sql'];
						break;
}

if	(isSet($_GET['pageNum']))	{	// Back
			$return_url	.="?pageNum="	.	$_GET['pageNum'];
}

if	(isset($_GET["lien"]))	{
			$link	=	htmlentities($_GET["lien"]);
			$query	=	mainQueryCmd($connexion);
			$query.=	" WHERE CMD_lien='$link'";
			$RSCmd	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			$row	=	mysql_fetch_array($RSCmd);
			$idCmd	=	$row['CMD_id'];
			mysql_data_seek($RSCmd,	0);
			$query	=	mainQueryAch($idCmd,	$connexion);
			$query.="ORDER BY FK_ADH_id, EVEN_id, JREVEN_date_debut, TYACH_ordre ASC, TYHEB_id, TYRES_id";
			$RSAchat	=	mysql_query($query,	$connexion)	or	die(mysql_error());
}	else	{
			$_SESSION['message_user']	=	"bad_get";
			adn_myRedirection($return_url);
}

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
			<head>
						<title>e-venement.com | <?php	echo	$title;	?></title>
						<meta NAME="author" CONTENT="www.atelierdu.net" />
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<!-- CSS -->
						<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/cmd_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="conteneur_print" class="corner20-all">
									<div>
												<a href="<?php	echo	$return_url;	?>"><div class="bt_lien corner10-all espace10"><?php	echo	_("retour");	?></div></a>
												<a href="javascript:window.print()"><div class="bt_lien corner10-all espace10 pointer"><?php	echo	_("imprimer");	?></div></a>
									</div>
									<?php	echo	htmlBodyCmd($_SESSION['info_client'],	$RSCmd,	$RSAchat,	$langue,	FALSE,	$configAppli);	?>
						</div>
			</body>
</html>