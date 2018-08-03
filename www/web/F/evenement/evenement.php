<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../B/evenement/_requete.php');
require_once('../../B/commande/_requete.php');
require_once('../../B/achat/_requete.php');
require_once('../_fonction/setEtatEvent.php');
require_once('../_fonction/getInfoOptions.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
// Pour les Documents liés
require_once('../../B/upload_doc/_requete.php');

$title	=	_("Les événements");

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	(!isset($_SESSION['info_adherent']))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../evenement/index.php');
}	else	{
			$idAdh	=	$_SESSION['info_adherent']['id_adh'];
}

/*	* ******************************************************** */
/*              Redirection                 */
/*	* ******************************************************** */
// On force l'adhérent à remplir sa page perso pour sa première connexion
if	(isset($_SESSION['info_adherent'])	&&	$_SESSION['info_adherent']["etat_adh"]	==	2)	{
			$_SESSION['message_user']	=	"first_add";
			adn_myRedirection("../adherent/add.php");
}

/*	* ******************************************************** */
/*              Gestion des variables                     */
/*	* ******************************************************** */
if	(!isset($_SESSION['info_beneficiaire']))	{
			$_SESSION['info_beneficiaire']["id_benef"]	=	$_SESSION['info_adherent']["id_adh"];
			$_SESSION['info_beneficiaire']["nom_benef"]	=	$_SESSION['info_adherent']["nom_adh"];
			$_SESSION['info_beneficiaire']["prenom_benef"]	=	$_SESSION['info_adherent']["prenom_adh"];
}
/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

// Création de la requête pour les événements
$query	=	mainQueryEvent($connexion);
$subquery	=	"";
// Si on est mode SAISIE DIRECT, affiche même les évenements vides
if	(!isset($_SESSION['saisie']))	{
			$subquery	.=	" WHERE compte_display > 0";
}
// Si l'adherent est "privé" affiche les événements privés
if	($_SESSION['info_adherent']['prive_ok']	==	0)	{
			if	($subquery	==	"")	{
						$subquery	.=	" WHERE EVEN_prive = 0";
			}	else	{
						$subquery	.=	" AND EVEN_prive = 0";
			}
}
$subquery	.=	" ORDER BY minDate ASC";
$query	.=	$subquery;

$RSEvent	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$NbreRows_RSEvent	=	mysql_num_rows($RSEvent);

// Derniers evenements ouverts
if	(isset($_SESSION['lastOpen']))	{
			$arrayLastOpen	=	$_SESSION['lastOpen'];
}	else	{
			$arrayLastOpen	=	array();
}

// Création de la requête pour les commandes (vient du BO)
$query	=	mainQueryCmd($connexion);
$query	.=	" WHERE FK_ADH_id=$idAdh AND FK_ECMD_id IN (1,2,4,5,9)";	// (Sans info, En cours, PAYPAL annulé, PAYPAL refusé, PAYPAL sans info)
$RSCmd	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$NbreRows_RSCmd	=	mysql_num_rows($RSCmd);
if	($NbreRows_RSCmd	>	0)	{
			$rowCmd	=	mysql_fetch_object($RSCmd);
			$idCmd	=	$rowCmd->CMD_id;
			$_SESSION['info_cmd']['id_cmd']	=	$idCmd;
			$_SESSION['info_cmd']['ref_cmd']	=	$rowCmd->CMD_ref;
			$_SESSION['info_cmd']['date_cmd']	=	$rowCmd->CMD_date;
			$_SESSION['info_cmd']['total_cmd']	=	$rowCmd->totalCommande;
			$_SESSION['info_cmd']['lien_cmd']	=	$rowCmd->CMD_lien;
// mysql_data_seek($RSCmd, 0);
// Charge les achats pour ouvrir les blocs correspondants
			$query	=	mainQueryAch($idCmd,	$connexion);
			$RSAchat	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			while	($row	=	mysql_fetch_assoc($RSAchat))	{
// Ajoute les blocs à $arrayLastOpen
						if	(!in_array($row['FK_EVEN_id'],	$arrayLastOpen))	{
									$arrayLastOpen[]	=	$row['FK_EVEN_id'];
						}
			}
}	else	{
			unset($_SESSION['info_cmd']);
}

// Garder cet ordre pour les 2 lignes suivantes
$_SESSION['lastOpen']	=	$arrayLastOpen;
$arrayLastOpen	=	json_encode($arrayLastOpen);

