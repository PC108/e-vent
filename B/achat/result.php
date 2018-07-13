<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
// include('../_shared.php'); // inutile. Déjà inclus dans commande/result.php
require_once('../../librairie/php/code_adn/Formatage.php');
require_once("../achat/_requete.php");
require_once('../../librairie/php/code_adn/CropTexte.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableIdAchat	=	'ACH';
$cheminAchat	=	'achat';

$autoriseAddAchat	=	false;
$autoriseMajAchat	=	true;
$autoriseCommentAchat	=	false;
$autoriseDeleteAchat	=	true;
$autoriseLogAchat	=	false;

$activeMaj	=	true;

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$query	=	mainQueryAch($row['CMD_id'],	$connexion);
$query.="ORDER BY FK_ADH_id,EVEN_id,JREVEN_date_debut, TYACH_ordre, TYRES_ordre, TYHEB_ordre ";

$RSAchat	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$NbreRows_achat	=	mysql_num_rows($RSAchat);

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
if	($NbreRows_achat	!=	0)	{
			?>
<div style="margin-bottom: 30px; text-align: right; display: inline-block;">
			<table class="table_result" cellspacing="0">
						<tr>
									<?php	if	($autoriseMajAchat	&&	$activeMaj)	{	?><th width="20">&nbsp;</th><?php	}	?>
									<?php	if	($autoriseLogAchat)	{	?><th width="20">&nbsp;</th><?php	}	?>
									<?php	if	($autoriseCommentAchat)	{	?><th width="20">&nbsp;</th><?php	}	?>
									<th nowrap>Bénéficiaire</th>
									<th nowrap>Type d'achat</th>
									<th>Participe</th>
									<th nowrap>Description | Montant</th>
									<th>etat</th>
									<th>Ratio <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></th>
									<th><b>Achat</b></th>
									<th>Remb.</th>
									<?php	if	($autoriseDeleteAchat)	{	?><th width="20">&nbsp;</th><?php	}	?>
						</tr>

						<?php
						if	($NbreRows_achat	>	0)	{
									$old_name	=	"";
									while	($rowAch	=	mysql_fetch_object($RSAchat))	{
												?>
												<tr class="to_highlight" valign="top">
															<?php	if	($autoriseMajAchat	&&	$activeMaj)	{	?><td align="center"><a href="../achat/add.php?idCmd=<?php	echo	$rowAch->FK_CMD_id;	?>&idAch=<?php	echo	$rowAch->ACH_id	?>&pageNum=<?php	echo	$pageNum	?>"><img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/></a></td><?php	}	?>
															<?php	if	($autoriseLogAchat)	{	?>
																		<td align="center">
																					<div class="js_logAchat" id="<?php	echo	$idCmd;	?>" table="<?php	echo	$tableIdAchat;	?>" adh="<?php	echo	$rowAch->ADH_id;	?>">
																								<img src="../_media/bo_maj.png" width="16" height="16" border="0" alt=""/>
																					</div>
																		</td>
															<?php	}	?>
															<?php	if	($autoriseCommentAchat)	{	?>
																		<td align="center" bgcolor="#EEEEEE">
																					<div class="js_commentaire pointer" id="<?php	echo	$rowAch->FK_CMTACH_id	?>" idfiche="<?php	echo	$row->FK_CMD_id	?>">
																								<?php	if	(is_null($rowAch->FK_CMTACH_id))	{	?>
																											<a href="../commentaire/add.php?table=JREVEN&idfiche=<?php	echo	$rowAch->ACH_id	?>">[+]</a>
																								<?php	}	else	{	?>
																											<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>
																								<?php	}	?>
																					</div>
																		</td>
															<?php	}	?>
															<?php
															$html	=	"";
															switch	($rowAch->FK_TYACH_id)	{
																		case	1:
																		case	2:	// Cotisations et dons
																					if	($old_name	!=	$rowAch->ADH_id)	{
																								$html	.=	'<td nowrap>'	.	$rowAch->ADH_prenom	.	' '	.	$rowAch->ADH_nom	.	'</td>';
																					}	else	{
																								$html	.=	'<td>&nbsp;</td>';
																					}
																					$html	.=	'<td nowrap><b>'	.	$rowAch->TYACH_nom_fr	.	'</b></td>';
																					$html	.=	'<td bgcolor="#DDDDDD">&nbsp;</td>';
																					$html	.=	"<td nowrap>"	.	$rowAch->TYCOT_nom_fr	.	' '	.	$rowAch->TYDON_nom_fr	.	' | '	.	$rowAch->ACH_montant	.	" €</td>";
																					break;

																		case	3;	// Jour evenement
																					$infoDates	=	adn_afficheFromDateToDate($rowAch->JREVEN_date_debut,	$rowAch->JREVEN_date_fin,	"DB_fr");

																					if	($old_name	!=	$rowAch->ADH_id)	{
																								$html	.=	'<td nowrap>'	.	$rowAch->ADH_prenom	.	' '	.	$rowAch->ADH_nom	.	' </td>';
																					}	else	{
																								$html	.=	'<td>&nbsp;</td>';
																					}
																					$html	.=	'<td nowrap><b>'	.	$rowAch->TYACH_nom_fr;
																					if ($rowAch->EVEN_pleintarif == 1) { // L'événement est plein tarif
																								$html	.= ' plein tarif';
																					}
																					$html	.= '</b></td>';
																					if	($rowAch->ACH_participe)	{
																								$html	.=	'<td><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/></td>';
																					}	else	{
																								$html	.=	'<td><img src="../_media/bo_participe_not.png" width="16" height="16" border="0"alt=""/></td>';
																					}
																					// Description Jour événement
																					$html	.=	'<td nowrap><span class="js_description pointer" id="fr_'	.	$rowAch->ACH_id	.	'">'	.	adn_cropTexte($rowAch->EVEN_nom_fr,	15)	.	' | '	.	adn_cropTexte($infoDates[0],	15)	.	' | '	.	$rowAch->ACH_montant	.	' €</span>';
																					$str_Description	.=	'<div id="fr_'	.	$rowAch->ACH_id	.	'" class="invisible">'	.	$rowAch->EVEN_nom_fr	.	'<br />'	.	$infoDates[0]	.	'<br />'	.	$rowAch->LEVEN_nom	.	' | '	.	$rowAch->PAYS_nom_fr	.	'<br />'	.	$rowAch->ACH_montant	.	'€</div>';
																					// Surcout
																					if	($rowAch->ACH_surcout	>	0)	{
																								$html	.=	'<br /><i>Surcoût cotisation : '	.	$rowAch->ACH_surcout	.	' €</i>';
																					}
																					$html	.=	'</td>';
																					break;

																		case	4:	// Options (Restauration et hébergement)
																		case	5:
																					$html	.=	'<td>&nbsp;</td>';
																					$html	.=	'<td nowrap class="note"> Avec '	.	$rowAch->TYACH_nom_fr;
																					if	($rowAch->ACH_participe)	{
																								$html	.=	'<td><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/></td>';
																					}	else	{
																								$html	.=	'<td><img src="../_media/bo_participe_not.png" width="16" height="16" border="0"alt=""/></td>';
																					}
																					$html	.=	'<td nowrap>'	.	$rowAch->TYHEB_nom_fr	.	' '	.	$rowAch->TYRES_nom_fr	.	' | '	.	$rowAch->ACH_montant	.	' €';
																					break;
															}
															echo	$html;
															?>
															<?php
															// Etat
															if	(
																							($rowAch->FK_TYACH_id	==	3	&&	($rowAch->nbreAchatsJREVEN	>=	$rowAch->JREVEN_places)	&&	$rowAch->JREVEN_places	!=	0)	||
																							($rowAch->FK_TYACH_id	==	4	&&	($rowAch->nbreAchatsTYHEB	>=	$rowAch->TYHEB_JREVEN_capacite)	&&	$rowAch->TYHEB_JREVEN_capacite	!=	0)	||
																							($rowAch->FK_TYACH_id	==	5	&&	($rowAch->nbreAchatsTYRES	>=	$rowAch->TYRES_JREVEN_capacite)	&&	$rowAch->TYRES_JREVEN_capacite	!=	0)
															)	{
																		echo	'<td class="complet">complet</td>';
															}	else	if	($rowAch->FK_EJREVEN_id	!=	2)	{	// en vente
																		echo	'<td class="note">'	.	$rowAch->EJREVEN_nom_fr	.	'</td>';
															}	else	{
																		echo	'<td>ok</td>';
															}
															// Ratio
															echo	'<td nowrap>'	.	$rowAch->ACH_ratio	.	' %</td>';
															// Achat
															echo	'<td nowrap><b>'	.	adn_enDecimal(($rowAch->ACH_montant	*	$rowAch->ACH_ratio	)	/	100,	FALSE)	.	' €</b>';
															// Surcout
															if	($rowAch->ACH_surcout	>	0)	{
																		echo	'<br /><b><i>'	.	adn_enDecimal(($rowAch->ACH_surcout	*	$rowAch->ACH_ratio)	/	100,	FALSE)	.	' €</i>';
															}
															echo	'</td>';
															// Remboursement
															if	($rowAch->ACH_remb	==	0)	{
																		echo	'<td nowrap style="background-color:#DDDDDD">'	.	$rowAch->ACH_remb	.	' € </td>';
															}	else	{
																		echo	'<td nowrap style="background-color:#FFFFFF">'	.	$rowAch->ACH_remb	.	' € </td>';
															}
															?>
															<?php	if	($autoriseDeleteAchat)	{	?><td align="center"><img class="bt_delete_achat pointer" src="../_media/bo_delete.png" id_todelete="<?php	echo	$rowAch->ACH_id;	?>" width="16" height="16" border="0"alt=""/></td><?php	}	?>
												</tr>
												<?php
												$old_name	=	$rowAch->ADH_id;
									}
						}
						?>
			</table>
			<div class="boutons bt_delete_all_achat" cmd_todelete="<?php	echo	$idCmd;	?>" style="margin: 5px 0 0 0">Supprimer tous les achats <img src="../_media/bo_delete.png" width="16" height="16" border="0" align="absmiddle"/></div>
			</div>
<?php	}	?>
