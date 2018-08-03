<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'cmd')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

// Vérification si la cotisation est activée
if	($configAppli['MENU']['don']	==	"non")	{
		$_SESSION['message_user']	=	"don_ko";
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
		$visible	=	1;
}	else	{
// Mode MAJ
		$Action	=	"maj";
		$Submit	=	"Modifier";
// création du recordset
		include("_requete.php");
		$query	.=	"WHERE TYDON_id=$id ";
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
		$row	=	mysql_fetch_object($RS);
		$ordre	=	$row->TYDON_ordre;
		$nomFr	=	$row->TYDON_nom_fr;
		$nomEn	=	$row->TYDON_nom_en;
		$visible	=	$row->TYDON_visible;
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L3']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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

								$("#form_typedon").validate({
										rules: {
												'TYDON_ordre': {
														required: true,
														number: true,
														min: 2
												},
												'TYDON_nom_fr': {
														required: true,
														rangelength: [2, 100]
												},
												'TYDON_nom_en': {
														required: true,
														rangelength: [2, 100]
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
						<form id="form_typedon" action="action.php" method="post">
								<div class="BoxSearch">
										<h2>Type de don</h2>
										<div class="form_hr">
												<div class="label_form label_R">Ordre</div>
												<div class="content_form"><input id="TYDON_ordre" name="TYDON_ordre" type="text" value="<?php	echo	$ordre;	?>" size="10" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Nom français</div>
												<div class="content_form"><input id="TYDON_nom_fr" name="TYDON_nom_fr" type="text" value="<?php	echo	htmlspecialchars($nomFr);	?>" size="40" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Nom anglais</div>
												<div class="content_form"><input  id="TYDON_nom_en" name="TYDON_nom_en" type="text" value="<?php	echo	htmlspecialchars($nomEn);	?>" size="40" class="form_R"></div>
										</div>

										<div class="form_hr">
												<div class="label_form label_R">Visible</div>
												<div class="content_form">
														<input type="checkbox" id="TYDON_visible"  name="TYDON_visible" value="" <?php	if	(!(strcmp("1",	$visible)))	{	echo	"CHECKED";	}	?>>
														<div class="note">A cocher pour que l'enregistrement soit visible pour les utilisateurs.</div>
												</div>
										</div>

										<div class="form_submit">
												<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
												<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
												<input name="TYDON_id" type="hidden" id="id" value="<?php	echo	$id;	?>">
												<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
										</div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
