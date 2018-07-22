<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/GestionTableau.php');
require_once('../../librairie/php/code_adn/CropTexte.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'TYTAR';
$nbreCol	=	7;
$chemin	=	't_typetarif_tytar';

$autoriseAdd	=	true;
$autoriseFiltre	=	false;
$autoriseExport	=	false;
$autoriseMaj	=	true;
$autoriseComment	=	false;
$autoriseDelete	=	true;

if	($autoriseMaj)	{
			$nbreCol+=	2;
}
if	($autoriseDelete)	{
			$nbreCol++;
}
if	($autoriseComment)	{
			$nbreCol++;
}
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
// création la requéte
include("_requete.php");
$query	.=	"ORDER BY TYTAR_ordre ";

// ajout des limites à la requéte
$maxRows	=	$_COOKIE['nbre_ligne'];
list($queryString,
								$query_RS_Limit,
								$totalRows,
								$currentUrl,
								$pageNum,
								$totalPages,
								$startRow)	=	adn_limiteAffichage($maxRows,	$query);
$RS	=	mysql_query($query_RS_Limit,	$connexion)	or	die(mysql_error());
$NbreRows_RS	=	mysql_num_rows($RS);

// Gestion des variables d'affichages
$str_Description	=	"";


/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L2']['nom']	.	"  - Résultat de la recherche";

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
						<script type="text/javascript" src="../../librairie/js/jquery/external/jquery.cookie.js"></script>
						<script type="text/javascript" src="../../librairie/js/code_adn/ns_util.js"></script>
						<script type="text/javascript" src="../_fonction/ns_back.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript" src="../_aide.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {

												// Affichage des descriptions
												NS_BACK.initBoxDescriptions(-6, -6, true);
												// Sauve le nombre de ligne affiché sur un tableau dans un cookie
												NS_BACK.saveNbreLigneTableau();
												// Alerte avant de supprimer
												NS_BACK.checkSupprime($('.bt_delete'), 'action.php?pageNum=<?php	echo	$pageNum	?>');
												// Met en surbrillance la ligne du tableau.
												NS_BACK.highlightRow();

									});
						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<ul class="menu_gauche">
												<?php	if	($autoriseAdd)	{	?><li class="boutons"><a href="add.php?id=0&pageNum=<?php	echo	$pageNum	?>">Ajouter un enregistrement <img src="../_media/bo_add.png" width="16" height="16" border="0" align="absmiddle" alt="ajouter"/></a></li><?php	}	?>
												<?php	if	($autoriseFiltre)	{	?><li class="boutons"><a href="search.php?id=0">Filtrer les résultats <img src="../_media/bo_filter.png" width="16" height="16" border="0" align="absmiddle" alt="filtrer"/></a></li><?php	}	?>
												<?php	if	($autoriseFiltre)	{	?><li class="boutons"><a href="?clean=1" class="js_filtre">Annuler les filtres actifs (<?php	echo	$infoFiltre;	?>) <img src="../_media/bo_nofiltre.png" width="16" height="16" border="0" align="absmiddle" alt="annuler les filtres"/></a></li><?php	}	?>
									</ul>
									<?php	if	($totalRows	==	0)	{	?>
												<div class="ui-state-error ui-corner-all uiphil-msg">
															<?php	if	(isset($infoFiltre)	&&	($infoFiltre	==	'0'))	{	?>
																		<p>Cette table ne contient actuellement aucun enregistrement.</p>
															<?php	}	else	{	?>
																		<p>Aucun résultat n'a été trouvé correspondant à la requête.</p>
																		<p class="SubmitMsg"><a href="search.php">Effectuer une autre recherche</a></p>
															<?php	}	?>
												</div>
									<?php	}	else	{	?>
												<table class="table_result" cellspacing="0">
															<tr>
																		<td colspan="<?php	echo	$nbreCol	?>" nowrap class="navig">
																					<?php	echo	adn_navigationTableau($currentUrl,	$queryString,	$pageNum,	$totalPages,	$startRow,	$maxRows,	$totalRows,	TRUE);	?>
																					<?php	if	($autoriseExport)	{	?><a href="excel.php?com=0"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a> <?php	if	($autoriseComment)	{	?><a href="../../../B/typetarif/excel.php?com=1"> <img src="../_media/ic_excel_com.gif" width="46" height="28" border="0" align="absmiddle" alt="exporter avec commentaires"/></a><?php	}	}	?>
																		</td>
															</tr>

															<tr>
																		<?php	if	($autoriseMaj)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseComment)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<th><i>Ordre</i> <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
																		<th nowrap>Nom français</th>
																		<th nowrap>Nom anglais</th>
																		<th>Ratio</th>
																		<th>Visible <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
																		<th nowrap>Description FR</th>
																		<th nowrap>Description EN</th>
																		<?php	if	($autoriseDelete)	{	?><th>&nbsp;</th><?php	}	?>
															</tr>

															<?php	if	($NbreRows_RS	>	0)	{	?>
																		<?php	while	($row	=	mysql_fetch_object($RS))	{	?>
																					<tr class="to_highlight" valign="top">
																								<td align="center"><?php	if	($autoriseMaj	&&	$row->TYTAR_id	>	1)	{	?><a href="add.php?id=<?php	echo	$row->TYTAR_id	?>&pageNum=<?php	echo	$pageNum	?>"><img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/></a><?php	}	?></td>
																								<?php	if	($autoriseComment)	{	?> <td align="center" bgcolor="#EEEEEE">&nbsp;
																														<div class="js_commentaire pointer" id="<?php	echo	$row->FK_CMTTYTAR_id	?>" idfiche="<?php	echo	$row->TYTAR_id	?>">
																																	<?php	if	(is_null($row->FK_CMTTYTAR_id))	{	?>
																																				<a href="../commentaire/add.php?table=TYTAR&idfiche=<?php	echo	$row->TYTAR_id	?>&pageNum=<?php	echo	$pageNum	?>">[+]</a>
																																	<?php	}	else	{	?>
																																				<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>
																																	<?php	}	?>
																														</div>
																											</td>
																								<?php	}	?>
																								<td class="note"><i><?php	echo	$row->TYTAR_ordre;	?></i></td>
																								<td nowrap><?php	echo	$row->TYTAR_nom_fr;	?></td>
																								<td nowrap><?php	echo	$row->TYTAR_nom_en;	?></td>
																								<td nowrap><?php	echo	$row->TYTAR_ratio;	?> %</td>
																								<td><?php	if	($row->TYTAR_visible)	{	?><img src="../_media/bo_visible.png" width="16" height="16" border="0" alt="visible"/><?php	}	else	{	?><img src="../_media/bo_invisible.png" width="16" height="16" border="0" alt="invisible"/><?php	}	?></td>
																								<td>
																											<?php
																											if	(!is_null($row->TYTAR_description_fr))	{
																														$str_Description	.=	'<div id="fr_'	.	$row->TYTAR_id	.	'" class="invisible">'	.	nl2br($row->TYTAR_description_fr)	.	'</div>';
																														echo	('<div class="js_description pointer" id="fr_'	.	$row->TYTAR_id	.	'">'	.	adn_cropTexte($row->TYTAR_description_fr,	10)	.	'</div>');
																											}
																											?>
																								</td>
																								<td>
																											<?php
																											if	(!is_null($row->TYTAR_description_en))	{
																														$str_Description	.=	'<div id="en_'	.	$row->TYTAR_id	.	'" class="invisible">'	.	nl2br($row->TYTAR_description_en)	.	'</div>';
																														echo	('<div class="js_description pointer" id="en_'	.	$row->TYTAR_id	.	'">'	.	adn_cropTexte($row->TYTAR_description_en,	10)	.	'</div>');
																											}
																											?>
																								</td>
																								<td align="center"><?php	if	($autoriseDelete	&&	$row->TYTAR_id	>	1)	{	?><img class="bt_delete pointer" src="../_media/bo_delete.png" id_todelete="<?php	echo	$row->TYTAR_id;	?>" width="16" height="16" border="0"alt=""/><?php	}	?></td>
																					</tr>
																		<?php	}	}	?>

															<tr>
																		<td colspan="<?php	echo	$nbreCol	?>" class="navig">
																					<?php	echo	adn_navigationTableau($currentUrl,	$queryString,	$pageNum,	$totalPages,	$startRow,	$maxRows,	$totalRows,	FALSE);	?>
																		</td>
															</tr>
												</table>
												<div id="help_icon" class="bt_help pointer"><img src="../_media/bo_help_32.png"/></div>
												<div id="help" title='<img src="../_media/bo_help_32.png" align="absmiddle"/> aide'>
															<p>Ordre:  <span class="note">Ordre d'affichage de la liste sur le site.</span></p>
															<p>Visible: <span class="note">Si oui, ce type de tarif sera visible et séléctionnable pour l'utilisateur. Pour désactiver un type de tarif déjà créé, passez-le en invisible.</span></p>
												</div>
									<?php	}	?>
									<?php	if	($_SESSION['afficheSession']	==	2)	{	echo	('<p class="requete"><b>Requête : </b>'	.	$query	.	'</p>');	}	?>
									<?php	if	(isSet($messageDebug))	{	echo	$messageDebug;	}	?>
									<div id="box_description" class="box_description"><?php	echo	$str_Description;	?></div>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
