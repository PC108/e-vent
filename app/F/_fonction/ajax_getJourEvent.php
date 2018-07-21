<?php

//DEBUGGAGE
// header("Content-Type: text/html; charset=iso-8859-1");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");
// Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
ini_set('html_errors',	0);

/*	* ******************************************************** */
/*              Connexion DB  + autres            */
/*	* ******************************************************** */
include('_shared_ajax.php');
require_once('../../B/jourevenement/_requete.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('../../B/achat/_requete.php');
require_once('ajax_getHebergement.php');
require_once('ajax_getRestauration.php');
// Traduction
require_once('../../librairie/php/code_adn/AfficheSession.php');
require_once('../../localisation/localisation.php');

/*	* ******************************************************** */
/*              Action                    */
/*	* ******************************************************** */
if	(isset($_POST['FK_EVEN_id']))	{
			$idEvent	=	$_POST['FK_EVEN_id'];
			$checkAchatsEvent	=	false;
			
			// création de la requête
			$query_RS	=	queryJourEvent($idEvent,	$connexion);
			$RS	=	mysql_query($query_RS,	$connexion)	or	die(mysql_error());
			if	(mysql_num_rows($RS)	==	0)	{
						echo	_("Aucune date active pour cet événement.");
			}	else	{

						if	(isset($_SESSION['info_adherent']))	{	// Si on est sur evenement.php
									//Récupère les achats Jour evenement, restauration et hebergement pour cet événement
									if	(isset($_SESSION['info_cmd']))	{
												$query_RSAchats	=	mainQueryAch($_SESSION['info_cmd']['id_cmd'],	$connexion);
												$query_RSAchats	.="WHERE FK_EVEN_id="	.	$idEvent;
												$query_RSAchats	.=" AND FK_TYACH_id IN (3,4,5)";
												$query_RSAchats	.=" AND FK_ADH_id="	.	$_SESSION['info_beneficiaire']['id_benef'];
												$RSAchats	=	mysql_query($query_RSAchats,	$connexion)	or	die(mysql_error());
												if	(mysql_num_rows($RS)	!=	0)	{
															// et les passes dans un array + vérifie s'il y a un achat
															while	($row	=	mysql_fetch_object($RSAchats))	{
																		$checkAchatsEvent[]	=	array($row->FK_JREVEN_id,	$row->FK_TYHEB_id,	$row->FK_TYRES_id);
															}
												}
									}
						}
			}

			// Construit l'html
			$html	=	'<div class="liste_jours">';
			while	($row	=	mysql_fetch_array($RS))	{

						if	(isset($_SESSION['saisie']))	{
									echo	creeHtmlJourEvent($html,	$row,	$checkAchatsEvent, $connexion);
						}	else	{
									if	($row['JREVEN_date_fin']	>=	date("Y-m-d"))	{
												echo	creeHtmlJourEvent($html,	$row,	$checkAchatsEvent, $connexion);
									}
						}
			}
			$html	.=	'</div>';
			return	$html;
}	else	{
			echo	_("bad param !");
}

/**	* ******************************************************* */
/*              Action                    */

/**	* ******************************************************* */
function	creeHtmlJourEvent($html,	$row,	$checkAchatsEvent, $connexion)	{

			if	(in_array($row['FK_EJREVEN_id'],	array(2,	3,	5)) || isset($_SESSION['saisie']))	{	//en vente, en pause, annulé ou mode SAISIE DIRECTE
						$dates	=	adn_afficheFromDateToDate($row['JREVEN_date_debut'],	$row['JREVEN_date_fin'],	"DB_"	.	$_SESSION['lang']);
						// Complet
						if	($row['JREVEN_places']	==	0)	{
									$complet	=	FALSE;
									$strEtatPlaces	=	_("places illimitées");
						}	else	{
									if	($row['nbreAchats']	>=	$row['JREVEN_places'])	{
												$complet	=	TRUE;
									}	else	{
												$complet	=	FALSE;
									}
									$strEtatPlaces	=	sprintf(_("reste %s places sur %s"),	$row['JREVEN_places']	-	$row['nbreAchats'],	$row['JREVEN_places']);
						}
						// Couleur fd Etat sous photo
						if	($row['FK_EJREVEN_id']	==	2	&&	!$complet)	{
									$coulJourEtat	=	"fd_vert";
						}	else	{
									$coulJourEtat	=	"fd_beige";
						}
						// Gratuit
						if	($row['JREVEN_montant']	==	0)	{
									$montantJour	=	_("gratuit");
						}	else	{
									$montantJour	=	$row['JREVEN_montant']	.	" €";
						}
						// Complet
						if	($complet)	{
									$etatJour	=	_("complet");
						}	else	{
									$etatJour	=	$row['EJREVEN_nom_'	.	$_SESSION['lang']];
						}

						$checked	=	'" />';
						$coulPrix	=	'fd_bleu';
						$coulBlocjour	=	'';
						if	(isset($_SESSION['info_adherent']))	{	// Si on est sur evenement.php
									// Si il y a un achat correspondant
									if	($checkAchatsEvent)	{
												foreach	($checkAchatsEvent	as	$value)	{
															if	(($value[0]	==	$row['JREVEN_id'])	&&	is_null($value[1])	&&	is_null($value[2]))	{
																		$checked	=	'" checked="checked" />';
																		$coulPrix	=	'fd_blanc';
																		$coulBlocjour	=	'fd_bleu';
																		break;
															}
												}
									}
						}

						// Construction du bloc HTML
						$html	.=	'<div class="jour_etat  '	.	$coulJourEtat	.	' corner20-tr" style="border-left:0px">'	.	$etatJour	.	'</div>';
						$html	.=	'<div class="jour_etat corner20-bl">'	.	$strEtatPlaces	.	'</div>';
						$html	.=	'<div class="info_jour corner20-all">';
						$html	.=	'<div class="bloc_jour '	.	$coulBlocjour	.	' corner20-all">';
						// N'affiche la case à cocher que si l'état du jourEvent est 'en vente' et pas complet ou mode SAISIE DIRECTE
						if	(($row['FK_EJREVEN_id']	==	2	&&	!$complet) || isset($_SESSION['saisie']))	{
									if	(isset($_SESSION['info_adherent']))	{	// Si on est sur evenement.php
												$html	.=	'<input type="checkbox" class="checkboxJourEvent" id-benef="'	.	$_SESSION['info_beneficiaire']['id_benef']	.	'" id-jrevent="'	.	$row['JREVEN_id']	.	'"  name="'	.	$row['JREVEN_id']	.	'" value="'	.	$row['JREVEN_montant']	.	'" surcout="'	.	$row['JREVEN_surcout']	.	$checked;
									}	else	{
												$html	.=	'<a href="?showid=yes"><img class="checkboxJourEventOK" src="../_media/GEN/checkboxDesactive.png"></a>';
									}
						}	else	{
									$html	.=	'<img class="checkboxJourEventKO" src="../_media/GEN/checkboxLocked.png" style="margin-left:5px">';
						}
						$html	.=	' '	.	$dates[0]	.	' '	.	_("à");
						$html	.=	' <span class="bt_lieu"><b><a href="lieu.php?idlieu='	.	$row['FK_LEVEN_id']	.	'&idjrevent='	.	$row['JREVEN_id']	.	'" class="'	.	$coulBlocjour	.	'">'	.	$row['LEVEN_nom']	.	'</a></b></span>';
						$html	.=	' <span class="prix fd_bleu corner10-all">'	.	$montantJour	.	'</span>';
						$html	.=	'</div>';

						// BLOC HEBERGEMENT
						if	(!is_null($row['isHeber']))	{
									if	(isset($_SESSION['info_adherent']))	{	// Si on est sur evenement.php
												$idHeberChecked	=	0;
												if	($checkAchatsEvent)	{
															foreach	($checkAchatsEvent	as	$value)	{
																		if	(($value[0]	==	$row['JREVEN_id'])	&&	!is_null($value[1])	&&	is_null($value[2]))	{
																					$idHeberChecked	=	$value[1];
																					break;
																		}
															}
												}
												if	($idHeberChecked	>	0)	{	// Ouvre le bloc Hebergement et affiche le choix
															$html	.=	'<div id-jrevent="'	.	$row['JREVEN_id']	.	'" class="bloc_heber fd_bleu corner20-all">';
															// Crée la requête pour les options hébergement
															$query	=	queryOptionHebergement($row['JREVEN_id'],	$connexion);
															$html	.=	getHebergement($query,	$row['JREVEN_id'],	$_SESSION['lang'],	$connexion,	$idHeberChecked);
															$html	.=	'</div>';
												}	else	{	// Affiche l'icone
															$html	.=	'<div id-jrevent="'	.	$row['JREVEN_id']	.	'" class="bloc_heber corner20-all">';
															$html	.=	'<img class="ic_heber pointer" src="../_media/GEN/hebergement1.png" alt=""/>';
															$html	.=	'</div>';
												}
									}	else	{
												// on est sur index.php. N'affiche que l'icone de l'option
												$html	.=	'<div id-jrevent="'	.	$row['JREVEN_id']	.	'" class="bloc_heber corner20-all"><img class="ic_heber" src="../_media/GEN/hebergement1.png" alt=""/></div>';
									}
						}
						// FIN BLOC HEBERGEMENT
						// BLOC RESTAURATION
						if	(!is_null($row['isResto']))	{
									if	(isset($_SESSION['info_adherent']))	{	// Si on est sur evenement.php
												$arrayRestoChecked	=	array();
												if	($checkAchatsEvent)	{
															foreach	($checkAchatsEvent	as	$value)	{
																		if	(($value[0]	==	$row['JREVEN_id'])	&&	is_null($value[1])	&&	!is_null($value[2]))	{
																					$arrayRestoChecked[]	=	$value[2];
																		}
															}
												}
												if	(count($arrayRestoChecked)	>	0)	{	// Ouvre le bloc Hebergement et affiche le choix
															$html	.=	'<div id-jrevent="'	.	$row['JREVEN_id']	.	'" class="bloc_resto fd_bleu corner20-all">';
															// Crée la requête pour les options hébergement
															$query	=	queryOptionRestauration($row['JREVEN_id'],	$connexion);
															$html	.=	getRestauration($query,	$row['JREVEN_id'],	$_SESSION['lang'],	$connexion,	$arrayRestoChecked);
															$html	.=	'</div>';
												}	else	{	// Affiche l'icone
															$html	.=	'<div id-jrevent="'	.	$row['JREVEN_id']	.	'" class="bloc_resto corner20-all">';
															$html	.=	'<img class="ic_resto pointer" src="../_media/GEN/restauration1.png" alt=""/>';
															$html	.=	'</div>';
												}
									}	else	{
												// on est sur index.php. N'affiche que l'icone de l'option
												$html	.=	'<div id-jrevent="'	.	$row['JREVEN_id']	.	'" class="bloc_resto corner20-all "><img class="ic_resto" src="../_media/GEN/restauration1.png" alt=""/></div>';
									}
						}
						// FIN BLOC RESTAURATION
						$html	.=	'</div>';
			}

			return	$html;
}

?>