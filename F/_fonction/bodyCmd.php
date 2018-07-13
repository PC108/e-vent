<?php

require_once('../../librairie/php/code_adn/Compare2Dates.php');

/** htmlBodyCmd
	* Met en forme une commande et renvoie le code HTML.
	* Utilisé dans la page commande/result.php et commande/ptrint.php
	*
	* @param <array> $infoClient = Information du client stockée en session
	* @param <ressource> $RSCmd = resultat de la requete sur la table des commandes
	* @param <ressource> $RSAchat = resultat de la requete sur la table des achats
	* @param <string> $langue = langue de l'interface
	* @param <boolean> $delete = Si il faut afficher le bouton pour supprimer un achat (oui pour result.php, non pour print.php)
	* @param <array> $configAppli = tableau de la config en YAML
	*/
function	htmlBodyCmd($infoClient,	$RSCmd,	$RSAchat,	$langue,	$delete,	$configAppli)	{

			/* Bloc Prestataire */
			$html	=	'<div class="bloc_print corner20-all agauche">';
			$html	.=	'<h2>'	.	_("Prestataire")	.	'</h2>';
			$html	.=	'<p><b>'	.	$infoClient['nom']	.	'</b></p>';
			$html	.=	'<p>'	.	$infoClient['adr1'];
			if	($infoClient['adr2']	!=	"")	{
						$html	.=	'<br />'	.	$infoClient['adr2'];
			}
			$html	.=	'<br />'	.	$infoClient['zip']	.	' '	.	$infoClient['ville'];
			$html	.=	'<br />'	.	$infoClient['pays']	.	'</p>';
			$html	.=	'<p><a href="mailto:'	.	$infoClient['email_contact']	.	'">'	.	$infoClient['email_contact']	.	'</a>';
			$html	.=	'<br />'	.	$infoClient['tel']	.	'</p>';
			$html	.=	'</div>';

			/* Bloc Commanditaire */
			$rowCmd	=	mysql_fetch_array($RSCmd);
			$html	.=	'<div class="bloc_print corner20-all adroite">';
			$html	.=	'<h2>'	.	_("Commanditaire")	.	'</h2>';
			$html	.=	'<p><b>'	.	$rowCmd['ADH_prenom']	.	" "	.	$rowCmd['ADH_nom']	.	'</b></p>';
			$html	.=	'<p>'	.	$rowCmd['ADH_adresse1'];
			if	($rowCmd['ADH_adresse2']	!=	"")	{
						$html	.=	'<br />'	.	$rowCmd['ADH_adresse2'];
			}
			$html	.=	'<br />'	.	$rowCmd['ADH_zip']	.	' '	.	$rowCmd['ADH_ville'];
			$html	.=	'<br />'	.	$rowCmd['PAYS_nom_'	.	$_SESSION['lang']]	.	'</p>';
			$html	.=	'<p><a href="mailto:'	.	$rowCmd['ADH_email']	.	'">'	.	$rowCmd['ADH_email']	.	'</a>';
			$html	.=	'<br /><span class="commentaire">'	.	_('Identifiant')	.	"</span> "	.	$rowCmd['ADH_identifiant']	.	'</p>';
			$html	.=	'</div>';
			$html	.=	'<div style="clear:left"></div>';

			/* Bloc Commande */
			$html	.=	'<div class="bloc_print corner20-all">';
			if	(in_array($rowCmd['FK_ECMD_id'],	array(3,	6,	10)))	{
						$commande	=	FALSE;
						$html	.=	'<h2>'	.	_("Facture")	.	'</h2>';
			}	else	{
						$commande	=	TRUE;
						$html	.=	'<h2>'	.	_("Commande")	.	'</h2>';
			}
			$html	.=	'<div class="agauche"><span class="commentaire">'	.	_('Référence')	.	' </span>'	.	$rowCmd['CMD_ref'];
			$html	.=	'<br /><span class="commentaire">'	.	_('Etat')	.	' </span>'	.	$rowCmd['ECMD_description_'	.	$langue];
			if	((!$commande))	{
						$html	.=	'<br /><span class="commentaire">'	.	_('Mode de paiement')	.	' </span>'	.	$rowCmd['MDPAY_nom_'	.	$langue];
			}
			$html	.=	'</div>';
			$html	.=	'<div class="adroite"><span class="commentaire">'	.	_('Créée le')	.	' </span>'	.	adn_changeFormatDate($rowCmd['CMD_date'],	'DB_'	.	$langue)	.	'</div>';
			$html	.=	'<div style="clear:left"></div>';
			$html	.=	'</div>';

			/* Bloc 1 Adherent */
			$OldAdhId	=	0;
			$OldEventId	=	0;
			$CloseBloc	=	FALSE;
			$txtComplet	=	_("Cet achat est aujourd'hui complet, dépassé ou désactivé par l'organisateur. Veuillez le supprimer de votre commande avant de payer.");

			while	($rowAch	=	mysql_fetch_array($RSAchat))	{

						/* Ferme le bloc */
						if	($OldAdhId	!=	0	&&	$OldAdhId	!=	$rowAch['FK_ADH_id'])	{
									$html	.=	'</div>';
						}

						/* QUEL BENEFICIARE */
						if	($OldAdhId	!=	$rowAch['FK_ADH_id'])	{
									$OldAdhId	=	$rowAch['FK_ADH_id'];
									/* Vérifie si le bénéficiaire de l'achat a payé sa cotisation */
									if	($configAppli['MENU']['cotisation']	==	"oui")	{
												$anneeCotisation	=	$rowAch['ADH_annee_cotisation'];
												if	($anneeCotisation	==	date('Y'))	{
															$msgCotisation	=	_("Cotisation à jour")	.	"*";
												}	else	{
															$msgCotisation	=	_("Cotisation en attente de réception*");
												}
									}	else	{
												$msgCotisation	=	FALSE;
									}

									$html	.=	'<div class="bloc_print corner20-all">';
									$html	.='<h2>'	.	$rowAch['ADH_prenom']	.	' '	.	$rowAch['ADH_nom'];
									if	($msgCotisation)	{	$html	.='<span class="commentaire"> ('	.	$msgCotisation	.	')</span>';	}
									$html	.='</h2>';
						}	else	{
									$CloseBloc	=	TRUE;
						}

						/* QUEL EVENEMENT */
						if	($OldEventId	!=	$rowAch['FK_EVEN_id'])	{
									$OldEventId	=	$rowAch['FK_EVEN_id'];
									$html	.='<h4>'	.	$rowAch['EVEN_nom_'	.	$langue];
									if	($rowAch['EVEN_pleintarif'])	{	$html	.='<span class="commentaire"> ('	.	_('Evénement plein tarif')	.	')</span>';	}
									$html	.='</h4>';
						}

						/* QUEL ACHAT */
						/* Affichage du ratio (fonctionne pour jour événement et options */
						if	($rowAch['ACH_ratio']	!=	100)	{
									$strRatio	=	'<span class="note">('	.	$rowAch['ACH_ratio']	.	"% "	.	_('de')	.	" "	.	$rowAch['ACH_montant']	.	" €)&nbsp&nbsp</span>";
									$strRatioSurcout	=	'<span class="note">('	.	$rowAch['ACH_ratio']	.	"% "	.	_('de')	.	" "	.	$rowAch['ACH_surcout']	.	" €)&nbsp&nbsp</span>";
						}	else	{
									$strRatio	=	"";
									$strRatioSurcout	=	"";
						}
						switch	($rowAch['FK_TYACH_id'])	{

									case	1:	//COTISATION
									case	2	:	// DON
												if	($rowAch['FK_TYACH_id']	==	1)	{
															$infoType	=	$rowAch['TYCOT_nom_'	.	$langue];
												}	else	{
															$infoType	=	$rowAch['TYDON_nom_'	.	$langue];
												}
												/* Ligne de prix pour un don ou une cotisation */
												$html	.='<div class="ligne_prix">';
												$html	.=	$rowAch['TYACH_nom_'	.	$langue]	.	'<span class="commentaire"> '	.	$infoType	.	'</span>';
												$html	.=	'<span class="adroite">'	.	$rowAch['ACH_montant']	.	' €';
												if	($delete)	{
															$html	.=	' <img src="../_media/GEN/ic_delete.png" alt="" id_adh="'	.	$rowAch['FK_ADH_id']	.	'" id_achat="'	.	$rowAch['ACH_id']	.	'" id_typeachat="'	.	$rowAch['FK_TYACH_id']	.	'">';
												}
												$html	.=	'</span></div>';
												break;

									case	3:	// JOUR EVENEMENT
												/* Vérification si le jour événement est complet, dépassé, désactivé ou annulé. */
												if	(
																				$commande	&&	(($rowAch['JREVEN_places']	!=	0)	&&	($rowAch['nbreAchatsJREVEN']	>	$rowAch['JREVEN_places']))	||	(compare2dates($rowAch['JREVEN_date_fin'],	"<",	"today"))	||	($rowAch['FK_EJREVEN_id']	!=	2)	// en vente
												)	{
															$classComplet	=	"complet";
															$strComplet	=	"<br /><i>"	.	$txtComplet	.	"</i>";
												}	else	{
															$classComplet	=	"";
															$strComplet	=	"";
												}
												/* Ligne de prix pour un jour événement */
												$infoDates	=	adn_afficheFromDateToDate($rowAch['JREVEN_date_debut'],	$rowAch['JREVEN_date_fin'],	"DB_"	.	$langue);
												$html	.=	'<p class="ligne_prix '	.	$classComplet	.	'">';
												$html	.=	'<span class="adroite">'	.	$strRatio	.	" "	.	adn_enDecimal(($rowAch['ACH_montant']	*	$rowAch['ACH_ratio'])	/	100)	.	' €';
												if	($delete)	{
															$html	.=	' <img src="../_media/GEN/ic_delete.png" alt="" id_adh="'	.	$rowAch['FK_ADH_id']	.	'" id_jreven="'	.	$rowAch['FK_JREVEN_id']	.	'" id_typeachat="'	.	$rowAch['FK_TYACH_id']	.	'" >';
												}
												$html	.=	'</span>';
												$html	.=	'<b>'	.	$rowAch['LEVEN_nom']	.	'</b>, '	.	$infoDates[0]	.	$strComplet;
												$html	.=	'</p>';
												/* Ligne de prix pour une cotisation ponctuelle */
												if	($rowAch['ACH_surcout']	>	0)	{
															$html	.=	'<p class="ligne_prix"><i>'	.	_('Cotisation ponctuelle')	.	"*";
															$html	.=	'<span class="adroite">'	.	$strRatioSurcout	.	" "	.	adn_enDecimal(($rowAch['ACH_surcout']	*	$rowAch['ACH_ratio'])	/	100)	.	' €';
															if	($delete)	{
																		$html	.=	'<span class="adroite" style="margin-right:21px">&nbsp;</span>';
															}
															$html	.=	'</span></i></p>';
												}
												break;

									case	4:	// OPTION HEBERGEMENT
									case	5:	// OPTION RESTAURATION
												/* Vérification si aucune option est complète */
												if	(
																				!$commande	||
																				($rowAch['FK_TYACH_id']	==	4	&&	(($rowAch['nbreAchatsTYHEB']	<	$rowAch['TYHEB_JREVEN_capacite'])	||	$rowAch['TYHEB_JREVEN_capacite']	==	0))	||
																				($rowAch['FK_TYACH_id']	==	5	&&	(($rowAch['nbreAchatsTYRES']	<	$rowAch['TYRES_JREVEN_capacite'])	||	$rowAch['TYRES_JREVEN_capacite']	==	0)))	{
															$classComplet	=	"";
															$strComplet	=	"";
												}	else	{
															$classComplet	=	"complet";
															$strComplet	=	"<br /><i>"	.	$txtComplet	.	"</i>";
												}
												/* Ligne de prix pour une option hébergement ou restauration */
												$html	.=	'<p class="ligne_prix '	.	$classComplet	.	'">';
												$html	.=	'<span class="adroite">'	.	$strRatio	.	" "	.	adn_enDecimal(($rowAch['ACH_montant']	*	$rowAch['ACH_ratio'])	/	100)	.	' €';
												if	($delete)	{
															$html	.=	' <img src="../_media/GEN/ic_delete.png" alt="" id_adh="'	.	$rowAch['FK_ADH_id']	.	'" id_achat="'	.	$rowAch['ACH_id']	.	'" id_typeachat="'	.	$rowAch['FK_TYACH_id']	.	'">';
												}
												$html	.=	'</span>';
												$html	.=	'<span class="commentaire"> '	.	$rowAch['TYACH_nom_'	.	$langue]	.	' : </span>'	.	$rowAch['TYHEB_nom_'	.	$langue]	.	$rowAch['TYRES_nom_'	.	$langue]	.	$strComplet;
												$html	.='</p>';
												break;
						}
			}

			$html	.=	'</div>';
			if	($configAppli['MENU']['cotisation']	==	"oui")	{
						$html	.=	'<p style="margin-left: 20px;" class="note">'	.	_("* Une cotisation ponctuelle peut être ajoutée à un achat si la cotisation annuelle n'est pas à jour.")	.	"</p>";
			}
			/* Bloc Commande */
			$html	.=	'<div class="bloc_print corner20-all adroite">';
			if	($rowCmd['CMD_remise']	>	0)	{
						$html	.=	'<p>'	.	_('Remise exceptionelle')	.	' : - '	.	$rowCmd['CMD_remise']	.	' €';
			}
			$html	.=	'<p><b>'	.	_('Total')	.	' : '	.	$rowCmd['totalCommande']	.	' €</b>';
			$html	.=	'</p>';
			$html	.=	'</div>';
			$html	.=	'<div style="height:80px"></div>';

			return	$html;
}

?>