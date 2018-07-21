<?php
/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$query_jrevent	=	queryJourEvent($row->EVEN_id,	$connexion);
$RS_jrev	=	mysql_query($query_jrevent,	$connexion)	or	die(mysql_error());
$NbreRows_jrev	=	mysql_num_rows($RS_jrev);

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
if	($NbreRows_jrev)	{
		?>
		<table class="table_result" cellspacing="0">
				<tr>
						<?php	if	($autoriseMajJrev)	{	?><th width="20">&nbsp;</th><?php	}	?>
						<?php	if	($autoriseLogJrev)	{	?><th width="20">&nbsp;</th><?php	}	?>
						<?php	if	($autoriseCommentJrev)	{	?><th width="20">&nbsp;</th><?php	}	?>
						<th >Etat</th>
						<th>&nbsp;</th>
						<th nowrap>Dates / Lieu / options</th>
						<th>Montant</th>
						<?php	if	($configAppli['MENU']['cotisation']	==	"oui")	{	?>
								<th>Surcoût <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></a></th>
						<?php	}	?>
						<th>Occupation</th>
						<th>inscrits</th>
						<?php	if	($autoriseDeleteJrev)	{	?><th width="20">&nbsp;</th><?php	}	?>
				</tr>

				<?php
				while	($rowJrev	=	mysql_fetch_object($RS_jrev))	{
						// Vérifie si le jour événement est complet
						if	(($rowJrev->nbreAchats	>=	$rowJrev->JREVEN_places)	&&	$rowJrev->JREVEN_places	!=	0)	{
								$classJrevenFull	=	"complet";
						}	else	{
								$classJrevenFull	=	"";
						}
						// Vérifie si l'événement est dépassé
						if	(compare2dates($rowJrev->JREVEN_date_fin,	"<",	"today"))	{
								$classDepasse	=	"depasse";
						}	else	{
								$classDepasse	=	"";
						}

						// En premier le Jour événement 
						echo	'<tr class="to_highlight" valign="top">';
						// BOUTONS
						if	($autoriseMajJrev)	{
								echo	'<td align="center">';
								echo	'<a href="../jourevenement/add.php?idEven='	.	$row->EVEN_id	.	'&idJour='	.	$rowJrev->JREVEN_id	.	'&pageNum='	.	$pageNum	.	'"><img src="../_media/bo_edit.png" width="16" height="16" border="0" alt=""/></a>';
								echo	'</td>';
						}
						if	($autoriseLog)	{
								echo	'<td align="center">';
								echo	'<div class="js_logJrev pointer" id="'	.	$rowJrev->JREVEN_id	.	'" table="'	.	$tableIdJrev	.	'"><img src="../_media/bo_maj.png" width="16" height="16" border="0" alt=""/></div>';
								echo	'</td>';
						}
						if	($autoriseCommentJrev)	{
								echo	'<td align="center" bgcolor="#EEEEEE"><div class="js_commentaire pointer" id="'	.	$rowJrev->FK_CMTJREVEN_id	.	' idfiche="'	.	$rowJrev->JREVEN_id	.	'">';
								if	(is_null($rowJrev->FK_CMTJREVEN_id))	{
										echo	'<a href="../commentaire/add.php?table=JREVEN&idfiche='	.	$rowJrev->JREVEN_id	.	'">[+]</a>';
								}	else	{
										echo	'<img src="../_media/bo_comment.gif" width="18" height="21" border="0" alt=""/>';
								}
						}
						// Etat jour event
						echo	'<td nowrap class="'	.	$rowJrev->EJREVEN_class	.	'">'	.	$rowJrev->EJREVEN_nom_fr	.	'</td>';
						// Visible / invisible
						if	($rowJrev->JREVEN_date_fin	<	date("Y-m-d")	||	in_array($rowJrev->FK_EJREVEN_id,	array(1,	4)))	{	// en préparation, en archive
								echo	'<td><img src="../_media/bo_invisible.png" width="16" height="16" border="0" alt="invisible"/></td>';
						}	else	{
								echo	'<td><img src="../_media/bo_visible.png" width="16" height="16" border="0" alt="visible"/></td>';
						}
						// Date jour event + Lieu
						$dates	=	adn_afficheFromDateToDate($rowJrev->JREVEN_date_debut,	$rowJrev->JREVEN_date_fin,	"DB_FR");
						echo	'<td nowrap class="'	.	$classJrevenFull	.	" "	.	$classDepasse	.	'"><b>'	.	$dates[0]	.	", "	.	$rowJrev->LEVEN_nom	.	'</b></td>';
						// montant
						echo	'<td nowrap class="'	.	$classJrevenFull	.	'">'	.	$rowJrev->JREVEN_montant	.	' €</td>';
						// surcout
						if	($configAppli['MENU']['cotisation']	==	"oui")	{
								echo	'<td nowrap class="'	.	$classJrevenFull	.	'">'	.	$rowJrev->JREVEN_surcout	.	' €</td>';
						}
						// occupation
						echo	'<td nowrap class="'	.	$classJrevenFull	.	'">'	.	infoOccupation($rowJrev->JREVEN_places,	$rowJrev->nbreAchats)	.	'</td>';
						// BOUTONS
						// Export de la liste des inscrits
						if	($rowJrev->nbreAchats)	{
								echo	'<td  align="center" class="'	.	$classJrevenFull	.	'">';
								$parametres	=	'jreven_id='	.	$rowJrev->JREVEN_id	.	'&tyheb_id=nul&tyres_id=nul';
								echo	'<a href="../jourevenement/inscrit_csv.php?'	.	$parametres	.	'"><img src="../_media/bo_csv.png" alt="" style="border:0" /></a>';
								echo	'</td>';
						}	else	{
								echo	'<td>&nbsp;</td>';
						}
						// Delete
						if	($autoriseDeleteJrev)	{
								echo	'<td align="center">';
								echo	'<img class="bt_delete_jrevent pointer" src="../_media/bo_delete.png" id_todelete="'	.	$rowJrev->JREVEN_id	.	'" width="16" height="16" border="0"alt=""/>';
								echo	'</td>';
						}
						echo	'</tr>';

						// Ensuite l'hébergement
						$query_heber	=	queryOptionHebergement($rowJrev->JREVEN_id,	$connexion);
						$RS_heber	=	mysql_query($query_heber,	$connexion)	or	die(mysql_error());
						$NbreRows_heber	=	mysql_num_rows($RS_heber);
						if	($NbreRows_heber)	{
								while	($rowHeber	=	mysql_fetch_object($RS_heber))	{
										// Vérifie si l'hébergement est complet
										if	(($rowHeber->nbreAchats	>=	$rowHeber->TYHEB_JREVEN_capacite)	&&	$rowHeber->TYHEB_JREVEN_capacite	!=	0)	{
												$classHeberFull	=	"complet";
										}	else	{
												$classHeberFull	=	"";
										}
										echo	'<tr class="to_highlight" valign="top">';
										if	($autoriseMajJrev)	{	echo	'<td>&nbsp;</td>';	}
										if	($autoriseLog)	{	echo	'<td>&nbsp;</td>';	}
										if	($autoriseCommentJrev)	{	echo	'<td>&nbsp;</td>';	}
										echo	'<td style="border-top:0;" nowrap class="'	.	$rowJrev->EJREVEN_class	.	'">&nbsp;</td>';
										echo	'<td>&nbsp;</td>';	// visible / invisible
										echo	'<td nowrap class="'	.	$classHeberFull	.	'"><span  class="note">+ hébergement :</span> '	.	$rowHeber->TYHEB_nom_fr	.	'</td>';
										echo	'<td nowrap class="'	.	$classHeberFull	.	'">'	.	$rowHeber->TYHEB_JREVEN_montant	.	' €</td>';
										if	($configAppli['MENU']['cotisation']	==	"oui")	{
												echo	'<td style="background-color:#DDDDDD">&nbsp;</td>';
										}
										echo	'<td nowrap class="'	.	$classHeberFull	.	'">'	.	infoOccupation($rowHeber->TYHEB_JREVEN_capacite,	$rowHeber->nbreAchats)	.	'</td>';
										// Export de la liste des inscrits
										if	($rowHeber->nbreAchats)	{
												echo	'<td  align="center" class="'	.	$classHeberFull	.	'">';
												$parametres	=	'jreven_id='	.	$rowHeber->TJ_JREVEN_id	.	'&tyheb_id='	.	$rowHeber->TJ_TYHEB_id	.	'&tyres_id=nul';
												echo	'<a href="../jourevenement/inscrit_csv.php?'	.	$parametres	.	'"><img src="../_media/bo_csv.png" alt="" style="border:0" /></a>';
												echo	'</td>';
										}	else	{
												echo	'<td>&nbsp;</td>';
										}
										// Delete
										if	($autoriseDeleteJrev)	{	echo	'<td>&nbsp;</td>';	}
										echo	'</tr>';
								}
						}

						// Enfin la restauration
						$query_resto	=	queryOptionRestauration($rowJrev->JREVEN_id,	$connexion);
						$RS_resto	=	mysql_query($query_resto,	$connexion)	or	die(mysql_error());
						$NbreRows_resto	=	mysql_num_rows($RS_resto);
						if	($NbreRows_resto)	{
								while	($rowResto	=	mysql_fetch_object($RS_resto))	{
										// Vérifie si l'hébergement est complet
										if	(($rowResto->nbreAchats	>=	$rowResto->TYRES_JREVEN_capacite)	&&	$rowResto->TYRES_JREVEN_capacite	!=	0)	{
												$classRestoFull	=	"complet";
										}	else	{
												$classRestoFull	=	"";
										}
										echo	'<tr class="to_highlight" valign="top">';
										if	($autoriseMajJrev)	{	echo	'<td>&nbsp;</td>';	}
										if	($autoriseLog)	{	echo	'<td>&nbsp;</td>';	}
										if	($autoriseCommentJrev)	{	echo	'<td>&nbsp;</td>';	}
										echo	'<td style="border-top:0;" nowrap class="'	.	$rowJrev->EJREVEN_class	.	'">&nbsp;</td>';
										echo	'<td>&nbsp;</td>';	// visible / invisible
										echo	'<td nowrap class="'	.	$classRestoFull	.	'"><span  class="note">+ restauration :</span> '	.	$rowResto->TYRES_nom_fr	.	'</td>';
										echo	'<td nowrap class="'	.	$classRestoFull	.	'">'	.	$rowResto->TYRES_JREVEN_montant	.	' €</td>';
										if	($configAppli['MENU']['cotisation']	==	"oui")	{
												echo	'<td style="background-color:#DDDDDD">&nbsp;</td>';
										}
										echo	'<td nowrap class="'	.	$classRestoFull	.	'">'	.	infoOccupation($rowResto->TYRES_JREVEN_capacite,	$rowResto->nbreAchats)	.	'</td>';
										// Export de la liste des inscrits
										if	($rowResto->nbreAchats)	{
												echo	'<td  align="center" class="'	.	$classRestoFull	.	'">';
												$parametres	=	'jreven_id='	.	$rowResto->TJ_JREVEN_id	.	'&tyheb_id=nul&tyres_id='	.	$rowResto->TJ_TYRES_id;
												echo	'<a href="../jourevenement/inscrit_csv.php?'	.	$parametres	.	'"><img src="../_media/bo_csv.png" alt="" style="border:0" /></a>';
												echo	'</td>';
										}	else	{
												echo	'<td>&nbsp;</td>';
										}
										if	($autoriseDeleteJrev)	{	echo	'<td>&nbsp;</td>';	}
										echo	'</tr>';
								}
						}
						?>
				<?php	}	?>
		</table>
<?php	}	?>
<ul class="menu_gauche">
		<li class="boutons"><a href="../jourevenement/add.php?idEven=<?php	echo	$row->EVEN_id;	?>&pageNum=<?php	echo	$pageNum	?>">Ajouter un jour <img src="../_media/bo_add.png" width="16" height="16" border="0" align="absmiddle" alt="ajouter un jour"/></a></li>
</ul>