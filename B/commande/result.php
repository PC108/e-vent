<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('../../librairie/php/code_adn/GestionTableau.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('_requete.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'CMD';
$sessionFiltre	=	'FiltreCMD';
$nbreCol	=	20;
$chemin	=	'commande';

$autoriseAdd	=	false;
$autoriseFiltre	=	true;
$autoriseExport	=	false;
$autoriseMaj	=	true;
$autoriseComment	=	true;
$autoriseDelete	=	true;
$autoriseLog	=	true;

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'cmd')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*           Gestion des GET                    */
/*	* ******************************************************** */

//Ferme tous les événements en une fois
if	(isset($_GET['close'])	&&	($_GET['close']	==	1)	&&	isset($_SESSION['lastOpenBOCmd']))	{
			$_SESSION['lastOpenBOCmd']	=	array();
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
adn_checkEffaceFiltres($sessionFiltre);
$query_cmd	=	mainQueryCmd($connexion);
$query_cmd	=	adn_creerFiltre($query_cmd,	$sessionFiltre,	array('check_withcmt',	'select_tri',	'submit',	'fonction'),	array('FK_CMTCMD_id'));
$query_cmd	=	adn_orderBy($query_cmd,	$sessionFiltre,	'CMD_id DESC');

// echo $query_cmd;

$maxRows	=	$_COOKIE['nbre_ligne'];
list($queryString,
								$query_RS_Limit,
								$totalRows,
								$currentUrl,
								$pageNum,
								$totalPages,
								$startRow)	=	adn_limiteAffichage($maxRows,	$query_cmd);

$RS	=	mysql_query($query_RS_Limit,	$connexion)	or	die(mysql_error());
$NbreRows_RS	=	mysql_num_rows($RS);
//-----------------------------
// Gestion des variables d'affichages
$str_Description	=	"";

/*	* ******************************************************** */
/*       Variables d'affichages de messages                */
/*	* ******************************************************** */
// Gestion des variables d'affichages
$infoFiltre	=	adn_afficheNbreFiltres('FiltreCMD',	array('check_withcmt',	'select_tri',	'submit',	'fonction'));

$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L1']['nom']	.	"  - Résultat de la recherche";

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
												// Gestion des commentaires
												NS_BACK.initPopupCommentaire('<?php	echo	$tableId	?>', '<?php	echo	$pageNum	?>');
												// Gestion des logs
												NS_BACK.initPopupLog($('.js_log'), '<?php	echo	$tableId	?>');
												// Sauve le nombre de ligne affiché sur un tableau dans un cookie
												NS_BACK.saveNbreLigneTableau();
												// Initialisation du bouton ouvrant les sous-tables
												NS_BACK.initBoutonSousTable($('.bt_openAchat'), "lastOpenBOCmd");
												// Alerte avant de supprimer
												NS_BACK.checkSupprime($('.bt_delete'), 'action.php?pageNum=<?php	echo	$pageNum	?>');
												NS_BACK.checkSupprime($('.bt_delete_achat'), '../achat/action.php?pageNum=<?php	echo	$pageNum	?>');
												NS_BACK.checkSupprimeAll($('.bt_delete_all_achat'), '../achat/action.php?pageNum=<?php	echo	$pageNum	?>');
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
												<li class="boutons"><a href="?close=1&pageNum=<?php	echo	$pageNum	?>">Cacher tous les achats <img src="../_media/bo_close.png" width="16" height="16" border="0" align="absmiddle" alt="annuler les filtres"/></a></li>
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
																		<th>&nbsp;</th>
																		<?php	if	($autoriseLog)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<?php	if	($autoriseComment)	{	?><th width="20">&nbsp;</th><?php	}	?>
																		<th>Référence</th>
																		<th>Achats</th>
																		<th>Acheteur</th>
																		<th>Etat <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
																		<th nowrap>Date créa.</th>
																		<th nowrap>Date confirm.</th>
																		<th>Remise </th>
																		<th nowrap>Total Commande <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
																		<th>Encaissement</th>
																		<th nowrap>Mode de P.</th>
																		<th nowrap>Total Remb.</th>
																		<?php	if	($autoriseDelete)	{	?><th>&nbsp;</th><?php	}	?>
															</tr>
															<?php
															if	($NbreRows_RS	>	0)	{
																		while	($row	=	mysql_fetch_array($RS))	{
																					$idCmd	=	$row['CMD_id'];

																					if	((isset($_SESSION['lastOpenBOCmd']))	&&	(in_array($idCmd,	$_SESSION['lastOpenBOCmd'])))	{
																								$classTrEvent	=	"titreEventActif";
																								$txtBouton	=	"Cacher les "	.	$row['nbreAchats']	.	" achats";
																					}	else	{
																								$classTrEvent	=	"";
																								$txtBouton	=	"Afficher les "	.	$row['nbreAchats']	.	" achats";
																					}
																					?>
																					<tr valign="top">
																								<?php	if	($autoriseMaj)	{	?>
																											<td class="<?php	echo	$classTrEvent	?>" align="center">
																														<a href="add.php?id=<?php	echo	$idCmd	?>&pageNum=<?php	echo	$pageNum	?>">
																																	<img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/>
																														</a>
																											</td>
																								<?php	}	?>
																								<td class="<?php	echo	$classTrEvent	?>" align="center">
																											<a href="../../F/commande/print.php?lien=<?php	echo	$row['CMD_lien']	?>&pageNum=<?php	echo	$pageNum	?>&retour=cmd_BO">
																														<img src="../_media/bo_print.gif" width="16" height="16" border="0" alt=""/>
																											</a>
																								</td>
																								<?php	if	($autoriseLog)	{	?>
																											<td class="<?php	echo	$classTrEvent	?>" align="center">
																														<div class="js_log pointer" id="<?php	echo	$idCmd	?>" table="<?php	echo	$tableId;	?>">
																																	<img src="../_media/bo_maj.png" width="16" height="16" border="0" alt=""/>
																														</div>
																											</td>
																								<?php	}	?>
																								<?php	if	($autoriseComment)	{	?>
																											<td align="center" bgcolor="#EEEEEE" class="<?php	echo	$classTrEvent	?>">
																														<div class="js_commentaire pointer" id="<?php	echo	$row['FK_CMTCMD_id']	?>" idfiche="<?php	echo	$idCmd	?>">
																																	<?php	if	(is_null($row['FK_CMTCMD_id']))	{	?>
																																				<a href="<?php	echo	"../commentaire/add.php?table="	.	$tableId	.	"&idfiche="	.	$idCmd	.	"&pageNum="	.	$pageNum	?>">[+]</a>
																																	<?php	}	else	{	?>
																																				<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>
																																	<?php	}	?>
																														</div>
																											</td>
																								<?php	}	?>
																								<td nowrap class="titreEvent <?php	echo	$classTrEvent	?>">
																											<h3 style="margin:0;"><?php	echo	$row['CMD_ref']	?></h3>
																											<div class="bt_openAchat" idParent="<?php	echo	$idCmd;	?>"><?php	echo	$txtBouton;	?></div>
																								</td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>"><b><?php	echo	$row['nbreAchats'];	?></b> achats</td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>">
																											<?php	echo	$row['ADH_nom']	.	"<br />"	.	$row['ADH_prenom']	?>
																											<form action="../adherent/result.php" method="POST" style="margin:0;">
																														<input type="hidden" name="ADH_identifiant" value="<?php	echo	$row['ADH_identifiant'];	?>">
																														<input type="hidden" name="fonction" value="adn_creerFiltre" />
																														<input type="submit" name="submit" value="" class="submit_row" />
																											</form>
																								</td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>" style="background-color: #<?php	echo	$row['ECMD_couleur']	?> "><?php	echo	$row['ECMD_nom']	?></td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>" ><?php	echo	adn_changeFormatDate($row['CMD_date'],	'DB_fr');	?></td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>" ><?php	if	(($row['CMD_date_confirm']	!=	"0000-00-00"))	{	echo	adn_changeFormatDate($row['CMD_date_confirm'],	'DB_fr');	}	?></td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>"><i><?php	echo	$row['CMD_remise']	?> €</i></td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>"><?php	echo	$row['totalAchats']	.	" - <i>"	.	$row['CMD_remise']	.	"</i> = <b>"	.	$row['totalCommande']	.	" €</b>"	?></td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>"><?php	echo	$row['CMD_encaissement']	?> €</td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>"><?php	echo	$row['MDPAY_nom_fr']	?></td>
																								<td nowrap class="<?php	echo	$classTrEvent	?>"><b><?php	echo	$row['totalRemb']	?> €</b></td>
																								<?php	if	($autoriseDelete)	{	?>
																											<td class="<?php	echo	$classTrEvent	?>" align="center">
																														<img class="bt_delete pointer " src="../_media/bo_delete.png" id_todelete="<?php	echo	$idCmd;	?>" width="16" height="16" border="0"alt=""/>
																											</td>
																								<?php	}	?>
																					</tr>
																					<?php	if	((isset($_SESSION['lastOpenBOCmd']))	&&	(in_array($idCmd,	$_SESSION['lastOpenBOCmd'])))	{	?>
																								<tr id="celToOpen_<?php	echo	$idCmd;	?>">
																											<td colspan="<?php	echo	$nbreCol	?>" class="cel_soustable">
																														<?php	include("../achat/result.php");	?>
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
												<?php	if	($_SESSION['afficheSession']	==	2)	{	echo	('<p class="requete"><b>Requête : </b>'	.	$query_cmd	.	'</p>');	}	?>
												<div id="help_icon" class="bt_help pointer"><img src="../_media/bo_help_32.png"/></div>
												<div id="help" title='<img src="../_media/bo_help_32.png" align="absmiddle"/> aide'>
															<p>Total: <span class="note">Le total de la commande correspond à : (Total des achats + Total des surcoûts) * (Ratio du Type de tarif)  - Remise sur la commande.
																					<br>Les surcoûts proviennent d'inscriptions à des événements alors que la cotisation de l'inscrit n'était pas à jour au moment de l'ajout de l'achat à la commande.</span></p>
															<p>Ratio: <span class="note">Correspond au ratio du type de tarif au moment de l'ajout de l'achat à la commande.
																					<br />Si le type de tarif a été modifié au niveau de l'adhérent, il ne sera pas mis à jour dans les achats de la commande en cours.</span></p>
															<p><u>Etats automatiques</u></p>
															<p>Sans info: <span class="note">L'application n'a pu fournir d'information complémentaire (état par défaut).</span></p>
															<p>PAYPAL validé: <span class="note">Le paiement de l'utilisateur via PAYPAL a été accepté et votre compte a été crédité du montant de la commande.
																					<br />La commande doit maintenant être passée en confirmée par vos soins pour le décompte des places restantes.</span></p>
															<p>PAYPAL annulé: <span class="note">L’utilisateur a initié un paiement via PAYPAL puis a annulé la procédure en cliquant sur le bouton « Annuler » sur la page PAYPAL.
																					<br />Aucun montant n’a été crédité à votre compte PAYPAL.</span></p>
															<p>PAYPAL refusé: <span class="note">L’utilisateur a tenté de payer sa commande via PAYPAL mais son paiement a été refusé.
																					<br />Aucun montant n’a été crédité à votre compte PAYPAL.</span></p>
															<p>PAYPAL sans info: <span class="note">L’application n’a pas reçu correctement la réponse de PAYPAL.
																					<br />Le paiement peut être validé, annulé ou refusé. Vous le saurez en allant dans votre compte PAYPAL.</span></p>
															<p><u>Etats assignables</u></p>
															<p>En cours: <span class="note">La commande a été créée par l'utilisateur qui est en train d'en définir le contenu.</span></p>
															<p>Confirmée: <span class="note">Passez une commande en « confirmée » lorsque vous avez reçu le paiement de la commande.
																					<br/>La commande ne peut alors plus être modifiée par l’utilisateur et passe dans la rubrique "Historique des commandes".
																					<br />Une commande dont l’état est PAYPAL validé doit être passée manuellement en confirmé.
																					<br />ATTENTION : Tous les achats d'une commande confirmée sont immédiatement déduits dans les décomptes de capacités de places.</span></p>
												</div>
									<?php	}	?>
									<?php	if	(isSet($messageDebug))	{	echo	$messageDebug;	}	?>
									<div id="box_popup">--</div>
									<div id="box_description" class="box_description"><?php	echo	$str_Description;	?></div>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
