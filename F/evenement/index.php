<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../B/evenement/_requete.php');
require_once('../../B/adherent/_requete.php');
require_once('../_fonction/setEtatEvent.php');
// Pour les Documents liés
require_once('../../B/upload_doc/_requete.php');

$title	=	_("Les événements");
/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	(isset($_SESSION['info_adherent']))	{	// On redirige l'adhérent sur evenement.php s'il est connecté
			adn_myRedirection("evenement.php");
}

/*	* ******************************************************** */
/*                  No identification                      */
/*	* ******************************************************** */
if	(isset($_GET['showid']))	{
			$showId	=	$_GET['showid'];
}	else	{
			$showId	=	"yes";
}
/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// Création de la requête
$query	=	mainQueryEvent($connexion);
// Si on est mode SAISIE DIRECT, affiche même les évenements vides
if	(!isset($_SESSION['saisie']))	{
			$query	.=	" WHERE compte_display > 0";
			$query	.=	" AND EVEN_prive = 0";
}	else	{
			$query	.=	" WHERE EVEN_prive = 0";
}
$query	.=	" ORDER BY minDate ASC";

$RS1	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$NbreRows_RS1	=	mysql_num_rows($RS1);
// Derniers evenements ouverts
if	(isset($_SESSION['lastOpen']))	{
			$idLastOpen	=	$_SESSION['lastOpen'][0];
}	else	{
			$idLastOpen	=	0;
}

