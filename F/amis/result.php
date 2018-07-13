<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/MyRedirection.php');

$title	=	_("Mes relations");

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../evenement/index.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$idAdh	=	$_SESSION['info_adherent']['id_adh'];

// Amis de l'adhérent
$query1	=	"
		SELECT *
		FROM t_amis_amis
		LEFT JOIN t_adherent_adh ON FK_ADHAMI_id = ADH_id
		LEFT JOIN t_pays_pays ON FK_PAYS_id = PAYS_id
		WHERE FK_ADH_id = $idAdh
		ORDER BY ADH_nom, ADH_prenom ASC";
$RS1	=	mysql_query($query1,	$connexion)	or	die(mysql_error());
$nbreRows1	=	mysql_num_rows($RS1);

// Groupes rejoint par l'adhérent
$query2	=	"
		SELECT *
		FROM t_amis_amis
		LEFT JOIN t_adherent_adh ON FK_ADH_id = ADH_id
		LEFT JOIN t_pays_pays ON FK_PAYS_id = PAYS_id
		WHERE FK_ADHAMI_id = $idAdh
		ORDER BY ADH_nom, ADH_prenom ASC";
$RS2	=	mysql_query($query2,	$connexion)	or	die(mysql_error());
$nbreRows2	=	mysql_num_rows($RS2);

