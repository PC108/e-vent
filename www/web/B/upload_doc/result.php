<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/GestionTableau.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('_requete.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'DOCEVEN';
$nbreCol	=	7;
$chemin	=	'upload_doc';

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
// Vérification de l'user
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// création la requéte
$query	=	listeDocuments();
$query	.=	"ORDER BY LOG_date DESC";

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
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L6']['nom']	.	"  - Résultat de la recherche";

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
						<script type="text/javascript" src="../../librairie/js/jquery/external/jquery.cookie.js"></script>
						<script type="text/javascript" src="../../librairie/js/code_adn/ns_util.js"></script>
						<script type="text/javascript" src="../_fonction/ns_back.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">

									$(document).ready(function() {

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
												<?php	if	($autoriseAdd)	{	?><li class="boutons"><a href="upload_doc.php?pageNum=<?php	echo	$pageNum	?>">Ajouter un enregistrement <img src="../_media/bo_add.png" width="16" height="16" border="0" align="absmiddle" alt="ajouter"/></a></li><?php	}	?>
												<?php	if	($autoriseFiltre)	{	?><li class="boutons"><a href="search.php?id=0&pageNum=<?php	echo	$pageNum	?>">Filtrer les résultats <img src="../_media/bo_filter.png" width="16" height="16" border="0" align="absmiddle" alt="filtrer"/></a></li><?php	}	?>
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
																					<?php	if	($autoriseExport)	{	?><a href="excel.php?com=0"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a> <?php	if	($autoriseComment)	{	?><a href="../../../B/competence/excel.php?com=1"> <img src="../_media/ic_excel_com.gif" width="46" height="28" border="0" align="absmiddle" alt="exporter avec commentaires"/></a><?php	}	}	?>
																		</td>
															</tr>
															<tr>
																		<?php	if	($autoriseMaj)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseComment)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<th>&nbsp;</th>
																		<th nowrap>Nom</th>
																		<th nowrap>Langue doc</th>
																		<th nowrap>type</th>
																		<th nowrap>associé à</th>
																		<th nowrap>uploadé le</th>
																		<th nowrap>par</th>
																		<?php	if	($autoriseDelete)	{	?><th width="20">&nbsp;</th><?php	}	?>
															</tr>

															<?php	if	($NbreRows_RS	>	0)	{	?>
																		<?php	while	($row	=	mysql_fetch_object($RS))	{	?>
																					<tr class="to_highlight" valign="top">
																								<?php	if	($autoriseMaj)	{	?><td align="center"><a href="add.php?id=<?php	echo	$row->DOCEVEN_id	?>&pageNum=<?php	echo	$pageNum	?>"><img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/></a></td><?php	}	?>
																								<?php	if	($autoriseComment)	{	?> <td align="center" bgcolor="#EEEEEE">
																														<div class="js_commentaire pointer" id="<?php	echo	$row->FK_CMTDOCEVEN_id	?>" idfiche="<?php	echo	$row->DOCEVEN_id	?>">
																																	<?php	if	(is_null($row->FK_CMTDOCEVEN_id))	{	?><a href="../commentaire/add.php?table=DOCEVEN&idfiche=<?php	echo	$row->DOCEVEN_id	?>&pageNum=<?php	echo	$pageNum	?>">[+]</a>
																																	<?php	}	else	{	?><a href="../commentaire/add.php?table=DOCEVEN&idcom=<?php	echo	$row->FK_CMTDOCEVEN_id	?>&idfiche=<?php	echo	$row->DOCEVEN_id	?>&pageNum=<?php	echo	$pageNum	?>"><img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/></a>
																																	<?php	}	?>
																														</div>
																											</td>
																								<?php	}	?>
																								<?php
																								$chemin	=	"../../upload/doc/"	.	$row->DOCEVEN_file;
																								if	(file_exists($chemin))	{
																											?>
																											<td nowrap><a href="<?php	echo	$chemin	?>" target="blank">voir le doc</a></td>
																								<?php	}	else	{	?>
																											<td nowrap class="note">Doc introuvable !</td>
																								<?php	}	?>
																								<td nowrap><b><?php	echo	$row->DOCEVEN_nom;	?></b></td>
																								<td nowrap><b><?php	echo	$row->DOCEVEN_langue;	?></b></td>
																								<td nowrap class="note"><img src="../../F/_media/GEN/ic_<?php	echo	$row->DOCEVEN_type;	?>.png"> <?php	echo	$row->DOCEVEN_type;	?></td>
																								<td nowrap>
																											<?php
																											// récupère la liste des événements associés à ce document
																											$query_RS2	=	eventsForThisDoc($row->DOCEVEN_id);
																											$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());
																											$nbrEventLinked	=	mysql_num_rows($RS2);
																											if	($nbrEventLinked)	{
																														while	($row_RS2	=	mysql_fetch_object($RS2))	{
																																	echo	'> '	.	$row_RS2->EVEN_nom_fr	.	'<br/>';
																														}
																											}	else	{
																														echo	'<i>aucun	événement</i>';
																											}
																											?>
																								</td>
																								<td nowrap class="note"><?php	if	(!is_null($row->LOG_date))	{	echo	adn_afficheDateTime($row->LOG_date,	"DB_FR");	}	?></td>
																								<td nowrap class="note"><?php	if	(!is_null($row->LOG_date))	{	echo	$row->ACS_login;	}	?></td>
																								<?php	if	($autoriseDelete)	{	?>
																											<td align="center">
																														<?php	if	(!$nbrEventLinked)	{	?>
																																	<img class="bt_delete pointer" src="../_media/bo_delete.png" id_todelete="<?php	echo	$row->DOCEVEN_id;	?>" width="16" height="16" border="0"alt=""/>
																														<?php	}	else	{	?>
																																	<img src="../_media/bo_help_16.png" width="16" height="16" border="0"alt=""/>
																														<?php	}	?>
																											</td>
																								<?php	}	?>
																					</tr>
																		<?php	}	}	?>
															<tr>
																		<td colspan="<?php	echo	$nbreCol	?>" class="navig">
																					<?php	echo	adn_navigationTableau($currentUrl,	$queryString,	$pageNum,	$totalPages,	$startRow,	$maxRows,	$totalRows,	FALSE);	?>
																		</td>
															</tr>
												</table>
									<p class="note"><img src="../_media/bo_help_16.png" width="16" height="16" border="0"alt=""/> Ce document ne peut être supprimé pour le moment car il est associé à au moins un événement.</p>
									<?php	}	?>
									<?php	if	($_SESSION['afficheSession']	==	2)	{	echo	('<p class="requete"><b>Requête : </b>'	.	$query	.	'</p>');	}	?>
									<?php	if	(isSet($messageDebug))	{	echo	$messageDebug;	}	?>
									<div id="box_description" class="box_description"><?php	echo	$str_Description;	?></div>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