/*	* ******************************************************** */
/*              Récupération des descriptions                     */
/*	* ******************************************************** */
// Récupération des descriptions des options hebergement + restauration pour la boite de de dialogue
$arrayInfoOptions	=	getInfoOptions($connexion,	$langue);

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php	echo	$langue;	?>" xml:lang="<?php	echo	$langue;	?>">
			<head>
						<title>e-venement.com | <?php	echo	$title;	?></title>
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<meta http-equiv="Content-Language" content="<?php	echo	$langue;	?>" />
						<link rel="icon" type="image/png" href="../_media/GEN/favicon.png" />
						<!-- JS -->
						<?php	include('../_shared_js.php');	?>
						<script type="text/JavaScript" src="index.js"></script>
						<script type="text/JavaScript" src="evenement.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												// NAVIGATION
												$('#navigation #evenement').addClass('ui-state-active')
												$('#navigation #evenement').button("option", "disabled", true);

												// BLOC EVENT
												// Initialise le comportement du header des blocs événements
												EVENEMENT.initHeadersBlocsEvenement($('.blocEvent_header'));
												// Initialise le comportement des blocs Hebergement et restauration
												EVENEMENT.initHeberResto($('.bloc_heber, .bloc_resto'), '<?php	echo	$langue;	?>');
												// Intialise le comportement des cases à cocher Jour Evenement et charge les blocs Hebergement et Restauration
												// + Gére les sauvegardes AJAX des Jour Evenements
												EVENEMENT.initCheckboxJourEvent($('.checkboxJourEvent'), '<?php	echo	$langue;	?>');
												// Gére les sauvegardes des options Hébergement
												EVENEMENT.initRadioHebergement($('.radioHebergement'));
												// Gére les sauvegardes des options Restauration
												EVENEMENT.initCheckboxRestauration($('.checkboxRestauration'));

												// Ouvre les blocs événements en fonction de la variable de session 'lastOpen' et des achats
												$.each(<?php	echo	$arrayLastOpen;	?>, function(index, value) {
															EVENEMENT.openBlocEvenement($('.blocEvent[id-event=' + value + ']'));
												});

												// Message popup
												INDEX.initInfoPlusAccordeon($('.ic_prive'), $('#info_prive').html(), [-40, 7], ['no', 'no']);

												// Icone GMAP
												INDEX.initInfoPlusAccordeon($('.bt_lieu'), $('#info_lieu').html(), [-57, 0], ['no', 'yes']);

												// Icone Info option + Dialogue
												NS_DIALOG.initAlertDialog($('#alert_info_option'), '<?php	echo	_("ok");	?>');
												INDEX.initInfoPlusAccordeon($(".bt_info_resto, .bt_info_heber"), $('#info_option').html(), [-37, 0], ['no', 'yes']);
												EVENEMENT.initBtInfoOpt($(".bt_info_resto, .bt_info_heber"), $('#box_icinfoopt'), [-38, -8], $('#alert_info_option'));

									});
						</script>
						<!-- CSS -->
						<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
						<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
						<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="global">
									<?php	include('../_header.php');	?>
									<?php	include('../amis/_amis.php');	// A placer avant sidebar.php ?>
									<?php	include('../_sidebar.php');	?>
									<div id="content" class="corner20-all">
												<h1><?php	echo	_("Liste des événements");	?> (<?php	echo	$NbreRows_RSEvent;	?>)</h1>
												<?php	if	($NbreRows_RSEvent	==	0)	{	?>
															<p><i><?php	echo	_("Aucun événements à afficher");	?></i></p>
												<?php	}	else	{	?>
															<!--	EVENEMENTS -->
															<?php
															while	($rowEvent	=	mysql_fetch_array($RSEvent))	{
																		// if	(!$rowEvent['EVEN_prive']	||	($rowEvent['EVEN_prive']	&&	$_SESSION['info_adherent']['prive_ok']))	{ //Enléve les évenements qui sont privés sauf pour les adhérents qui y ont accès
																		// Inutile à cause de la clause WHERE ajoutée en fin de requete
																		$idEvent	=	$rowEvent['EVEN_id'];
																		// Jours à afficher
																		$JoursToDisplay	=	$rowEvent['NbreDisplay'];
																		// if	($JoursToDisplay	>	0)	{ // Inutile à cause de la clause WHERE ajoutée en fin de requete
																		$etatEvent	=	setEtatEvent($rowEvent['NbreEnVente'],	$rowEvent['NbreDesactive'],	$rowEvent['NbreAnnule']);
																		$infoDates	=	adn_afficheFromDateToDate($rowEvent['minDate'],	$rowEvent['maxDate'],	"DB_"	.	$langue);
																		// Charge l'image de l'événement si elle existe
																		if	(file_exists("../../upload/img/crop_"	.	$rowEvent['EVEN_image']))	{
																					$cheminIcone	=	"../../upload/img/ic_"	.	$rowEvent['EVEN_image'];
																					$cheminCrop	=	"../../upload/img/crop_"	.	$rowEvent['EVEN_image'];
																		}	else	{
																					$cheminIcone	=	"../../upload/img/ic_00defaut.jpg";
																					$cheminCrop	=	"../../upload/img/crop_00defaut.jpg";
																		}
																		// Réouvre les blocs déjà ouvert précédemment
																		if	(isset($_SESSION['lastOpen'])	&&	in_array("#"	.	$idEvent,	$_SESSION['lastOpen']))	{
																					$classHeader	=	"ui-state-active  corner20-top";
																					$displayContent	=	"";
																					$getJours	=	TRUE;
																		}	else	{
																					$classHeader	=	"ui-state-default corner20-all";
																					$displayContent	=	"display: none";
																					$getJours	=	FALSE;
																		}
																		?>
																		<a name="<?php	echo	$idEvent	?>"></a>
																		<div class="blocEvent" id-event="<?php	echo	$idEvent	?>">
																					<div class="blocEvent_header <?php	echo	$classHeader	?>">
																								<img src="<?php	echo	$cheminIcone;	?>" width="40px" height="40px" class="icone_event cadre" alt="<?php	echo	$rowEvent['EVEN_nom_'	.	$langue];	?>" />
																								<span class="header_titre">
																											<?php	echo	$rowEvent['EVEN_nom_'	.	$langue];	?>
																											<?php	if	($rowEvent['EVEN_prive'])	{	?><img class="ic_prive" src="../_media/GEN/locked_alpha.png" width="16" height="16" border="0" alt=""/><?php	}	?>
																								</span>
																								<span class='text_date'>
																											<i><?php	echo	" - "	.	$JoursToDisplay	.	" "	.	_('date(s)')	?></i>
																											<i><?php	if	($rowEvent['EVEN_pleintarif'])	{	echo	" - "	.	_('plein tarif');	}	?></i>
																								</span>
																								<div class="header_date text_date"><?php	echo	$infoDates[0];	?></div>
																								<div class="header_etat corner20-tr <?php	echo	$etatEvent	?>"></div>
																					</div>
																					<div class="blocEvent_content corner20-bottom" style="padding:20px; <?php	echo	$displayContent	?>">
																								<!-- IMAGE  -->
																								<div class="image_event">
																											<div class="cadre_image corner20-bottom <?php	if	($etatEvent	==	"prodbad")	{	echo	'fd_beige';	}	else	{	echo	'fd_vert';	}	?>">
																														<img src="<?php	echo	$cheminCrop;	?>" width="240px" height="240px" alt="<?php	echo	$rowEvent['EVEN_nom_'	.	$langue];	?>" />
																														<div class="text_Etat <?php	echo	$etatEvent	?>" style="background-position: 0 -3px">
																																	<?php
																																	echo	$JoursToDisplay	.	" "	.	_('date(s) disponibles')	.	"<br />";
																																	if	($rowEvent['NbreDesactive']	>	0)	{	echo	_("dont")	.	" "	.	$rowEvent['NbreDesactive']	.	" "	.	_("date(s) désactivée(s)")	.	"<br />";	}
																																	if	($rowEvent['NbreAnnule']	>	0)	{	echo	_("dont")	.	" "	.	$rowEvent['NbreAnnule']	.	" "	.	_("date(s) annulée(s)");	}
																																	?>
																														</div>
																											</div>
																											<!-- PLEIN TARIF  -->
																											<?php	if	($rowEvent['EVEN_pleintarif'])	{	?>
																														<div class="widget_event cadre_info corner20-all">
																																	<h2><img src="../_media/GEN/cotisation_bad.png"/> <?php	echo	_("Plein tarif uniquement")	?></h2>
																																	<div class="note"><?php	echo	_("Cet événement ne prend pas en compte les tarifs réduits dont vous pourriez bénéficier dans vos infos personnelles.")	?></div>
																														</div>
																											<?php	}	?>
																											<!-- DOCUMENTS  -->
																											<?php
																											$query_RS2	=	DocsForThisEvent($idEvent);
																											$query_RS2	.=	"AND DOCEVEN_langue = '"	.	strtoupper($langue)	.	"' ";
																											$query_RS2	.=	"ORDER BY DOCEVEN_nom ASC";
																											$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());
																											$nbreRows_RS2	=	mysql_num_rows($RS2);
																											if	($nbreRows_RS2	>	0)	{
																														?>
																														<div	class	=	"widget_event">
																																	<div class="jour_etat	corner20-tr	corner20-bl"><?php	echo	_("Documents à télécharger");	?></div>
																																	<div class="info_jour 	corner20-all">
																																				<ul style="padding: 10px 10px 0 20px;">
																																							<?php	while	($row_RS2	=	mysql_fetch_object($RS2))	{	?>
																																										<li><a href="../../upload/doc/<?php	echo	$row_RS2->DOCEVEN_file;	?>" target="blank"><?php	echo	$row_RS2->DOCEVEN_nom;	?></a> <img src="../../F/_media/GEN/ic_<?php	echo	$row_RS2->DOCEVEN_type;	?>.png" style="vertical-align: middle"></li>
																																							<?php	}	?>
																																				</ul>
																																	</div>
																														</div>
																											<?php	}	?>
																								</div>
																								<div class="content_event">
																											<!-- TITRE + DESCRIPTION  -->
																											<form id="event_<?php	echo	$rowEvent['EVEN_id'];	?>" action="action.php" method="post">
																														<div class="cadre_info corner20-all">
																																	<h2><?php	echo	$rowEvent['EVEN_nom_'	.	$langue];	?></h2>
																																	<p style="margin-left: 35px;"><?php	echo	nl2br($rowEvent['EVEN_descriptif_'	.	$langue]);	?></p>
																																	<!-- LIEN URL -->
																																	<?php	if	(!is_null($rowEvent['EVEN_lien']))	{	?>
																																				<p style="margin-left: 35px;"><a href="<?php	echo	($rowEvent['EVEN_lien']);	?>" target="blank"><?php	echo	_("En savoir plus");	?> <img src="../_media/GEN/ic_web4.png" style="border:0; vertical-align: middle" alt="<?php	echo	($rowEvent['EVEN_lien']);	?>"/></a></p>
																																	<?php	}	?>
																																	<!-- JOURS -->
																																	<!-- Charge le bloc HTML via ajax et _fonction/ajax_getJourEvent.php -->
																																	<div class="includeAjax_jours">
																																				<div class="centrer"><img src="../_media/custom/ajax-loader_bloc.gif" /></div>
																																	</div>
																														</div>
																											</form>
																								</div>
																								<div style="clear: left"></div>
																					</div>
																		</div>
															<?php	}	?>
															<div style="text-align:right"><a href="../commande/result.php"><div class="bt_lien corner10-all espace10"><?php	echo	_("voir le détail de la commande & payer");	?></div></a></div>
												<?php	}	?>
									</div>
									<?php	include('../_footer.php');	?>
						</div>
			</body>
			<div id="box_popup" class="popup">--</div>
			<div id="info_option" class="invisible"><?php	echo	_("en savoir plus sur cette option");	?></div>
			<div id="info_lieu" class="invisible"><img src="../_media/GEN/ic_gmap.png" alt="" /><?php	echo	_("plan d'accès");	?></div>
			<div id="info_prive" class="invisible"><?php	echo	_("événement privé");	?></div>
			<div id="alert_info_option" title="<?php	echo	_("Informations sur les options");	?>">
						<?php
						foreach	($arrayInfoOptions	as	$key	=>	$value)	{
									if	(is_null($value[1])	||	$value[1]	==	"")	{
												$content	=	"<i>"	.	_("aucune description pour cette option.")	.	"</i>";
									}	else	{
												$content	=	nl2br($value[1]);
									}
									$html	=	'<p class="'	.	$key	.	' optcontent invisible"><u>'	.	$value[0]	.	'</u></p>';
									$html	.=	'<p class="'	.	$key	.	' optcontent invisible" style="padding-left: 30px">'	.	$content	.	'</p>';
									echo	$html;
						}
						?>
			</div>
</html>