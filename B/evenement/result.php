<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('../../librairie/php/code_adn/GestionTableau.php');
require_once('../../librairie/php/code_adn/CropTexte.php');
require_once('_requete.php');
// Pour les Documents liés
require_once('../upload_doc/_requete.php');
// Pour la sous table JOUR EVENEMENT
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('../../librairie/php/code_adn/Compare2Dates.php');
require_once("../jourevenement/_requete.php");

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'EVEN';
$sessionFiltre	=	'FiltreEVEN';

$nbreCol	=	9;
$chemin	=	'evenement';

$autoriseAdd	=	true;
$autoriseFiltre	=	true;
$autoriseExport	=	false;
$autoriseMaj	=	true;
$autoriseComment	=	true;
$autoriseDelete	=	true;
$autoriseLog	=	true;

if	($autoriseMaj)	{
			$nbreCol+=	2;
}
if	($autoriseDelete)	{
			$nbreCol++;
}
if	($autoriseComment)	{
			$nbreCol++;
}
if	($autoriseLog)	{
			$nbreCol++;
}

// la configuration ds jours événements est positionnée ici pour ne pas être rappelé en boucle à chaque chargement de jourevenement/result.php
$tableIdJrev	=	'JREVEN';

$cheminJrev	=	'jourevenement';
$autoriseAddJrev	=	true;
$autoriseMajJrev	=	true;
$autoriseCommentJrev	=	false;
$autoriseDeleteJrev	=	true;
$autoriseLogJrev	=	true;

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */

if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*           Gestion des lignes cachées                    */
/*	* ******************************************************** */

