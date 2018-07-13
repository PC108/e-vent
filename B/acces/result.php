<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/GestionTableau.php');

/* * ******************************************************** */
/*              Configuration                    */
/* * ******************************************************** */
$tableId	=	'ACS';
$nbreCol	=	6;
$chemin	=	'acces';

$autoriseAdd	=	true;
$autoriseFiltre	=	false;
$autoriseExport	=	false;
$autoriseMaj	=	true;
$autoriseComment	=	true;
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
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

// création la requéte
$query	=	"SELECT * FROM t_acces_acs ORDER BY ACS_id";

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
/*              Début code de la page html                 */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['CONFIGURATION']['L1']['nom']	.	"  - Résultat de la recherche";

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

								// Gestion des commentaires
								NS_BACK.initPopupCommentaire('<?php	echo	$tableId	?>', '<?php	echo	$pageNum	?>');
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
														<?php	if	($autoriseExport)	{	?><a href="excel.php?com=0"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a> <?php	if	($autoriseComment)	{	?><a href="../../../B/competence/excel.php?com=1"> <img src="../_media/ic_excel_com.gif" width="46" height="28" border="0" align="absmiddle" alt="exporter avec commentaires"/></a><?php	}	}	?>
												</td>
										</tr>

										<tr>
												<?php	if	($autoriseMaj)	{	?><th width="20">&nbsp;</th><?php	}	?>
												<?php	if	($autoriseComment)	{	?><th width="20">&nbsp;</th><?php	}	?>
												<th nowrap>Login</th>
												<th nowrap>Mot de passe</th>
												<th nowrap>Groupe</th>
												<th nowrap>Compteur</th>
												<th nowrap>Date</th>
												<th nowrap>Time</th>
												<?php	if	($autoriseDelete)	{	?><th width="20">&nbsp;</th><?php	}	?>
										</tr>

										<?php	if	($NbreRows_RS	>	0)	{	?>
												<?php	while	($row	=	mysql_fetch_object($RS))	{	?>
														<tr class="to_highlight" valign="top">
																<td align="center"><?php	if	($autoriseMaj	&&	$row->ACS_id	>	99)	{	?><a href="add.php?id=<?php	echo	$row->ACS_id	?>&pageNum=<?php	echo	$pageNum	?>"><img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/></a><?php	}	?></td>
																<?php	if	($autoriseComment)	{	?><td align="center" bgcolor="#EEEEEE">
																				<div class="js_commentaire pointer" id="<?php	echo	$row->FK_CMTACS_id	?>" idfiche="<?php	echo	$row->ACS_id	?>">
																						<?php	if	(is_null($row->FK_CMTACS_id))	{	?>
																								<a href="../commentaire/add.php?table=ACS&idfiche=<?php	echo	$row->ACS_id	?>&pageNum=<?php	echo	$pageNum	?>">[+]</a>
																						<?php	}	else	{	?>
																								<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>
																						<?php	}	?>
																				</div>
																		</td>
																<?php	}	?>
																<td nowrap><?php	echo	$row->ACS_login;	?></td>
																<td nowrap><?php	echo	$row->ACS_pwd;	?></td>
																<td nowrap><?php	echo	$row->ACS_grp;	?></td>
																<td nowrap><?php	echo	$row->ACS_compteur;	?></td>
																<td nowrap><?php	echo	$row->ACS_date;	?></td>
																<td nowrap><?php	echo	$row->ACS_time;	?></td>
																<td align="center"><?php	if	($autoriseDelete	&&	$row->ACS_id	>	100)	{	?><img class="bt_delete pointer" src="../_media/bo_delete.png" id_todelete="<?php	echo	$row->ACS_id;	?>" width="16" height="16" border="0"alt=""/><?php	}	?></td>
														</tr>
												<?php	}	}	?>

										<tr>
												<td colspan="<?php	echo	$nbreCol	?>" class="navig">
														<?php	echo	adn_navigationTableau($currentUrl,	$queryString,	$pageNum,	$totalPages,	$startRow,	$maxRows,	$totalRows,	FALSE);	?>
												</td>
										</tr>

								</table>
						<?php	}	?>
						<?php	if	($_SESSION['afficheSession']	==	2)	{	echo	('<p class="requete"><b>Requête : </b>'	.	$query	.	'</p>');	}	?>
						<?php	if	(isSet($messageDebug))	{	echo	$messageDebug;	}	?>
						<div id="box_popup">--</div>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
