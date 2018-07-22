<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher')))	{
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
		$ordre	=	999;
		$nomFr	=	"";
		$nomEn	=	"";
		$ratio	=	"";
		$visible	=	1;
		$descriptionFr	=	"";
		$descriptionEn	=	"";
}	else	{
// Mode MAJ
		$Action	=	"maj";
		$Submit	=	"Modifier";
// création du recordset
		include("_requete.php");
		$query	.=	" WHERE TYTAR_id=$id";
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
		$row	=	mysql_fetch_object($RS);
		$ordre	=	$row->TYTAR_ordre;
		$nomFr	=	$row->TYTAR_nom_fr;
		$nomEn	=	$row->TYTAR_nom_en;
		$ratio	=	$row->TYTAR_ratio;
		$visible	=	$row->TYTAR_visible;
		$descriptionFr	=	$row->TYTAR_description_fr;
		$descriptionEn	=	$row->TYTAR_description_en;
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L2']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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

								$("#form_typetarif").validate({
										rules: {
												'TYTAR_ordre': {
														required: true,
														number: true,
														min: 2
												},
												'TYTAR_nom_fr': {
														required: true,
														minlength: 2
												},
												'TYTAR_nom_en': {
														required: true,
														minlength: 2
												},
												'TYTAR_ratio': {
														required: true,
														range: [0, 100]
												},
												'TYTAR_description_fr': {
														maxlength: 200
												},
												'TYTAR_description_en': {
														maxlength: 200
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
						<form id="form_typetarif" action="action.php" method="post">
								<div class="BoxSearch">
										<h2>Type de tarifs</h2>
										<div class="form_hr">
												<div class="label_form label_R">Ordre</div>
												<div class="content_form"><input id="TYTAR_ordre" name="TYTAR_ordre" type="text" value="<?php	echo	$ordre;	?>" size="10" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Nom en français</div>
												<div class="content_form"><input id="TYTAR_nom_fr" name="TYTAR_nom_fr" type="text" value="<?php	echo	htmlspecialchars($nomFr);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">English name</div>
												<div class="content_form"><input id="TYTAR_nom_en" name="TYTAR_nom_en" type="text" value="<?php	echo	htmlspecialchars($nomEn);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Ratio</div>
												<div class="content_form">
														<input  id="TYTAR_ratio" name="TYTAR_ratio" type="text" value="<?php	echo	($ratio);	?>" size="10" class="form_R"> %
														<div class="note">Entrez un chiffre de 1 à 100.</div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Visible</div>
												<div class="content_form">
														<input type="checkbox" id="TYTAR_visible"  name="TYTAR_visible" value="" <?php	if	(!(strcmp("1",	$visible)))	{	echo	"CHECKED";	}	?>>
														<div class="note">A cocher pour que l'enregistrement soit visible pour les utilisateurs.</div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Description en français</div>
												<div class="content_form"><input id="TYTAR_description_fr" name="TYTAR_description_fr" type="text" value="<?php	echo	htmlspecialchars($descriptionFr);	?>" size="70"></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Description en anglais</div>
												<div class="content_form"><input id="TYTAR_description_en" name="TYTAR_description_en" type="text" value="<?php	echo	htmlspecialchars($descriptionEn);	?>" size="70"></div>
										</div>
										<div class="form_submit">
												<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
												<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
												<input name="TYTAR_id" type="hidden" id="id" value="<?php	echo	$id;	?>">
												<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
										</div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
