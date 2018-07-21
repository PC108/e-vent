<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../adherent/_requete.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// Liste des adherents pour l'auto-complete
$query4	=	listeAdherentsQuery();
$RS4	=	mysql_query($query4,	$connexion)	or	die(mysql_error());
$liste_acheteurs	=	"[";
while	($row_RS4	=	mysql_fetch_object($RS4))	{
			$liste_acheteurs	.=	"{label:\""	.	$row_RS4->ADH_nom	.	" "	.	$row_RS4->ADH_prenom	.	"\",value:\""	.	$row_RS4->ADH_identifiant	.	"\"},";
}
$liste_acheteurs	=	rtrim($liste_acheteurs,	",")	.	"]";

/*	* ******************************************************** */
/*       Variables d'affichages de messages                */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L6']['nom'];

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
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.core.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.position.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.widget.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.dialog.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.autocomplete.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/external/jquery.cookie.js"></script>
						<script type="text/javascript" src="../../librairie/js/code_adn/ns_util.js"></script>
						<script type="text/javascript" src="../_fonction/ns_back.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript" src="../_aide.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {

												$("#adherent_keep").autocomplete({
															source: <?php	echo	$liste_acheteurs;	?>,
															select: function(event, ui) {
																		NS_BACK.showInfoAdherent(ui.item.value, "keep");
															}
												});
												$("#adherent_merge").autocomplete({
															source: <?php	echo	$liste_acheteurs;	?>,
															select: function(event, ui) {
																		NS_BACK.showInfoAdherent(ui.item.value, "merge");
															}
												});


									});
						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
						<style type="text/css">
									.table_merge {width: 100%}
									.table_merge td {
												width: 50%;
												padding: 10px;
												vertical-align: top;
									}
									.input_merge {
												font-size: 17px;
									}
						</style>
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<table class="table_merge">
												<tr>
															<td>
																		<h2>Adhérent à garder <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></h2>
																		<div>Saisissez le début du nom ou du prénom ou l'identifiant de l'adhérent <b>à garder.</b></div>

															</td>
															<td>
																		<h2>Adhérent à fusionner <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></h2>
																		<div>Saisissez le début du nom ou du prénom ou l'identifiant de l'adhérent <b>à fusionner.</b></div>

															</td>
												</tr>
												<form action="fusion.php" method="POST">
															<tr>
																		<td>
																					<input class="input_merge form_R" name="adherent_keep" id="adherent_keep" type="text"  value="">
																					<div id="info_keep" class="invisible">
																								<?php	include("_fiche.php");	?>
																					</div>
																		</td>
																		<td>
																					<input class="input_merge form_R" name="adherent_merge" id="adherent_merge" type="text"  value="">
																																													<!--	Le formulaire englobe tout le <tr>-->
																					<input type="submit" name="Submit" value="Fusionner à gauche" class="submit">
																					<div id="info_merge" class="invisible">
																								<?php	include("_fiche.php");	?>
																					</div>
																		</td>
															</tr>
												</form>
									</table>
									<div id="help_icon" class="bt_help pointer"><img src="../_media/bo_help_32.png"/></div>
									<div id="help" title='<img src="../_media/bo_help_32.png" align="absmiddle"/> aide'>
												<p>Adhérent à garder : <span class="note">Chaque information déjà présente pour cet adhérent sera préservée, sauf <u>commandes</u> et <u>compétences</u> qui seront additionnées avec celles de l'adhérent à fusionner.</span></p>
												<p>Adhérent à fusionner : <span class="note">Chaque information de cet adhérent sera transférée vers l'adhérent à garder sauf si une information est déjà présente. Pour <u>commandes</u> et <u>compétences</u>, les informations sont additionnées à celle déjà présentes.</span></p>
												<p>Commande :  <span class="note">Ne sont pris en compte ici que les commandes directes créés par l'adhérent depuis son espace privé. <br />Les commandes où l'adhérent pourrait apparaitre comme ami ne sont pas décomptées.</span></p>
									</div>
						</div>
						<?php	include("../_footer.php");	?>
			</body>
</html>
