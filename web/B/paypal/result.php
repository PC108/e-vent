<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('../../librairie/php/code_adn/GestionTableau.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('_requete.php');

/* * ******************************************************** */
/*              Configuration                    */
/* * ******************************************************** */
$tableId	=	'PAY';
$sessionFiltre	=	'FiltrePAY';
$nbreCol	=	21;
$chemin	=	'paypal';

$autoriseAdd	=	false;
$autoriseFiltre	=	true;
$autoriseExport	=	false;
$autoriseMaj	=	false;
$autoriseComment	=	false;
$autoriseDelete	=	false;
$autoriseLog	=	false;

if	($autoriseMaj)
		$nbreCol+=	2;
if	($autoriseDelete)
		$nbreCol++;
if	($autoriseComment)
		$nbreCol++;
if	($autoriseLog)
		$nbreCol++;

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'cmd')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

adn_checkEffaceFiltres($sessionFiltre);
$query	=	mainQueryPay();
$query	=	adn_creerFiltre($query,	$sessionFiltre,	array('fonction',	'submit'),	array());
$query	=	adn_orderBy($query,	$sessionFiltre,	'PAY_id DESC');

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

//-----------------------------


/*	* ******************************************************** */
/*       Variables d'affichages de messages                */
/*	* ******************************************************** */
// Gestion des variables d'affichages
$infoFiltre	=	adn_afficheNbreFiltres('FiltrePAY',	array('check_withcmt',	'select_tri',	'submit',	'chemin_retour',	'fonction'));
$str_Description	=	"";

