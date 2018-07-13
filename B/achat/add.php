<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once("../achat/_requete.php");

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
$id	=	$_GET["idAch"];
if	($id	==	0)	{	// Mode ADD
		$Action	=	"add";
		$Submit	=	"Ajouter";
}	else	{	// Mode MAJ
		$Action	=	"maj";
		$Submit	=	"Modifier";
		// création du recordset

		$query	=	mainQueryAch($_GET["idCmd"],	$connexion);
		$query	.=	"WHERE ACH_id=$id ";
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
		$rowAch	=	mysql_fetch_object($RS);
}

/*	* ******************************************************** */
/*       Titre               */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L5']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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
				<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.core.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.datepicker.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/ui_localisation/jquery.ui.datepicker-fr.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
				<script type="text/javascript" src="../_shared.js"></script>
				<script type="text/javascript">
						$(document).ready(function() {
								$("#form_achat").validate({
										rules: {
												'ACH_remb': {
														required: true,
														number: true,
														min: 0
												}
										}
								});
						});
				</script>
				<!-- CSS -->
				<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
				<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
				<link href="../_css/style_bo.css" rel="stylesheet" type="text/css"/>
		</head>
		<body>
				<?php	include("../_header.php");	?>
				<div id="contenu">
						<form id="form_achat" action="action.php" method="post">
								<div class="BoxSearch">
										<h2>Achat</h2>
										<div class="form_hr">
												<div class="label_form">Prénom & nom bénéficiaire</div>
												<div class="content_form"><?php	echo	$rowAch->ADH_prenom	.	" "	.	$rowAch->ADH_nom;	?></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Type d'achat</div>
												<div class="content_form"><?php	echo	$rowAch->TYACH_nom_fr;	?>	</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Description</div>
												<div class="content_form">
														<?php
														switch	($rowAch->FK_TYACH_id)	{
																case	1:
																case	2:	// Cotisations et dons
																		$html	=	'<td nowrap>'	.	$rowAch->TYCOT_nom_fr	.	' '	.	$rowAch->TYDON_nom_fr;
																		break;
																case	3;	// Jour evenement
																		$infoDates	=	adn_afficheFromDateToDate($rowAch->JREVEN_date_debut,	$rowAch->JREVEN_date_fin,	"DB_fr");
																		$html	=	'<td nowrap>'	.	$rowAch->EVEN_nom_fr	.	' | '	.	$infoDates[0]	.	' | '	.	$rowAch->PAYS_nom_fr;
																		break;
																case	4:	// Options (Restauration et hébergement)
																case	5:
																		$html	=	'<td nowrap>'	.	$rowAch->TYHEB_nom_fr	.	' '	.	$rowAch->TYRES_nom_fr;
																		break;
																default:
																		break;
														}
														echo	$html;
														?>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Montant</div>
												<div class="content_form"><b><?php	echo	$rowAch->ACH_montant;	?> €</b></div>
										</div>
										<?php	if	($configAppli['MENU']['cotisation']	==	"oui")	{	?>
												<div class="form_hr">
														<div class="label_form">Surcoût cotisation</div>
														<div class="content_form"><b><?php	echo	$rowAch->ACH_surcout;	?> €</b></div>
												</div>
										<?php	}	?>
										<div class="form_hr">
												<div class="label_form">Ratio du type de tarif</div>
												<div class="content_form"><?php	echo	$rowAch->ACH_ratio;	?>%	</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Remboursement</div>
												<div class="content_form">
														<input id="ACH_remb" name="ACH_remb" type="text" value="<?php	echo	$rowAch->ACH_remb;	?>" size="10" class="form_R"> €
												</div>
										</div>
										<?php
										if	($rowAch->FK_TYACH_id	==	3	||
																		$rowAch->FK_TYACH_id	==	4	||
																		$rowAch->FK_TYACH_id	==	5)	{
												?>
												<div class="form_hr">
														<div class="label_form">Participe à l'événement</div>
														<div class="content_form">
																<input id="ACH_participe" name="ACH_participe" type="checkbox"
																							<?php	if	($rowAch->ACH_participe	==	true)	{	echo	'checked="checked"';	}	?>class="form_R">
														</div>
												</div>
										<?php	}	?>
	   		    <div class="form_submit">
												<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
												<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
												<input name="ACH_id" type="hidden" id="ACH_id" value="<?php	echo	$id;	?>">
												<input name="idcmd" type="hidden" id="id_cmd" value="<?php	echo	$_GET["idCmd"];	?>">
												<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
	   		    </div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
