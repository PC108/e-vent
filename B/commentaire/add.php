<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */

if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher',	'cmd',	'event')))	{
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
if	(isSet($_GET["idcom"]))
			$idCommentaire	=	$_GET["idcom"];
else
			$idCommentaire	=	0;

$tableId	=	$_GET["table"];
$idFiche	=	$_GET["idfiche"];

// Tables ou les commentaires sont activés
switch	($tableId)	{
			case	"ACS":
						$repertoire	=	"acces";
						break;
			case	"ADH":
						$repertoire	=	"adherent";
						break;
			case	"CMD":
						$repertoire	=	"commande";
						break;
			case	"EVEN":
						$repertoire	=	"evenement";
						break;
}

if	($idCommentaire	==	0)	{
// Mode ADD
			$action	=	"add";
			$submit	=	"Ajouter";
			$commentaire	=	"";
}	else	{
// Mode MAJ
			$action	=	"maj";
			$submit	=	"Modifier";
// création du recordset
			$query_RS	=	"SELECT * FROM t_commentaire_cmt WHERE CMT_id = $idCommentaire";
			$RS	=	mysql_query($query_RS,	$connexion)	or	die(mysql_error());
// récupération at affichage des valeurs
			$row	=	mysql_fetch_object($RS);
			$commentaire	=	$row->CMT_commentaire;
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['NAVIGATION']['L4']['nom']	.	"  - "	.	$submit	.	" un enregistrement";

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
			<head>
						<title><?php	echo	$titre	?></title>
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

												// VALIDATE
												$("#form_commentaire").validate({
															rules: {
																		'CMT_commentaire': {
																					required: true,
																					minlength: 2
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
									<?php	if	($action	==	"maj")	{	?>
			    	    <ul class="menu_gauche">
															<li class="boutons"><a href="action.php?table=<?php	echo	$tableId	?>&idcom=<?php	echo	$idCommentaire;	?>&idfiche=<?php	echo	$idFiche;	?>&pageNum=<?php	echo	$pageNum;	?>">Supprimer le commentaire</a> <img src="../_media/bo_delete.png" width="16" height="16" border="0" align="absmiddle" alt="supprimer"/></li>
			    	    </ul>
									<?php	}	?>
									<form id="form_commentaire" action="action.php" method="post">
												<div class="BoxSearch">
															<h2><?php	echo	$submit	?> un enregistrement</h2>
															<div class="form_hr">
																		<div class="label_form label">en realtion avec</div>
																		<div class="content_form">Table <b><?php	echo	$repertoire;	?></b> n°<b><?php	echo	$idFiche;	?></b></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Commentaire</div>
																		<div class="content_form"><textarea name="CMT_commentaire" id="CMT_commentaire" cols="50" rows="15"><?php	echo	$commentaire;	?></textarea></div>
															</div>
															<div class="form_submit">
																		<input name="action" type="hidden" id="action" value="<?php	echo	$action;	?>">
																		<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
																		<input name="idcom" type="hidden" value="<?php	echo	$idCommentaire;	?>">
																		<input name="table" type="hidden" value="<?php	echo	$tableId;	?>">
																		<input name="idfiche" type="hidden" value="<?php	echo	$idFiche;	?>">
																		<input type="submit" name="submit" value="<?php	echo	$submit	?>" class="submit">
															</div>
												</div>
									</form>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