$str_Description	=	"";	// Fonctionne pour les 2 requêtes

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
						<script type="text/javascript" src="../../B/_fonction/ns_back.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												//Navigation
												$('#navigation #amis').addClass('ui-state-active')
												$('#navigation #amis').button( "option", "disabled", true );

												// Affichage des descriptions
												NS_BACK.initBoxDescriptions(0, 35, false);

												/////////////////////////////////////////////////////////
												// BOUTON "Ajouter une relation"
												/////////////////////////////////////////////////////////
												// Initialisation du bouton
												$( "#bt_add_link" ).button({
															icons: {
																		primary:'ui-icon-circle-plus'
															}
												});
												// Gestion du clic
												$('#bt_add_link').click(function() {window.location='search.php';});

												/////////////////////////////////////////////////////////
												// BOUTON "Supprimer"
												/////////////////////////////////////////////////////////
												NS_DIALOG.initConfirmDialog($('#dialog_deleteLink'), ['<?php	echo	_("supprimer");	?>','<?php	echo	_("annuler");	?>']);
												// + Boite de dialogue "Confirmer"
												$('.bt_delete_link').click(function() {
															IdAdh = $(this).attr('id');
															NS_DIALOG.confirmChemin = 'action.php?idAdhLien='+IdAdh+'&action=del';
															NS_DIALOG.openDialog($('#dialog_deleteLink'));
												});

												/////////////////////////////////////////////////////////
												// BOUTON "Désactiver la relation"
												/////////////////////////////////////////////////////////
												NS_DIALOG.initConfirmDialog($('#dialog_desactiveLink'), ['<?php	echo	_("désactiver");	?>','<?php	echo	_("annuler");	?>']);
												// Click bouton "se désactiver (d'un groupe)" avec boite de dialogue "Confirmer"
												$('.bt_desactiveLink').click(function(){
															IdAdh = $(this).attr('id');
															NS_DIALOG.confirmChemin = 'action.php?idAdhLien='+IdAdh+'&action=unlink';
															NS_DIALOG.openDialog($('#dialog_desactiveLink'));
												});
									});
						</script>
						<!-- CSS -->
						<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
						<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
						<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/cmd_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="global">
									<?php	include('../_header.php');	?>
									<?php	include('../amis/_amis.php');	// A placer avant sidebar.php ?>
									<?php	include('../_sidebar.php');	?>
									<div id="content" class="corner20-all">
												<h1><?php	echo	$title;	?></h1>
												<div class="info_print corner10-all">
															<div><img src="../_media/GEN/ic_info.png" align="absmiddle" alt="info"/> <span class="commentaire"><?php	echo	_("Les relations vous permettent de vous inscrire à plusieurs à un événement ou de payer une cotisation de groupe.");	?></span></div>
												</div>
												<div class="titre_amis corner20-top" style="border-bottom:0;"><?php	echo	_("Les relations de")	.	" "	.	$_SESSION['info_adherent']['prenom_adh']	.	" "	.	$_SESSION['info_adherent']['nom_adh']	.	" "	.	_("sont :");	?></div>
												<div class="formulaire corner20-bottom">
															<?php	if	($nbreRows1	>	0)	{	?>
																		<table>
																					<?php
																					while	($row1	=	mysql_fetch_array($RS1))	{
																								$str_Description	.=	'<div id="info_q1_'	.	$row1['FK_ADHAMI_id']	.	'" class="invisible">';
																								$str_Description	.=	'<a href="mailto:'	.	$row1['ADH_email']	.	'">'	.	$row1['ADH_email']	.	'</a><br /><i>';
																								$str_Description	.=	$row1['ADH_adresse1']	.	'<br />';
																								if	(!is_null($row1['ADH_adresse2']))	{	$str_Description	.=	$row1['ADH_adresse2']	.	'<br />';	}
																								$str_Description	.=	$row1['ADH_zip']	.	" "	.	$row1['ADH_ville']	.	'<br />';
																								$str_Description	.=	$row1['PAYS_nom_'	.	$langue];
																								$str_Description	.=	'</i></div>';
																								?>
																								<tr>
																											<td nowrap><div class="bloc_relations cellule corner10-all"><?php	echo	$row1['ADH_prenom'];	?> <?php	echo	$row1['ADH_nom'];	?></div></td>
																											<td>
																														<div class="js_description cellule cell_txt pointer corner10-all" id="info_q1_<?php	echo	$row1['FK_ADHAMI_id']	?>"> <img src="../_media/GEN/ic_info.png" alt="info"></img></div>
																											</td>
																											<td>
																														<div class="bt_delete_link cellule cell_txt pointer corner10-all" id="<?php	echo	$row1['ADH_id'];	?>">
																																	<img src="../_media/GEN/ic_delete.png" alt="info"></img>
																														</div>
																											</td>
																											<td>
																														<?php	if	($row1['ADHAMI_actif']	==	0)	{	?>
																																	<div class="note"><?php	echo	_("Cette relation a été désactivée.")	?></div>
																														<?php	}	?>
																											</td>
																								</tr>
																					<?php	}	?>
																		</table>
															<?php	}	else	{	?>
																		<p>
																					<?php	echo	_("Vous n'avez pas encore créé de relations.");	?><br />
																		</p>
															<?php	}	?>
															<div id="bt_add_link" style="margin-top: 20px;"><?php	echo	_("ajouter une relation");	?></div>
												</div>
												<div class="titre_amis corner20-top" style="border-bottom:0;"><?php	echo	$_SESSION['info_adherent']['prenom_adh']	.	" "	.	$_SESSION['info_adherent']['nom_adh']	.	" "	.	_("a été ajouté dans les relations des personnes suivantes :");	?></div>
												<div class="formulaire corner20-bottom">
															<?php	if	($nbreRows2)	{	?>
																		<table>
																					<?php
																					while	($row2	=	mysql_fetch_array($RS2))	{
																								$str_Description	.=	'<div id="info_q2_'	.	$row2['FK_ADH_id']	.	'" class="invisible">';
																								$str_Description	.=	'<a href="mailto:'	.	$row2['ADH_email']	.	'">'	.	$row2['ADH_email']	.	'</a><br /><i>';
																								$str_Description	.=	$row2['ADH_adresse1']	.	'<br />';
																								if	(!is_null($row2['ADH_adresse2']))	{	$str_Description	.=	$row2['ADH_adresse2']	.	'<br />';	}
																								$str_Description	.=	$row2['ADH_zip']	.	" "	.	$row2['ADH_ville']	.	'<br />';
																								$str_Description	.=	$row2['PAYS_nom_'	.	$langue];
																								$str_Description	.=	'</i></div>';
																								?>
																								<tr>
																											<td><div class="cellule cell_txt corner10-all"><?php	echo	$row2['ADH_prenom'];	?> <?php	echo	$row2['ADH_nom'];	?></div></td>
																											<td>
																														<div class="js_description cellule cell_txt pointer corner10-all" id="info_q2_<?php	echo	$row2['FK_ADH_id']	?>"> <img src="../_media/GEN/ic_info.png" alt="info"></img></div>
																											</td>
																											<td>
																														<?php	if	($row2['ADHAMI_actif']	==	1)	{	?>
																																	<div class="cellule bt_desactiveLink" id="<?php	echo	$row2['ADH_id']	?>">
																																				<input type="button" value="<?php	echo	_("désactiver la relation");	?>" class="bt_delete corner10-all"/>
																																	</div>
																														<?php	}	else	{	?>
																																	<div class="note"><?php	echo	_("Vous avez désactivé cette relation.")	?></div>
																														<?php	}	?>
																											</td>
																								</tr>
																					<?php	}	?>
																		</table>
															<?php	}	else	{	?>
																		<p><?php	echo	_("Vous n'avez pas encore été ajouté à une relation.");	?></p>
															<?php	}	?>
												</div>
												<div id="dialog_deleteLink" title="<?php	echo	_("Confirmation");	?>">
															<p><?php	echo	_("Vous allez supprimer cette relation.")	?><br/><?php	echo	_("cette action est irréversible.")	?></p>
												</div>
												<div id="dialog_desactiveLink" title="<?php	echo	_("Confirmation");	?>">
															<p><?php	echo	sprintf(_("Vous allez désactiver la relation initialisée par cette personne. %s Elle ne pourra plus vous associer à ses commandes."),	'<br/>')	?></p>
												</div>
									</div>
									<?php	include('../_footer.php');	?>
						</div>
						<div id="box_popup" class="popup">--</div>
						<div id="box_description" class="popup corner10-all" style="padding-right: 12px;"><?php	echo	$str_Description;	?></div>
			</body>
</html>