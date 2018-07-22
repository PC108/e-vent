<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

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
		$ordre	=	999;
		$nomFr	=	"";
		$nomEn	=	"";
		$montant	=	"";
		$capacite	=	"";
		$descriptionFr	=	null;
		$descriptionEn	=	null;
}	else	{
// Mode MAJ
		$Action	=	"maj";
		$Submit	=	"Modifier";
// création du recordset
		include("_requete.php");
		$query	.=	"WHERE TYHEB_id=$id ";
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
		$row	=	mysql_fetch_object($RS);
		$ordre	=	$row->TYHEB_ordre;
		$nomFr	=	$row->TYHEB_nom_fr;
		$nomEn	=	$row->TYHEB_nom_en;
		$montant	=	$row->TYHEB_montant_defaut;
		$capacite	=	$row->TYHEB_capacite_defaut;
		$descriptionFr	=	$row->TYHEB_description_fr;
		$descriptionEn	=	$row->TYHEB_description_en;
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L3']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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

								$("#form_typeheber").validate({
										rules: {
												'TYHEB_ordre': {
														required: true,
														number: true,
														min: 1
												},
												'TYHEB_nom_fr': {
														required: true,
														rangelength: [2, 100]
												},
												'TYHEB_nom_en': {
														required: true,
														rangelength: [2, 100]
												},
												'TYHEB_montant_defaut': {
														required: true,
														number: true,
														min: 0
												},
												'TYHEB_capacite_defaut': {
														required: true,
														number: true,
														min: 0
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
						<form id="form_typeheber" action="action.php" method="post">
								<div class="BoxSearch">
										<h2>Type d'hébergement</h2>
										<div class="form_hr">
												<div class="label_form label_R">Ordre</div>
												<div class="content_form"><input id="TYHEB_ordre" name="TYHEB_ordre" type="text" value="<?php	echo	$ordre;	?>" size="10" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Nom français</div>
												<div class="content_form"><input id="TYHEB_nom_fr" name="TYHEB_nom_fr" type="text" value="<?php	echo	htmlspecialchars($nomFr);	?>" size="40" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Nom anglais</div>
												<div class="content_form"><input  id="TYHEB_nom_en" name="TYHEB_nom_en" type="text" value="<?php	echo	htmlspecialchars($nomEn);	?>" size="40" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Montant d'une place par défaut</div>
												<div class="content_form"><input id="TYHEB_montant_defaut" name="TYHEB_montant_defaut" type="text" value="<?php	echo	($montant);	?>" size="10" class="form_R" AUTOCOMPLETE=OFF></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Capacité totale de places par défaut</div>
												<div class="content_form"><input  id="TYHEB_capacite_defaut" name="TYHEB_capacite_defaut" type="text" value="<?php	echo	($capacite);	?>" size="10" class="form_R" AUTOCOMPLETE=OFF></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Description en français</div>
												<div class="content_form"><textarea id="TYHEB_description_fr" name="TYHEB_description_fr" cols="50" rows="10"><?php	echo	htmlspecialchars($descriptionFr);	?></textarea></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Desciption en anglais</div>
												<div class="content_form"><textarea id="TYHEB_description_en" name="TYHEB_description_en" cols="50" rows="10"><?php	echo	htmlspecialchars($descriptionEn);	?></textarea></div>
										</div>

										<div class="form_submit">
												<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
												<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
												<input name="TYHEB_id" type="hidden" id="id" value="<?php	echo	$id;	?>">
												<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
										</div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