//Ferme tous les événements en une fois
if	(isset($_GET['close'])	&&	($_GET['close']	==	1)	&&	isset($_SESSION['lastOpenBOEvent']))	{
			$_SESSION['lastOpenBOEvent']	=	array();
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
adn_checkEffaceFiltres($sessionFiltre);
$query	=	mainQueryEvent($connexion);
$query	=	adn_creerFiltre($query,	$sessionFiltre,	array('check_withcmt',	'select_tri',	'submit',	'chemin_retour',	'fonction'),	array('FK_CMTEVEN_id',	'EVEN_image',	'EVEN_lien'));
$query	=	adn_orderBy($query,	$sessionFiltre,	'minDate DESC');
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

/*	* ******************************************************** */
/*       Variables d'affichages de messages                */
/*	* ******************************************************** */
$infoFiltre	=	adn_afficheNbreFiltres('FiltreEVEN',	array('check_withcmt',	'select_tri',	'submit',	'chemin_retour',	'fonction',	'SQL_jour'));
$str_Description	=	"";

$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L1']['nom']	.	"  - Résultat de la recherche";

/*	* ******************************************************** */
/*              partamêtrage de la sous-table jourevenement/result.php                  */
/*	* ******************************************************** */
// les scripts sont positionnés ici pour ne pas être rappelé en boucle à chaque chargement de jourevenement/result.php
$tableIdJrev	=	'JREVEN';

$cheminJrev	=	'jourevenement';
$autoriseAddJrev	=	true;
$autoriseMajJrev	=	true;
$autoriseCommentJrev	=	false;
$autoriseDeleteJrev	=	true;
$autoriseLogJrev	=	true;

/*	* ******************************************************** */
/*              Fonctions utilisées dans jourevenement/result.php                  */
/*	* ******************************************************** */

// est positionné ici pour ne pas être initilaisée en boucle à chaque chargement de jourevenement/result.php (bug)
function	infoOccupation($nbrePlaces,	$nbreAchats)	{
			if	($nbrePlaces	==	0)	{
						$str	=	$nbreAchats	.	" sur illimitée";
			}	else	{
						$str	=	$nbreAchats	.	" sur "	.	$nbrePlaces;
						if	($nbreAchats	>=	$nbrePlaces)	{
									$str	.=	" - complet";
						}
			}
			return	$str;
}

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

												// Gestion des commentaires
												NS_BACK.initPopupCommentaire('<?php	echo	$tableId	?>', '<?php	echo	$pageNum	?>');
												// Gestion des logs
												NS_BACK.initPopupLog($('.js_log'), '<?php	echo	$tableId	?>');
												NS_BACK.initPopupLog($('.js_logJrev'), '<?php	echo	$tableIdJrev	?>');
												// Sauve le nombre de ligne affiché sur un tableau dans un cookie
												NS_BACK.saveNbreLigneTableau();
												// Affichage des descriptions
												NS_BACK.initBoxDescriptions(-6, -6, true);
												// Initialisation du bouton ouvrant les sous-tables
												NS_BACK.initBoutonSousTable($('.bt_openJrs'), "lastOpenBOEvent");
												// Alerte avant de supprimer
												NS_BACK.checkSupprime($('.bt_delete_event'), 'action.php?pageNum=<?php	echo	$pageNum	?>');
												NS_BACK.checkSupprime($('.bt_delete_jrevent'), '../jourevenement/action.php?pageNum=<?php	echo	$pageNum	?>');
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
												<?php	if	($autoriseFiltre)	{	?><li class="boutons"><a href="search.php?id=0&pageNum=<?php	echo	$pageNum	?>">Filtrer les résultats <img src="../_media/bo_filter.png" width="16" height="16" border="0" align="absmiddle" alt="filtrer"/></a></li><?php	}	?>
												<?php	if	($autoriseFiltre)	{	?><li class="boutons"><a href="?clean=1" class="js_filtre">Annuler les filtres actifs (<?php	echo	$infoFiltre;	?>) <img src="../_media/bo_nofiltre.png" width="16" height="16" border="0" align="absmiddle" alt="annuler les filtres"/></a></li><?php	}	?>
												<li class="boutons"><a href="?close=1&pageNum=<?php	echo	$pageNum	?>">Cacher tous les jours <img src="../_media/bo_close.png" width="16" height="16" border="0" align="absmiddle" alt="annuler les filtres"/></a></li>
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
																					<?php	if	($autoriseExport)	{	?><a href="csv.php?com=0"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a><?php	if	($autoriseComment)	{	?><a href="csv.php?com=1"> <img src="../_media/ic_excel_com.gif" width="46" height="28" border="0" align="absmiddle" alt="exporter avec commentaires"/></a><?php	}	}	?></td>
															</tr>
															<tr>
																		<?php	if	($autoriseMaj)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseLog)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseComment)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<th nowrap>Image</th>
																		<th nowrap>Nom FR / EN</th>
																		<th nowrap>Etat</th>
																		<th nowrap>Privé <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
																		<th nowrap>Plein tarif <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
																		<th nowrap>Documents</th>
																		<th nowrap>Url</th>
																		<th nowrap>Description FR</th>
																		<th nowrap>Description EN</th>
																		<?php	if	($autoriseDelete)	{	?><th width="20">&nbsp;</th><?php	}	?>
															</tr>
															<?php
															if	($NbreRows_RS	>	0)	{
																		while	($row	=	mysql_fetch_object($RS))	{
																					$idEvent	=	$row->EVEN_id;
																					$infoDates	=	adn_afficheFromDateToDate($row->minDate,	$row->maxDate,	"DB_fr");
																					// Icone
																					if	(is_null($row->EVEN_image)	||	!file_exists("../../upload/img/ic_"	.	$row->EVEN_image))	{
																								$image	=	"../../upload/img/ic_00defaut.jpg";
																					}	else	{
																								$image	=	"../../upload/img/ic_"	.	$row->EVEN_image;
																					}
																					if	((isset($_SESSION['lastOpenBOEvent']))	&&	(in_array($idEvent,	$_SESSION['lastOpenBOEvent'])))	{
																								$classTrEvent	=	"titreEventActif";
																								$txtBouton	=	"Cacher les "	.	$row->NbreAll	.	" jours";
																					}	else	{
																								$classTrEvent	=	"";
																								$txtBouton	=	"Afficher les "	.	$row->NbreAll	.	" jours";
																					}
																					?>
																					<tr valign="top">
																								<?php	if	($autoriseMaj)	{	?>
																											<td class="<?php	echo	$classTrEvent	?>" align="center">
																														<a href="add.php?id=<?php	echo	$idEvent	?>&pageNum=<?php	echo	$pageNum	?>">
																																	<img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/>
																														</a>
																											</td>
																								<?php	}	?>
																								<?php	if	($autoriseLog)	{	?>
																											<td class="<?php	echo	$classTrEvent	?>" align="center">
																														<div class="js_log pointer" id="<?php	echo	$idEvent;	?>" table="<?php	echo	$tableId;	?>">
																																	<img src="../_media/bo_maj.png" width="16" height="16" border="0" alt=""/>
																														</div>
																											</td>
																								<?php	}	?>
																								<?php	if	($autoriseComment)	{	?>
																											<td align="center" bgcolor="#EEEEEE" class="<?php	echo	$classTrEvent	?>">
																														<div class="js_commentaire pointer" id="<?php	echo	$row->FK_CMTEVEN_id	?>" idfiche="<?php	echo	$idEvent	?>">
																																	<?php	if	(is_null($row->FK_CMTEVEN_id))	{	?>
																																				<a href="../commentaire/add.php?table=EVEN&idfiche=<?php	echo	$idEvent	?>&pageNum=<?php	echo	$pageNum	?>">[+]</a>
																																	<?php	}	else	{	?>
																																				<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>
																																	<?php	}	?>
																														</div>
																											</td>
																								<?php	}	?>
																								<!--	TITRE -->
																								<td class="titreEvent <?php	echo	$classTrEvent	?>">
																											<img src="<?php	echo	$image	?>" width="40px" height="40px" alt="icone de l'événement"  class="img_event"/>
																								</td>
																								<td nowrap class="titreEvent <?php	echo	$classTrEvent	?>">
																											<h3 style="margin:0;"><?php	echo	$row->EVEN_nom_fr;	?><br /><?php	echo	$row->EVEN_nom_en;	?></h3>
																											<div class="dateEvent"><?php	echo	$infoDates[0];	?></div>
																											<div class="bt_openJrs" idParent="<?php	echo	$idEvent;	?>"><?php	echo	$txtBouton;	?></div>
																								</td>
																								<!--	ETAT -->
																								<td nowrap class="<?php	echo	$classTrEvent	?>">
																											<span><?php	echo	$row->NbreAll	?> dates</span>
																											<br /><span style="color: #3366cc"><i><?php	echo	$row->NbreEnVente	?> en vente</i></span>
																											<br /><span style="color: #bbbbcc"><i><?php	echo	$row->NbreDesactive	?> désactivés</i></span>
																											<br /><span style="color: #bbbbcc"><i><?php	echo	$row->NbreAnnule	?> annulés</i></span>
																											<br /><span class="depasse"><i><?php	if	($row->NbreDepasse)	{	echo	'<b>';	}	?><?php	echo	$row->NbreDepasse	?> dépassés<?php	if	($row->NbreDepasse)	{	echo	'</b>';	}	?></i></span>
																								</td>
																								<!--	PRIVE -->
																								<td nowrap class="<?php	echo	$classTrEvent	?>" align="center"><?php	if	($row->EVEN_prive)	{	?><img src="../_media/bo_locked.png" width="16" height="16" border="0" alt=""/><?php	}	?></td>
																								<!--	PLEIN TARIF -->																						<!--	PRIVE -->
																								<td nowrap class="<?php	echo	$classTrEvent	?>" align="center"><?php	if	($row->EVEN_pleintarif)	{	?><img src="../_media/bo_participe.png" width="16" height="16" border="0" alt=""/><?php	}	?></td>
																								<!--	DOCUMENT -->
																								<td nowrap class="<?php	echo	$classTrEvent	?>">
																											<?php
																											$query_RS2	=	DocsForThisEvent($idEvent);
																											$query_RS2	.=	"ORDER BY DOCEVEN_nom ASC";
																											$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());
																											while	($row_RS2	=	mysql_fetch_object($RS2))	{
																														echo	$row_RS2->DOCEVEN_nom	.	' <span class="note">('	.	$row_RS2->DOCEVEN_langue	.	')</span><br />';
																											}
																											?>
																								</td>
																								<!--	URL -->
																								<td nowrap class="<?php	echo	$classTrEvent	?>">
																											<?php
																											if	(!is_null($row->EVEN_lien))	{
																														$str_Description	.=	'<div id="url_'	.	$idEvent	.	'" class="invisible" style="white-space:nowrap;">'	.	$row->EVEN_lien	.	'</div>';
																														echo	'<img class="js_description pointer" id="url_'	.	$idEvent	.	'" src="../_media/bo_url.png" width="18" height="21" alt="description" border="0"/>';
																											}	else	{
																														echo	'&nbsp;';
																											}
																											?>
																								</td>
																								<!--	DESCRIPTION -->
																								<td class="<?php	echo	$classTrEvent	?>">
																											<?php
																											$str_Description	.=	'<div id="fr_'	.	$idEvent	.	'" class="invisible">'	.	nl2br($row->EVEN_descriptif_fr)	.	'</div>';
																											echo	('<div class="js_description pointer" id="fr_'	.	$idEvent	.	'">'	.	adn_cropTexte($row->EVEN_descriptif_fr,	40)	.	'</div>');
																											?>
																								</td>
																								<td class="<?php	echo	$classTrEvent	?>">
																											<?php
																											$str_Description	.=	'<div id="en_'	.	$idEvent	.	'" class="invisible">'	.	nl2br($row->EVEN_descriptif_en)	.	'</div>';
																											echo	('<div class="js_description pointer" id="en_'	.	$idEvent	.	'">'	.	adn_cropTexte($row->EVEN_descriptif_en,	40)	.	'</div>');
																											?>
																								</td>
																								<?php	if	($autoriseDelete)	{	?>
																											<td  class="<?php	echo	$classTrEvent	?>" align="center">
																														<img class="bt_delete_event pointer" src="../_media/bo_delete.png" id_todelete="<?php	echo	$idEvent;	?>" width="16" height="16" border="0"alt=""/>
																											</td>
																								<?php	}	?>
																					</tr>
																					<?php	if	((isset($_SESSION['lastOpenBOEvent']))	&&	(in_array($idEvent,	$_SESSION['lastOpenBOEvent'])))	{	?>
																								<tr id="celToOpen_<?php	echo	$idEvent;	?>">
																											<td colspan="<?php	echo	$nbreCol	?>" class="cel_soustable">
																														<?php	include("../jourevenement/result.php");	?>
																											</td>
																								</tr>
																					<?php	}	?>
																		<?php	}	}	?>
															<tr>
																		<td colspan="<?php	echo	$nbreCol	?>" class="navig">
																					<?php	echo	adn_navigationTableau($currentUrl,	$queryString,	$pageNum,	$totalPages,	$startRow,	$maxRows,	$totalRows,	FALSE);	?>
																		</td>
															</tr>
												</table>
												<div id="help_icon" class="bt_help pointer"><img src="../_media/bo_help_32.png"/></div>
												<div id="help" title='<img src="../_media/bo_help_32.png" align="absmiddle"/> aide'>
															<p>Privé:  <span class="note">Ces événements ne seront visibles qu'aux adhérents ayant un accès aux événements privés.</span></p>
															<p>Plein tarif:  <span class="note">Ces événements ne prennent pas en compte les conditions de tarifs réduits des adhérents lors de l'inscription.</span></p>
															<?php	if	($configAppli['MENU']['cotisation']	==	"oui")	{	?>
																		<p>Surcoût:  <span class="note">Correspond au coût additionnel pour ce jour événement si la personne n'a pas acquité sa cotisation annuelle.</span></p>
															<?php	}	?>
												</div>
									<?php	}	?>
									<?php	if	($_SESSION['afficheSession']	==	2)	{	echo	('<p class="requete"><b>Requête : </b>'	.	$query	.	'</p>');	}	?>
									<?php	if	(isSet($messageDebug))	{	echo	$messageDebug;	}	?>
						</div>
						<div id="box_popup">--</div>
						<div id="box_description"><?php	echo	$str_Description;	?></div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
