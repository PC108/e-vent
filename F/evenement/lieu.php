<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/Formatage.php');

$title	=	_("Lieu de l'événement");

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$check	=	"ok";
if	(isset($_GET['idlieu']))	{
			$idLieu	=	$_GET['idlieu'];
			$query	=	"
						SELECT *
						FROM t_lieuevent_leven
						LEFT JOIN t_pays_pays ON FK_PAYS_id = PAYS_id
						WHERE LEVEN_id = $idLieu";
			$RSLieu	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			$NbreRows_RSLieu	=	mysql_num_rows($RSLieu);
			if	($NbreRows_RSLieu	==	0)	{
						$check	=	"lieu_ko";
			}	else	{
						$rowLieu	=	mysql_fetch_array($RSLieu);
						$nomLieu	=	$rowLieu['LEVEN_nom'];
						$adresseLieu	=	$rowLieu['LEVEN_adresse1'];
						if	(!is_null($rowLieu['LEVEN_adresse2']))	{
									$adresseLieu	.=	"<br /> ";
									$adresseLieu	.=	$rowLieu['LEVEN_adresse2'];
						}
						$villeLieu	=	$rowLieu['LEVEN_ville'];
						$zipLieu	=	$rowLieu['LEVEN_zip'];
						$paysLieu	=	$rowLieu['PAYS_nom_'	.	$_SESSION['lang']];
						$longitude	=	$rowLieu['LEVEN_longitude'];
						$latitude	=	$rowLieu['LEVEN_latitude'];
						$zoom	=	$rowLieu['LEVEN_zoom'];
			}
}	else	{
			$check	=	"lieu_ko";
}

if	($check	==	"lieu_ko")	{
			$_SESSION['message_user']	=	$check;
			adn_myRedirection('../evenement/index.php');
}

$afficheJrEvent	=	TRUE;
if	(isset($_GET['idjrevent']))	{
			$idJrEvent	=	$_GET['idjrevent'];
			$query	=	"
				SELECT *
				FROM t_jourevent_jreven
				LEFT JOIN t_evenement_even ON FK_EVEN_id = EVEN_id
				WHERE JREVEN_id = $idJrEvent";
			$RSJrEvent	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			$NbreRows_RSJrEvent	=	mysql_num_rows($RSJrEvent);
			if	($NbreRows_RSJrEvent	==	0)	{
						$afficheJrEvent	=	FALSE;
			}	else	{
						$rowJrEvent	=	mysql_fetch_array($RSJrEvent);
						$nomEvent	=	$rowJrEvent['EVEN_nom_'	.	$langue];
						$dateJrEvent	=	adn_afficheFromDateToDate($rowJrEvent['JREVEN_date_debut'],	$rowJrEvent['JREVEN_date_fin'],	"DB_"	.	$langue);
			}
}	else	{
			$afficheJrEvent	=	FALSE;
}

/*	* ******************************************************** */
/*              Retour                     */
/*	* ******************************************************** */
if	(isset($_SESSION['info_adherent']))	{
			$retour	=	"evenement.php";
}	else	{
			$retour	=	"index.php";
}
/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php	echo	$langue;	?>" xml:lang="<?php	echo	$langue;	?>">
			<head>
						<title>e-venement.com | <?php	echo	$title;	?></title>
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<meta http-equiv="Content-Language" content="<?php	echo	$langue;	?>" />
						<link rel="icon" type="image/png" href="../_media/GEN/favicon.png" />
						<!-- JS -->
						<?php	include('../_shared_js.php');	?>
						<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
						<script type="text/javascript">
									// GOOGLE MAPS
									var geocoder;
									var map;
									var pointeur;
									var latitude = <?php	echo	$latitude	?>;
									var longitude = <?php	echo	$longitude	?>;
									var niveauzoom = <?php	echo	$zoom	?>

									function initialize() {
												var myLatlng = new google.maps.LatLng(latitude, longitude);
												var myOptions = {
															zoom: niveauzoom,
															center: myLatlng,
															mapTypeId: google.maps.MapTypeId.ROADMAP
												}
												map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
												pointeur = new google.maps.Marker({
															position: myLatlng,
															animation: google.maps.Animation.DROP,
															map: map,
															title:"<?php	echo	$nomLieu	?>"
												});
									}

									$(document).ready(function() {
												// Initialise Google Maps
												initialize();

									});
						</script>
						<!-- CSS -->
						<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
						<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
						<link href="../_css/style_front.css" rel="stylesheet" type="text/css" />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="global">
									<?php	include('../_header.php');	?>
									<?php	include('../_sidebar.php');	?>

									<div id="content" class="corner20-all">
												<div style="float:right;"><a href=<?php	echo	$retour;	?>><div class="bt_lien corner10-all espace10"><?php	echo	_("retour");	?></div></a></div>
												<h1><?php	echo	$title;	?></h1>
												<h2><?php	echo	$nomLieu;	?></h2>
												<p><?php	echo	$adresseLieu;	?>
															<br /><?php	echo	$zipLieu;	?> <?php	echo	$villeLieu;	?>
															<br /><?php	echo	$paysLieu;	?>
												</p>
												<?php	if	($afficheJrEvent)	{	?>
															<div class="bloc_jour corner20-all fd_bleu" style="float:right; padding:5px 10px; margin-bottom: 10px;"><?php	echo	$nomEvent;	?>, <?php	echo	$dateJrEvent[0];	?></div>
												<?php	}	?>
												<div id="map_canvas" style="width: 100%; height: 400px;"></div>
									</div>
									<?php	include('../_footer.php');	?>
						</div>
			</body>
</html>