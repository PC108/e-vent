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
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// récupération de la variable GET $id
$id	=	$_GET["id"];
if	($id	==	0)	{
// Mode ADD
			$Action	=	"add";
			$Submit	=	"Ajouter";
			$nom	=	"";
			$langue	=	"";
			$type	=	"";
}	else	{
// Mode MAJ
			$Action	=	"maj";
			$Submit	=	"Modifier";
// création du recordset
			$query	=	listeDocuments($connexion);
			$query	.=	" WHERE DOCEVEN_id=$id ";
			$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
			$row	=	mysql_fetch_object($RS);
			$nom	=	$row->DOCEVEN_nom;
			$langue	=	$row->DOCEVEN_langue;
			$type	=	$row->DOCEVEN_type;
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L6']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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
						<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												$("#form_docevent").validate({
															rules: {
																		'DOCEVEN_nom': {
																					required: true
																		}
															}
												});

									});

						</script>
						<!-- CSS -->
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<form id="form_docevent" action="action.php" method="post">
												<div class="BoxSearch">
															<h2>Document associé</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Nom</div>
																		<div class="content_form"><input id="DOCEVEN_nom" name="DOCEVEN_nom" type="text" value="<?php	echo	htmlspecialchars($nom);	?>" size="40" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Langue du doc</div>
																		<div class="content_form">
																					<select  id="DOCEVEN_langue" name="DOCEVEN_langue" class="form_R">
																								<option value="FR" <?php	if	(!(strcmp($langue, "FR")))	{	echo	"SELECTED";	}	?>>en français (FR)</option>
																								<option value="EN" <?php	if	(!(strcmp($langue, "EN")))	{	echo	"SELECTED";	}	?>>en anglais (EN)</option>
																					</select>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Type de fichier</div>
																		<div class="content_form"><img src="../../F/_media/GEN/ic_<?php	echo	$type;	?>.png"> <?php	echo $type;	?></div>
															</div>

															<div class="form_submit">
																		<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
																		<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
																		<input name="DOCEVEN_id" type="hidden" id="id" value="<?php	echo	$id;	?>">
																		<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
															</div>
												</div>
									</form>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
