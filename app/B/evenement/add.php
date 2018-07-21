<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('_requete.php');
require_once('../upload_doc/_requete.php');

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

$id	=	$_GET["id"];
if	($id	==	0)	{
// Mode ADD
			$Action	=	"add";
			$Submit	=	"Ajouter";
			$nomFr	=	"";
			$nomEn	=	"";
			$prive	=	0;
			$pleintarif	=	0;
			$descrFr	=	"";
			$descrEn	=	"";
			$image	=	"00defaut.jpg";
			$lien	=	"";
}	else	{
// Mode MAJ
			$Action	=	"maj";
			$Submit	=	"Modifier";
// création du recordset
			$query	=	mainQueryEvent($connexion);
			$query	.=	" WHERE EVEN_id=$id ";
			$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
			$row	=	mysql_fetch_array($RS);
			$nomFr	=	$row['EVEN_nom_fr'];
			$nomEn	=	$row['EVEN_nom_en'];
			$prive	=	$row['EVEN_prive'];
			$pleintarif	=	$row['EVEN_pleintarif'];
			$descrFr	=	$row['EVEN_descriptif_fr'];
			$descrEn	=	$row['EVEN_descriptif_en'];
			$image	=	$row['EVEN_image'];
			$lien	=	$row['EVEN_lien'];
}

// Liste de toutes les images uploadés qui sont valides et complètes
$allFiles	=	scandir("../../upload/img");
$listeImgOk	=	array();
foreach	($allFiles	as	$value)	{
			$nomGenerique	=	str_replace(array("crop_",	"ic_"),	"",	$value);
			if	($nomGenerique	!=	"00defaut.jpg"	&&	in_array("crop_"	.	$nomGenerique,	$allFiles)	&&	in_array("ic_"	.	$nomGenerique,	$allFiles)	&&	!in_array($nomGenerique,	$listeImgOk))	{
						array_push($listeImgOk,	$nomGenerique);
			}
}

// Liste de tout les docs uploadés pour les evenements
$query_RS2	=	listeDocuments();
$query_RS2	.=	"ORDER BY DOCEVEN_langue, DOCEVEN_nom";
$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());
$nbreRows_RS2	=	mysql_num_rows($RS2);
// Liste des docs associés à cet événement (pour les cases à cocher.)
$query_RS3	=	DocsForThisEvent($id);
$RS3	=	mysql_query($query_RS3,	$connexion)	or	die(mysql_error());
$nbreRows_RS3	=	mysql_num_rows($RS3);
if	($nbreRows_RS3	===	0)	{
			$listeDocAssocie	=	array();
}	else	{
			while	($row	=	mysql_fetch_object($RS3))	{
						$listeDocAssocie[]	=	$row->TJ_DOCEVEN_id;
			}
}



