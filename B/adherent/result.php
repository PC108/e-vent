<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('../../librairie/php/code_adn/GestionTableau.php');
require_once('_requete.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'ADH';
$sessionFiltre	=	'FiltreADH';
$nbreCol	=	23;
$chemin	=	'adherent';

$autoriseAdd	=	true;
$autoriseFiltre	=	true;
$autoriseExport	=	true;
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

adn_checkEffaceFiltres($sessionFiltre);
$query	=	mainQueryAdherent();
$query	=	adn_creerExists($query,	$sessionFiltre,	'TJ_CMPT_id',	'EXISTS',	'tj_adh_cmpt',	'TJ_ADH_id = ADH_id');
$query	=	adn_creerFiltre($query,	$sessionFiltre,	array('check_withcmt',	'select_tri',	'submit',	'chemin_retour',	'fonction',	'TJ_CMPT_id'),	array('FK_CMTADH_id',	'NEWS_email'));
$query	=	adn_groupBy($query,	'ADH_id');
$query	=	adn_orderBy($query,	$sessionFiltre,	'ADH_id DESC');

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
// Gestion des variables d'affichages
$infoFiltre	=	adn_afficheNbreFiltres($sessionFiltre,	array('check_withcmt',	'select_tri',	'submit',	'chemin_retour',	'fonction'));

$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L1']['nom']	.	"  - Résultat de la recherche";

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
												<?php	if	($autoriseFiltre)	{	?><li class="boutons"><a href="search.php?id=0&pageNum=<?php	echo	$pageNum	?>">Filtrer les résultats <img src="../_media/bo_filter.png" width="16" height="16" border="0" align="absmiddle" alt="filtrer"/></a></li><?php	}	?>
												<?php	if	($autoriseFiltre)	{	?><li class="boutons"><a href="?clean=1" class="js_filtre">Annuler les filtres actifs (<?php	echo	$infoFiltre;	?>) <img src="../_media/bo_nofiltre.png" width="16" height="16" border="0" align="absmiddle" alt="annuler les filtres"/></a></li><?php	}	?>
												<li class="boutons"><a href="publipostage.php">Publipostage <img src="../_media/bo_csv.png" width="16" height="16" border="0" align="absmiddle" alt="publipostage"/></a></li>
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
																					<?php	if	($autoriseExport)	{	?><a href="csv.php?com=0"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a> <?php	if	($autoriseComment)	{	?><a href="csv.php?com=1"> <img src="../_media/ic_excel_com.gif" width="46" height="28" border="0" align="absmiddle" alt="exporter avec commentaires"/></a><?php	}	}	?>
																		</td>
															</tr>

															<tr>
																		<?php	if	($autoriseMaj)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseLog)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseComment)	{	?><th>&nbsp;</th><?php	}	?>
																		<th colspan="4"><b>Inscription</b></th>
																		<th colspan="6"><b>Informations Personnelles</b></th>
																		<th colspan="4"><b>Communication</b></th>
																		<th colspan="3"><b>Adresse</b></th>
																		<?php	if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	?><th colspan="2"><b>Sangha</b></th><?php	}	?>
																		<?php	if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	?><th colspan="4"><b>Bénévolat</b></th><?php	}	?>
																		<?php	if	($autoriseDelete)	{	?><th width="20">&nbsp;</th><?php	}	?>
															</tr>

															<tr>
																		<?php	if	($autoriseMaj)	{	?><th>&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseLog)	{	?><th>&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseComment)	{	?><th>&nbsp;</th><?php	}	?>
																		<th nowrap>Etat</th>
																		<th nowrap>Cotisation</th>
																		<th nowrap>Cmd <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
																		<th nowrap>Accès<br/>privé</th>
																		<th nowrap>Nom<br/>Prénom</th>
																		<th nowrap>Identifiant</th>
																		<th nowrap>Mot de passe</th>
																		<th nowrap>Genre</th>
																		<th nowrap>Année</th>
																		<th nowrap>Type tarif</th>
																		<th nowrap>Email</th>
																		<th nowrap>Newsletter</th>
																		<th nowrap>Telephone<br/>Portable</th>
																		<th nowrap>Langue</th>
																		<th nowrap>Adresse 1<br/>Adresse 2</th>
																		<th nowrap>Code postal<br/>Ville</th>
																		<th nowrap>Pays</th>
																		<?php	if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	?>
																					<th nowrap>Ordination</th>
																					<th nowrap>Nom Dharma</th>
																		<?php	}	?>
																		<?php	if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	?>
																					<th nowrap>ok ?</th>
																					<th nowrap>Profession</th>
																					<th nowrap>Disponibilités</th>
																					<th nowrap>Compétences</th>
																		<?php	}	?>
																					<?php	if	($autoriseDelete)	{	?><th>&nbsp;</th><?php	}	?>
															</tr>

															<?php	if	($NbreRows_RS	>	0)	{	?>
																		<?php	while	($row	=	mysql_fetch_object($RS))	{	?>
																					<tr class="to_highlight" valign="top">
																								<?php	if	($autoriseMaj)	{	?><td align="center"><a href="add.php?id=<?php	echo	$row->ADH_id	?>&pageNum=<?php	echo	$pageNum	?>"><img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/></a></td><?php	}	?>
																								<?php	if	($autoriseLog)	{	?>
																											<td align="center">
																														<div class="js_log pointer" id="<?php	echo	$row->ADH_id	?>" table="<?php	echo	$tableId;	?>">
																																	<img src="../_media/bo_maj.png" width="16" height="16" border="0" alt=""/>
																														</div>
																											</td>
																								<?php	}	?>
																								<?php	if	($autoriseComment)	{	?> <td align="center" bgcolor="#EEEEEE">
																														<div class="js_commentaire pointer" id="<?php	echo	$row->FK_CMTADH_id	?>" idfiche="<?php	echo	$row->ADH_id	?>">
																																	<?php	if	(is_null($row->FK_CMTADH_id))	{	?>
																																				<a href="../commentaire/add.php?table=ADH&idfiche=<?php	echo	$row->ADH_id	?>&pageNum=<?php	echo	$pageNum	?>">[+]</a>
																																	<?php	}	else	{	?>
																																				<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>
																																	<?php	}	?>
																														</div>
																											</td>
																								<?php	}	?>
																								<td nowrap style="background-color: #<?php	echo	$row->EADH_couleur;	?>"><?php	echo	$row->EADH_nom;	?></td>
																								<td nowrap><?php	echo	$row->ADH_annee_cotisation;	?></td>
																								<td nowrap><b><?php	echo	$row->NbrCommande;	?></b>
																											<?php	if	($row->NbrCommande)	{	?>
																														<form action="../commande/result.php" method="POST" style="margin:0; display: inline-block">
																																	<input type="hidden" name="ADH_identifiant" value="<?php	echo	$row->ADH_identifiant;	?>">
																																	<input type="hidden" name="fonction" value="adn_creerFiltre" />
																																	<input type="submit" name="submit" value="" class="submit_row" />
																														</form>
																											<?php	}	?>
																								</td>
																								<td nowrap align="center"><?php	if	($row->ADH_prive)	{	?><img src="../_media/bo_locked.png" width="16" height="16" border="0"alt=""/><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/><?php	}	?></td>
																								<td nowrap><b><?php	echo	$row->ADH_nom;	?></b><br/><?php	echo	$row->ADH_prenom;	?></td>
																								<td nowrap><?php	echo	$row->ADH_identifiant;	?></td>
																								<td nowrap><?php	echo	$row->ADH_password;	?></td>
																								<td nowrap><?php	echo	$row->ADH_genre;	?></td>
																								<td nowrap><?php	echo	$row->ADH_annee_naissance;	?></td>
																								<td nowrap><?php	echo	$row->TYTAR_nom_fr;	?> (<?php	echo	$row->TYTAR_ratio	?>%)</td>
																								<td nowrap><?php	echo	$row->ADH_email;	?></td>
																								<td nowrap align="center"><?php	if	(!is_null($row->NEWS_email))	{	?><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/><?php	}	?></td>
																								<td nowrap><?php	echo	$row->ADH_telephone;	?><br/><?php	echo	$row->ADH_portable;	?></td>
																								<td nowrap><?php	echo	$row->ADH_langue;	?></td>
																								<td nowrap><?php	echo	$row->ADH_adresse1;	?><br/><?php	echo	$row->ADH_adresse2;	?></td>
																								<td nowrap><?php	echo	$row->ADH_zip;	?><br/><?php	echo	$row->ADH_ville;	?></td>
																								<td nowrap><?php	echo	$row->PAYS_nom_fr;	?></td>
																								<?php	if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	?>
																											<td nowrap align="center"><?php	if	($row->ADH_ordination)	{	?><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/><?php	}	?></td>
																											<td nowrap><?php	echo	$row->ADH_nom_dharma;	?></td>
																								<?php	}	?>
																								<?php	if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	?>
																											<td nowrap align="center"><?php	if	($row->ADH_benevolat)	{	?><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/><?php	}	?>&nbsp;</td>
																											<td nowrap><?php	echo	$row->ADH_profession;	?></td>
																											<td nowrap><?php	echo	$row->ADH_disponibilite;	?></td>

																											<td nowrap>
																														<?php
																														$queryCmpt	=	getCompetenceAdherents($row->ADH_id);
																														$res	=	mysql_query($queryCmpt,	$connexion)	or	die(mysql_error());
																														$strCmpt	=	"";
																														while	($cmpt	=	mysql_fetch_object($res))	{
																																	$strCmpt	.=	$cmpt->CMPT_nom_fr	.	"<br />";
																														}
																														echo	rtrim($strCmpt,	"<br />");
																														?>
																											</td>
																								<?php	}	?>
																								<?php	if	($autoriseDelete)	{	?><td align="center"><img class="bt_delete pointer" src="../_media/bo_delete.png" id_todelete="<?php	echo	$row->ADH_id;	?>" width="16" height="16" border="0"alt=""/></td><?php	}	?>
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
															<p>Commande :  <span class="note">Ne sont pris en compte ici que les commandes directes créés par l'adhérent depuis son espace privé.
																					<br />Les commandes où l'adhérent pourrait apparaitre comme ami ne sont pas décomptées.</span></p>
												</div>
									<?php	}	?>
									<?php	if	($_SESSION['afficheSession']	==	2)	{	echo	('<p class="requete"><b>Requête : </b>'	.	$query	.	'</p>');	}	?>
									<?php	if	(isSet($messageDebug))	{	echo	$messageDebug;	}	?>
									<div id="box_popup">--</div>
						</div>
						<?php	include("../_footer.php");	?>
			</body>
</html>