// Liste des acheteurs pour l'auto-complete
$query4	=	listeAdherentsQuery();
$RS4	=	mysql_query($query4,	$connexion)	or	die(mysql_error());
$liste_acheteurs	=	"[";
while	($row_RS4	=	mysql_fetch_object($RS4))	{
			$liste_acheteurs	.=	"{label:\""	.	$row_RS4->ADH_nom	.	" "	.	$row_RS4->ADH_prenom	.	"\",value:\""	.	$row_RS4->ADH_identifiant	.	"\"},";
}
$liste_acheteurs	=	rtrim($liste_acheteurs,	",")	.	"]";

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
						<?php	include('../_shared_js.php');	// Charge les bibliothèques JQUERY, ect. 	?>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.accordion.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.autocomplete.min.js"></script>
						<script  type="text/JavaScript" src="index.js"></script>
						<script type="text/JavaScript" src="evenement.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												//Navigation
												$('#navigation #evenement').addClass('ui-state-active')
												$('#navigation #evenement').button("option", "disabled", true);

												//Accordeon
												$('#accordion').accordion({
															autoHeight: false,
															active: false,
															collapsible: true,
															navigation: true,
															header: '.acc_header',
															icons: false
												});

												$("#user").autocomplete({
															source: <?php	echo	$liste_acheteurs;	?>,
															minLength: 3
												});

												// Initialise le comportement du header de l'accordéon
												INDEX.initAccordeon($('.acc_header'));
												// Recharge les jours pour l'événement ouvert.
												INDEX.openAccordeon(<?php	echo	$idLastOpen	?>, $('#accordion'));

												// Message popup
												INDEX.initInfoPlusAccordeon($('.checkboxJourEventOK'), $('#info_checkboxOK').html(), [-55, 0], ['no', 'no']);
												INDEX.initInfoPlusAccordeon($('.checkboxJourEventKO'), $('#info_checkboxKO').html(), [-35, 0], ['no', 'no']);
												INDEX.initInfoPlusAccordeon($('.ic_heber'), $('#info_heber').html(), [-35, 0], ['no', 'yes']);
												INDEX.initInfoPlusAccordeon($('.ic_resto'), $('#info_resto').html(), [-35, 0], ['no', 'yes']);
												// Icone GMAP
												INDEX.initInfoPlusAccordeon($('.bt_lieu'), $('#info_lieu').html(), [-55, 0], ['no', 'yes']);

												var showid = "<?php	echo	$showId	?>";
												if (showid === "yes") {
															$('#dialog_register').dialog("open");
												}
									});
						</script>
						<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
						<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="global">
									<div id="top"></div>
									<?php	include('../_header.php');	?>
									<?php	include('../_sidebar.php');	?>
									<div id="content" class="corner20-all">
												<h1><?php	echo	_("Liste des événements");	?> (<?php	echo	$NbreRows_RS1;	?>)</h1>
												<?php	if	($NbreRows_RS1	==	0)	{	?>
															<p><i><?php	echo	_("Aucun événements à afficher");	?></i></p>
												<?php	}	else	{	?>
															<div id="accordion">
																		<?php
																		while	($rowEvent	=	mysql_fetch_array($RS1))	{
																					// if	(!$rowEvent['EVEN_prive'])	{	//Enléve les évenements qui sont privés. inutile à cause de la clause WHERE ajoutée en fin de requete
																					$idEvent	=	$rowEvent['EVEN_id'];
																					// Jours à afficher
																					$JoursToDisplay	=	$rowEvent['NbreDisplay'];
																					// if	($JoursToDisplay	>	0)	{ // inutile à cause de la clause WHERE ajoutée en fin de requete
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
																					?>
																					<div id="<?php	echo	$idEvent	?>" class="acc_header">
																								<a href="#<?php	echo	$idEvent	?>">
																											<img src="<?php	echo	$cheminIcone;	?>" width="40px" height="40px" class="icone_event cadre" alt="<?php	echo	$rowEvent['EVEN_nom_'	.	$langue];	?>" />
																											<span><?php	echo	$rowEvent['EVEN_nom_'	.	$langue];	?></span>
																											<span class='text_date'><i><?php	echo	" - "	.	$JoursToDisplay	.	" "	.	_('date(s)')	?></i></span>
																								</a>
																								<div class="header_date text_date"><?php	echo	$infoDates[0];	?></div>
																								<div class="header_etat corner20-tr <?php	echo	$etatEvent	?>"></div>
																					</div>
																					<div>
																								<div class="image_event">
																											<!-- IMAGE  -->
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
																											<!-- DOCUMENTS  -->
																											<?php
																											$query_RS2	=	DocsForThisEvent($idEvent);
																											$query_RS2	.=	"AND DOCEVEN_langue = '"	.	strtoupper($langue)	.	"' ";
																											$query_RS2	.=	"ORDER BY DOCEVEN_nom ASC";
																											$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());
																											$nbreRows_RS2	=	mysql_num_rows($RS2);
																											if	($nbreRows_RS2	>	0)	{
																														?>
																														<div	class	=	"document_event">
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
																											<div class="cadre_info	corner20-all">
																														<h2><?php	echo	$rowEvent['EVEN_nom_'	.	$langue];	?></h2>
																														<p style="margin-left:	35px;	"><?php	echo	nl2br($rowEvent['EVEN_descriptif_'	.	$langue]);	?></p>
																														<!-- LIEN URL -->
																														<?php	if	(!is_null($rowEvent['EVEN_lien']))	{	?>
																																	<p style="margin-left:	35px;	"><a href="<?php	echo	($rowEvent['EVEN_lien']);	?>" target="blank"><?php	echo	_("En savoir plus");	?> <img src="../_media/GEN/ic_web4.png" style="border:0; vertical-align: middle" alt="<?php	echo	($rowEvent['EVEN_lien']);	?>"/></a></p>
																														<?php	}	?>
																														<!-- JOURS -->
																														<!-- Charge le bloc HTML via ajax et _fonction/ajax_getJourEvent.php -->
																														<div class="includeAjax_jours">
																																	<div class="centrer"><img src="../_media/custom/ajax-loader_bloc.gif" /></div>
																														</div>
																											</div>
																								</div>
																					</div>
																		<?php	}	?>
															</div>
												<?php	}	?>
												<div style="clear: left"></div>
									</div>
									<?php	include('../_footer.php');	?>
						</div>
			</body>
			<div id="box_popup" class="popup">--</div>
			<div id="info_lieu" class="invisible"><img src="../_media/GEN/ic_gmap.png" alt="" /><?php	echo	_("plan d'accès");	?></div>
			<div id="info_heber" class="invisible"><?php	echo	_("hébergement possible en option");	?></div>
			<div id="info_resto" class="invisible"><?php	echo	_("restauration possible en option");	?></div>
			<div id="info_checkboxOK" class="invisible"><?php	echo	sprintf(_("Pour vous inscrire à cet événement,%s veuillez vous identifier en cliquant ici."),	"<br />");	?></div>
			<div id="info_checkboxKO" class="invisible"><?php	echo	_("cet événement n'est plus proposé à la vente");	?></div>

			<!-- DIALOG S'INSCRIRE  -->
			<div id="dialog_register" title="S'identifier">
						<p><?php	echo	_("Pour vous inscrire aux événements, vous aurez besoin de vous être identifié sur le site.")	?></p>
						<!-- MESSAGE -->
						<div><?php	include('../_messages.php');	?></div>
						<table>
									<tr style="vertical-align: top;">
												<td  width="320">
															<form id="form_identification" action="../general/login.php" method="post">
																		<!-- Le chemin de retour est dans le header pour rester sur la même page après l'identification -->
																		<input id="chemin" type="hidden" name="chemin" value="<?php	echo	("http://"	.	$_SERVER['HTTP_HOST']	.	$_SERVER['PHP_SELF']);	?>">
																					<?php	if	(!isset($_GET['perdu'])	||	($_GET['perdu']	==	0))	{	?>
																								<div class="corner20-all bloc_identification">
																											<img style ="margin-bottom: 70px;" src="../_media/GEN/ic_account.png" width="32" height="32"/>
																											<div style="text-align: right;">
																														<!--<div class="label_dialog note"><?php	echo	_("identifiant");	?></div>-->
																														<div class="content_dialog">
																																	<input id="user" name="user" value="" type="text" class="identification light" placeholder="Votre identifiant ou nom" size="32"/>
																																	<span class="note"><?php	echo	_("Entrez votre identifiant. Sinon votre nom pour retrouver votre identifiant.");	?></span>
																														</div>
																														<!--<div class="label_dialog note"><?php	echo	_("mot de passe");	?></div>-->
																														<div class="content_dialog"><input id="pwd" name="pwd" value ="" type="password" class="identification light" placeholder="Votre mot de passe" size="32"/></div>
																														<div><input type="submit" name="login" value="<?php	echo	_("s'identifier");	?>" class="bt_submit corner10-all"/></div>
																											</div>
																								</div>
																								<div class="centrer"><a href="?perdu=1"><?php	echo	_("Mot de passe perdu")	?></a></div>
																					<?php	}	else	{	?>
																								<div class="corner20-all bloc_identification">
																											<img style ="margin-bottom: 70px;" src="../_media/GEN/ic_lostpwd.png" width="32" height="32"/>
																											<div style="text-align: right;">
																														<!--<div class="label_dialog note"><?php	echo	_("email");	?></div>-->
																														<div class="content_dialog">
																																	<input id="email_lostpwd" name="email_lostpwd" value ="" type="text" class="identification light" placeholder="Votre email" size="32"/>
																																	<span class="note"><?php	echo	_("Entrez votre adresse pour récupérer vos identifiants par email.");	?></div>
																											</div>
																											<div style="text-align: right;"><input type="submit" name="lostpwd" value="<?php	echo	_("envoyer");	?>" class="bt_submit corner10-all"/></div>
																								</div>

																								<div class="centrer"><a href="?perdu=0"><?php	echo	_("S'inscrire")	?></a></div>
																					<?php	}	?>
															</form>
												</td>
												<td width="10"></td>
												<td>
															<div class="corner20-all bloc_identification centrer">
																		<img src="../_media/GEN/ic_register.png" width="32" height="32"/>
																		<a href="../adherent/inscription.php"><div class="bt_lien corner10-all"><?php	echo	_("S'inscrire")	?></div></a>
															</div>
															<div class="corner20-all bloc_identification">
																		<img style ="margin-bottom: 30px;" src="../_media/GEN/ic_calendar.png" width="32" height="32"/>
																		<div  style="text-align: right;"><a href="?showid=no"><?php	echo	_("Je m'inscrirai plus tard. Je veux d'abord voir les événements proposés sur le site.")	?></a></div>
															</div>
												</td>
									</tr>
						</table>
			</div>
</html>