/*	* ******************************************************** */
/*       Titre                */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L1']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
			<head>
						<title><?php	echo	$titre;	?></title>
						<meta NAME="author" CONTENT="Atelier Du Net" />
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<link rel="icon" type="image/png" href="../_media/favicon.png" />
						<!-- JS -->
						<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												$("#form_evenement").validate({
															rules: {
																		'EVEN_nom_fr': {
																					required: true,
																					rangelength: [2, 150]
																		},
																		'EVEN_nom_en': {
																					required: true,
																					rangelength: [2, 150]
																		},
																		'EVEN_descriptif_fr': {
																					required: true,
																					minlength: 4
																		},
																		'EVEN_descriptif_en': {
																					required: true,
																					minlength: 4
																		},
																		'EVEN_lien': {
																					url: true
																		}
															}
												});
									});
						</script>
						<!-- CSS -->
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css"/>
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<form id="form_evenement" action="action.php" method="post">
												<div class="BoxSearch">
															<h2>Informations générales</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Nom en français</div>
																		<div class="content_form"><input id="EVEN_nom_fr" name="EVEN_nom_fr" type="text" value="<?php	echo	htmlspecialchars($nomFr);	?>" size="40" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Nom en anglais</div>
																		<div class="content_form"><input id="EVEN_nom" name="EVEN_nom_en" type="text" value="<?php	echo	htmlspecialchars($nomEn);	?>" size="40" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form">Privé <img src="../_media/bo_locked.png" width="16" height="16" border="0"alt=""/></div>
																		<div class="content_form">
																					<input type="checkbox" id="EVEN_prive"  name="EVEN_prive" value="" <?php	if	(!(strcmp("1",	$prive)))	{	echo	"CHECKED";	}	?>>
																					<div class="note">Si cochée, l'événement sera uniquement visible aux adhérents ayant un <b>accès aux événements privés.</b></div>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form">Plein tarif</div>
																		<div class="content_form">
																					<input type="checkbox" id="EVEN_pleintarif"  name="EVEN_pleintarif" value="" <?php	if	(!(strcmp("1",	$pleintarif)))	{	echo	"CHECKED";	}	?>>
																					<div class="note">Si cochée, l'événement ne prendra pas en compte les conditions de tarifs réduits des adhérents lors de l'inscription.</b></div>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Descriptif en français</div>
																		<div class="content_form"><textarea id="EVEN_descriptif_fr" name="EVEN_descriptif_fr" cols="50" rows="10" class="form_R"><?php	echo	($descrFr);	?></textarea></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Descriptif en anglais</div>
																		<div class="content_form"><textarea id="EVEN_descriptif_en" name="EVEN_descriptif_en" cols="50" rows="10" class="form_R"><?php	echo	($descrEn);	?></textarea></div>
															</div>
															<!--IMAGES-->
															<div class="form_hr">
																		<div class="label_form">Images disponibles</div>
																		<div class="content_form">
																					<div class="radio_img">
																								<input type="radio"  name="EVEN_image" value="00defaut.jpg" <?php	if	(!(strcmp("00defaut.jpg",	$image)))	{	echo	"CHECKED";	}	?>>
																								<img src="../../upload/img/ic_00defaut.jpg" />
																					</div>
																					<?php	foreach	($listeImgOk	as	$value)	{	?>
																								<div class="radio_img">
																											<input type="radio"  name="EVEN_image" value="<?php	echo	$value;	?>" <?php	if	(!(strcmp($value,	$image)))	{	echo	"CHECKED";	}	?>>
																											<img src="../../upload/img/ic_<?php	echo	$value;	?>" />
																								</div>
																					<?php	}	?>
																					<div class="note">Sélectionnez une image pour l'associer à l'événement.</a></div>
																					<div class="note">Pour ajouter une nouvelle image à la liste, <a href="../upload_img/result.php">cliquez ici.</a></div>
																		</div>
															</div>
															<!--LIEN URL-->
															<div class="form_hr">
																		<div class="label_form">Lien url</div>
																		<div class="content_form">
																					<input id="EVEN_lien" name="EVEN_lien" type="text" value="<?php	echo	htmlspecialchars($lien);	?>" size="40">
																					<div class="note">Le lien doit être du type http://www.mondomaine.com</div>
																		</div>
															</div>
															<!--DOC PDF-->
															<div class="form_hr">
																		<div class="label_form">Documents PDF disponibles</div>
																		<div class="content_form">
																					<?php	if	($nbreRows_RS2	===	0)	{	?>
																								<span>Aucun document n'a été uploadé pour le moment</span>
																					<?php	}	else	{	?>
																								<table width="100%">
																											<tr>
																														<td>&nbsp;</td>
																														<td class="note">Nom</td>
																														<td class="note">Langue du doc</td>
																														<td class="note">Type de fichier</td>
																											</tr>
																											<?php	while	($row_RS2	=	mysql_fetch_object($RS2))	{	?>
																														<tr>
																																	<td><input type="checkbox"  name="document[]" value="<?php	echo	$row_RS2->DOCEVEN_id	?>" <?php	if	(in_array($row_RS2->DOCEVEN_id,	$listeDocAssocie))	{	echo	"CHECKED";	}	?>></td>
																																	<td><?php	echo	$row_RS2->DOCEVEN_nom	?></td>
																																	<td><?php	echo	$row_RS2->DOCEVEN_langue	?></td>
																																	<td nowrap class="note"><img src="../../F/_media/GEN/ic_<?php	echo	$row_RS2->DOCEVEN_type;	?>.png"> <?php	echo	$row_RS2->DOCEVEN_type;	?></td>
																														</tr>
																											<?php	}	?>
																								</table>
																					<?php	}	?>
																					<div class="note">Sélectionnez un document pour l'associer à l'événement.</a></div>
																					<div class="note">Pour ajouter un nouveau document à la liste, <a href="../upload_doc/result.php">cliquez ici.</a></div>
																		</div>
															</div>
															<div class="form_submit">
																		<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
																		<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
																		<input name="EVEN_id" type="hidden" id="EVEN_id" value="<?php	echo	$id;	?>">
																		<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
															</div>
												</div>
									</form>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