$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L2']['nom']	.	"  - Résultat de la recherche";

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
												<th nowrap>&nbsp;</th>
		    		    <th nowrap><b>Référence</b></th>
		    		    <th nowrap>Montant</th>
		    		    <th nowrap>Timestamp </th>
		    		    <th nowrap>Retour</th>
		    		    <th nowrap>Status</th>
		    		    <th nowrap>Custom</th>
		    		    <th nowrap>Description </th>
		    		    <th nowrap>Token</th>
		    		    <th nowrap>Nom (paypal)</th>
		    		    <th nowrap>Prénom (paypal)</th>
		    		    <th nowrap>Email (paypal)</th>
		    		    <th nowrap>Status (paypal)</th>
		    		    <th nowrap>Code ville (paypal)</th>
		    		    <th nowrap>Identifiant (paypal)</th>
		    		    <th nowrap>Nom (livraison)</th>
		    		    <th nowrap>Rue (livraison)</th>
		    		    <th nowrap>Ville (livraison)</th>
		    		    <th nowrap>Code Etat (livraison)</th>
		    		    <th nowrap>Code postal (livraison)</th>
		    		    <th nowrap>Code ville (livraison)</th>
		    		    <th nowrap>Nom état (livraison)</th>
												<?php	if	($autoriseDelete)	{	?><th>&nbsp;</th><?php	}	?>
										</tr>
										<?php
										if	($NbreRows_RS	>	0)	{
												while	($row	=	mysql_fetch_array($RS))	{
														$idCmd	=	$row['CMD_id'];
														// Gestion du TimeStamp
														$timeStamp	=	explode("T",	$row['PAY_timestamp']);
														$timeStamp[1]	=	rtrim($timeStamp[1],	"Z");
														$strTimeStamp	=	adn_afficheDateTime($timeStamp[0]	.	" "	.	$timeStamp[1],	"DB_FR");
														// Affichage du retour de PAYPAL
														switch	($row['PAY_transaction_status'])	{
																case	"PaymentActionNotInitiated":
																case	"PaymentActionInProgress":
																		$colorTxt	=	'DDDDDD';
																		$retourTxt	=	"ANNULE";
																		break;
																case	"PaymentActionFailed":
																		$colorTxt	=	'FF0000';
																		$retourTxt	=	"REFUS";
																		break;
																case	"PaymentCompleted":
																case	"PaymentActionCompleted":
																		$colorTxt	=	'00FF00';
																		$retourTxt	=	"OK";
																		break;
																default:
																		$colorTxt	=	'00FFFF';
																		$retourTxt	=	"INCONNU";
																		break;
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
																<?php	if	($autoriseLog)	{	?>
																		<td class="<?php	echo	$classTrEvent	?>" align="center">
																				<div class="js_log pointer" id="<?php	echo	$idCmd	?>" table="<?php	echo	$tableId;	?>">
																						<img src="../_media/bo_maj.png" width="16" height="16" border="0" alt=""/>
																				</div>
																		</td>
																<?php	}	?>
																<?php	if	($autoriseComment)	{	?>
																		<td align="center" bgcolor="#EEEEEE" class="<?php	echo	$classTrEvent	?>">
																				<div class="js_commentaire pointer" id="<?php	echo	$row['FK_CMTPAY_id']	?>" idfiche="<?php	echo	$idCmd	?>">
																						<?php	if	(is_null($row['FK_CMTPAY_id']))	{	?>
																								<a href="<?php	echo	"../commentaire/add.php?table="	.	$tableId	.	"&idfiche="	.	$idCmd	.	"&pageNum="	.	$pageNum	?>">[+]</a>
																						<?php	}	else	{	?>
																								<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>
																						<?php	}	?>
																				</div>
																		</td>
																<?php	}	?>
																<td align="center">
																		<a href="../../F/commande/print.php?lien=<?php	echo	$row['CMD_lien']	?>&pageNum=<?php	echo	$pageNum	?>&retour=paypal">
																				<img src="../_media/bo_print.gif" width="16" height="16" border="0" alt=""/>
																		</a>
																</td>
																<td nowrap><b><?php	echo	$row['CMD_ref']	?></b></td>
																<td nowrap><?php	echo	$row['PAY_montant']	?></td>
																<td nowrap><?php	echo	$strTimeStamp;	?></td>
																<td nowrap style="background-color: #<?php	echo	$colorTxt	?>"><?php	echo	$retourTxt;	?></td>
																<td nowrap><?php	echo	$row['PAY_transaction_status']	?></td>
																<td nowrap><?php	echo	$row['PAY_custom']	?></td>
																<td nowrap><?php	echo	$row['PAY_description']	?></td>
																<td nowrap><?php	echo	$row['PAY_token']	?></td>
																<td nowrap><?php	echo	$row['PAY_payer_first_name']	?></td>
																<td nowrap><?php	echo	$row['PAY_payer_last_name']	?></td>
																<td nowrap><?php	echo	$row['PAY_payer_email']	?></td>
																<td nowrap><?php	echo	$row['PAY_payer_status']	?></td>
																<td nowrap><?php	echo	$row['PAY_payer_contry_code']	?></td>
																<td nowrap><?php	echo	$row['PAY_payer_id']	?></td>
																<td nowrap><?php	echo	$row['PAY_shipto_name']	?></td>
																<td nowrap><?php	echo	$row['PAY_shipto_street']	?></td>
																<td nowrap><?php	echo	$row['PAY_shipto_city']	?></td>
																<td nowrap><?php	echo	$row['PAY_shipto_state']	?></td>
																<td nowrap><?php	echo	$row['PAY_shipto_zip']	?></td>
																<td nowrap><?php	echo	$row['PAY_shipto_country_code']	?></td>
																<td nowrap><?php	echo	$row['PAY_shipto_country_name']	?></td>
																<?php	if	($autoriseDelete)	{	?><td align="center"><img class="bt_delete pointer" src="../_media/bo_delete.png" id_todelete="<?php	echo	$idCmd;	?>" width="16" height="16" border="0"alt=""/></td><?php	}	?>
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
				</div>
				<div id="box_popup">--</div>
				<div id="BoxDescription"><?php	echo	$str_Description;	?></div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
