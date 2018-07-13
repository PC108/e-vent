<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('_requete.php');

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
// Conservation du numéro de page
if	(isSet($_GET['pageNum']))	{
		$pageNum	=	$_GET['pageNum'];
}	else	{
		$pageNum	=	0;
}

/*	* ******************************************************** */
/*               Définition des variables                  */
/*	* ******************************************************** */
// récupération de la variable GET $id
$id	=	$_GET["id"];
if	($id	==	0)	{
// Mode ADD
		$Action	=	"add";
		$Submit	=	"Ajouter";
		$nom	=	"";
		$adr1	=	"";
		$adr2	=	"";
		$ville	=	"";
		$zip	=	"";
		$idPays	=	1;
		$lieuEvent	=	"Tour Eiffel Paris";
		$latitude	=	48.8584188;
		$longitude	=	2.2945976;
		$zoom	=	14;
}	else	{
// Mode MAJ
		$Action	=	"maj";
		$Submit	=	"Modifier";
// création du recordset
		$query	=	queryLieuEvent();
		$query	.=	"WHERE LEVEN_id=$id ";
		$query	.=	"ORDER BY LEVEN_nom ASC";
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
		$row	=	mysql_fetch_object($RS);

		$nom	=	$row->LEVEN_nom;
		$adr1	=	$row->LEVEN_adresse1;
		$adr2	=	$row->LEVEN_adresse2;
		$zip	=	$row->LEVEN_zip;
		$ville	=	$row->LEVEN_ville;
		$idPays	=	$row->FK_PAYS_id;
		$lieuEvent	=	$adr1	.	" "	.	$zip	.	" "	.	$ville;
		if	(is_null($row->LEVEN_latitude))	{	$latitude	=	48.8584188;	}	else	{	$latitude	=	$row->LEVEN_latitude;	}
		if	(is_null($row->LEVEN_longitude))	{	$longitude	=	2.2945976;	}	else	{	$longitude	=	$row->LEVEN_longitude;	}
		if	(is_null($row->LEVEN_zoom))	{	$zoom	=	14;	}	else	{	$zoom	=	$row->LEVEN_zoom;	}
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

$query_RS2	=	"SELECT * FROM t_pays_pays ORDER BY PAYS_tri_fr ASC";
$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L2']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
		<head>
<!--	<title><?php	echo	$titre;	?></title>-->
				<meta NAME="author" CONTENT="www.atelierdu.net" />
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
				<link rel="icon" type="image/png" href="../_media/favicon.png" />
				<!-- JS -->
				<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
				<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
				<script type="text/javascript" src="../_shared.js"></script>
				<script type="text/javascript">

						// GOOGLE MAPS
						var geocoder;
						var map;
						var pointeur;
						var latitude = <?php	echo	$latitude	?>;
						var longitude = <?php	echo	$longitude	?>;
						var niveauzoom = <?php	echo	$zoom	?>

						function initialize() {
								geocoder = new google.maps.Geocoder();
								var myLatlng = new google.maps.LatLng(latitude, longitude);
								var myOptions = {
										zoom: niveauzoom,
										center: myLatlng,
										mapTypeId: google.maps.MapTypeId.ROADMAP
								}
								map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
								pointeur = new google.maps.Marker({
										position: myLatlng,
										draggable:true,
										animation: google.maps.Animation.DROP,
										map: map,
										title:"Déplacez moi !"
								});
						}

						function codeAddress() {
								var address = document.getElementById("address").value;
								geocoder.geocode( { 'address': address}, function(results, status) {
										if (status == google.maps.GeocoderStatus.OK) {
												map.setCenter(results[0].geometry.location);
												pointeur.setPosition(results[0].geometry.location);
												getLatLngMarker(pointeur);
												afficheLatLng();
										} else {
												alert("Impossible de trouver cette adresse. Erreur de type : " + status);
										}
								});
						}

						function getLatLngMarker(leMarqueur) {
								pos = leMarqueur.getPosition();
								latitude = pos.lat();
								longitude = pos.lng();
						}

						function afficheLatLng() {
								latitude = Math.round(latitude*100000)/100000;
								$('#LEVEN_latitude').val(latitude);
								longitude = Math.round(longitude*100000)/100000;
								$('#LEVEN_longitude').val(longitude);
						}

						function afficheZoom() {
								niveauzoom = map.getZoom();
								$('#LEVEN_zoom').val(niveauzoom);
						}

						// JQUERY

						// Ajoute l'adresse au champ de recherche
						function updateStrAdresse() {
								var strAdresse = $('#LEVEN_adresse1').val() + " " + $('#LEVEN_zip').val() + " " + $('#LEVEN_ville').val();
								$('#address').val(strAdresse);
						}

						$(document).ready(function() {

								//Validation
								$("#form_lieuevent").validate({
										rules: {
												'LEVEN_nom': {
														required: true,
														minlength: 2
												},
												'LEVEN_adresse1': {
														required: true,
														minlength: 2
												},
												'LEVEN_ville': {
														required: true,
														minlength: 2
												},
												'LEVEN_zip': {
														required: true,
														minlength: 2
												}
										}
								});

								// Mise à jour de l'adresse de recherche
								$('#LEVEN_adresse1, #LEVEN_zip, #LEVEN_ville').keyup(function() {
										updateStrAdresse();
								});

								// Initialise Google Maps
								initialize();
								afficheLatLng();
								afficheZoom();

								google.maps.event.addListener(pointeur, 'dragend', function() {
										getLatLngMarker(pointeur);
										afficheLatLng();
										map.panTo(pos);
								});

								google.maps.event.addListener(map, 'zoom_changed', function() {
										afficheZoom();
								});

						});

				</script>
				<!-- CSS -->
				<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
		</head>
		<body>
				<?php	include("../_header.php");	?>
				<div id="contenu">
						<form id="form_lieuevent" action="action.php" method="post">
								<div class="BoxSearch">
										<h2>Lieu de l'évenement</h2>
										<div class="form_hr">
												<div class="label_form label_R">Nom</div>
												<div class="content_form"><input id="LEVEN_nom" name="LEVEN_nom" type="text" value="<?php	echo	htmlspecialchars($nom);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Adresse 1</div>
												<div class="content_form"><input id="LEVEN_adresse1" name="LEVEN_adresse1" type="text" value="<?php	echo	htmlspecialchars($adr1);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Adresse 2</div>
												<div class="content_form"><input  id="LEVEN_adresse2" name="LEVEN_adresse2" type="text" value="<?php	echo	htmlspecialchars($adr2);	?>" size="30"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Code postal</div>
												<div class="content_form"><input id="LEVEN_zip" name="LEVEN_zip" type="text" value="<?php	echo	htmlspecialchars($zip);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Ville</div>
												<div class="content_form"><input id="LEVEN_ville" name="LEVEN_ville" type="text" value="<?php	echo	htmlspecialchars($ville);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Pays</div>
												<div class="content_form">
														<select id="FK_PAYS_id" name="FK_PAYS_id" class="form_R">
																<?php	while	($row_RS2	=	mysql_fetch_assoc($RS2))	{	?>
																		<option value="<?php	echo	$row_RS2['PAYS_id']	?>"<?php	if	(!(strcmp($row_RS2['PAYS_id'],	$idPays)))	{	echo	"SELECTED";	}	?>>
																				<?php	echo	$row_RS2['PAYS_nom_fr']	?></option>
																<?php	}	?>
														</select>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Latitude et Longitude</div>
												<div class="content_form">
														<input id="address" type="textbox" value="<?php	echo	htmlspecialchars($lieuEvent)	?>" size="50">
														<input type="button" value="Trouver" onclick="codeAddress()">
														<div class="note"><a href="javascript:updateStrAdresse()">-> Rechargez l'adresse</a></div>
														<div style="margin-top: 10px; border:1px solid #CCCCCC" >
																<div id="map_canvas" style="height: 400px;" ></div>
														</div>
														<div class="note">Ajustez le marqueur sur la carte pour définir exactement la latitude et longitude du lieu de l'événement.</div>
														<div style="margin: 10px 0">
																Latitude : <input id="LEVEN_latitude" name="LEVEN_latitude" type="text" value="<?php	echo	($latitude);	?>" size="10" READONLY />
																Longitude : <input  id="LEVEN_longitude" name="LEVEN_longitude" type="text" value="<?php	echo	($longitude);	?>" size="10" READONLY />
																Zoom : <input  id="LEVEN_zoom" name="LEVEN_zoom" type="text" value="<?php	echo	($zoom);	?>" size="10" READONLY />
														</div>
												</div>
										</div>
										<div class="form_submit">
												<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
												<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
												<input name="LEVEN_id" type="hidden" id="id" value="<?php	echo	$id;	?>">
												<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
										</div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
