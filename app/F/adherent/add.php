<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

$title	=	_("Mes informations personnelles");

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../evenement/index.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$query	=	"
		SELECT *,
		(SELECT TYTAR_ratio FROM t_typetarif_tytar WHERE TYTAR_id = FK_TYTAR_id) AS TYTAR_ratio
    FROM t_adherent_adh
    LEFT JOIN t_newsletter_news ON NEWS_email=ADH_email
    WHERE ADH_id='"	.	$_SESSION['info_adherent']['id_adh']	.	"'";
$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
$row	=	mysql_fetch_object($RS);

$id	=	$row->ADH_id;
$eadh	=	$row->FK_EADH_id;
$nom	=	$row->ADH_nom;
$prenom	=	$row->ADH_prenom;
$identifiant	=	$row->ADH_identifiant;
$password	=	$row->ADH_password;
$genre	=	$row->ADH_genre;
$anneeN	=	$row->ADH_annee_naissance;
$anneeC	=	$row->ADH_annee_cotisation;
if	(is_null($anneeC))	{	$anneeC	=	_("aucune");	}
$langueAdh	=	$row->ADH_langue;
$idTytar	=	$row->FK_TYTAR_id;
$email	=	$row->ADH_email;
$newsletter	=	$row->NEWS_email;
$tel	=	$row->ADH_telephone;
$port	=	$row->ADH_portable;
$adr1	=	$row->ADH_adresse1;
$adr2	=	$row->ADH_adresse2;
$zip	=	$row->ADH_zip;
$ville	=	$row->ADH_ville;
$idPays	=	$row->FK_PAYS_id;
$ordination	=	$row->ADH_ordination;
$nomDharma	=	$row->ADH_nom_dharma;
$benevoleOk	=	$row->ADH_benevolat;
$profession	=	$row->ADH_profession;
$disponibilite	=	$row->ADH_disponibilite;

// Actualise les infos de $_SESSION['info_adherent'] si on vient de faire une maj via la page action.php
if	(isset($_GET['maj']))	{
			initSessionAdh($row);
}

// Mise en forme des infos de cotisation (à laisser après l'initialisation de $_SESSION['info_adherent'])
$arrayInfoCotisation	=	checkCotisation();

$query_RS2	=	"SELECT * FROM t_pays_pays ORDER BY PAYS_tri_"	.	$langue	.	" ASC";
$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());

// Affiche aussi le type de tarif non visible si il a été assaigné à cette personne
$query_RS3	=	"
		SELECT * FROM
		(SELECT *,
		(SELECT FK_TYTAR_id FROM t_adherent_adh WHERE ADH_id = "	.	$_SESSION['info_adherent']['id_adh']	.	") As currentTypeTarif
		FROM t_typetarif_tytar ) AS typeTarif
		WHERE TYTAR_visible=1
		OR TYTAR_id=currentTypeTarif
		ORDER BY TYTAR_ordre ASC";
$RS3	=	mysql_query($query_RS3,	$connexion)	or	die(mysql_error());
$nbr_tytar_visible	=	mysql_num_rows($RS3);

$query_RS4	=	"SELECT * FROM t_competence_cmpt WHERE CMPT_visible=1 ORDER BY CMPT_nom_"	.	$langue	.	" ASC";
$RS4	=	mysql_query($query_RS4,	$connexion)	or	die(mysql_error());
$nbr_competence	=	mysql_num_rows($RS4);

$query_RS5	=	"SELECT CMPT_id FROM t_competence_cmpt LEFT JOIN tj_adh_cmpt ON TJ_CMPT_id=CMPT_id WHERE TJ_ADH_id='"	.	$_SESSION['info_adherent']['id_adh']	.	"'";
$RS5	=	mysql_query($query_RS5,	$connexion)	or	die(mysql_error());
$cmpt	=	array();
while	($cmptLignes	=	mysql_fetch_array($RS5))	{
			array_push($cmpt,	$cmptLignes['CMPT_id']);
}
/*	* ******************************************************** */
/*              Navigation                    */
/*	* ******************************************************** */
// Permet de désactiver les boutons de la barre de navigation selon qu'on accéde à la page avec une inscription complète
// Voir en suivant le script Javascript
if	(isset($_SESSION['info_adherent'])	&&	$_SESSION['info_adherent']['etat_adh']	>	2)	{
			$disableInfoPerso	=	'oui';
}	else	{
			$disableInfoPerso	=	'non';
}

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
						<script type="text/javascript">
									$(document).ready(function() {
												//Navigation
												var disableInfoPerso = '<?php	echo	$disableInfoPerso	?>';
												if (disableInfoPerso == 'oui') {
															$('#navigation #info_pers').addClass('ui-state-active')
															$('#navigation #info_pers').button( "option", "disabled", true );
												} else {
															$('#navigation #evenement').addClass('ui-state-active')
															$('#navigation #evenement').button( "option", "disabled", true );
												}

												//Option de formulaire
												NS_UTIL.displayFormOpt('#ADH_benevolat', '#opt_benevolat');
												$('#ADH_benevolat').change (function() {
															NS_UTIL.openFormOpt('#ADH_benevolat', '#opt_benevolat');
												});
												//Validation
												$("#maj").validate({
															rules: {
																		'ADH_genre': {
																					required: true
																		},
																		'ADH_nom': {
																					required: true,
																					minlength: 2
																		},
																		'ADH_prenom': {
																					required: true,
																					minlength: 2
																		},
																		'ADH_password': {
																					required: true,
																					minlength: 4,
																					maxlength: 16
																		},
																		'ADH_password_confirm': {
																					required: true,
																					equalTo: "#ADH_password"
																		},
																		'ADH_annee_naissance': {
																					range: [1900, new Date().getFullYear()]
																		},
																		'ADH_email': {
																					required: true,
																					email: true
																		},
																		'ADH_langue': {
																					required: true
																		},
																		'ADH_telephone': {
																					required: true,
																					minlength: 2
																		},
																		'ADH_adresse1': {
																					required: true,
																					minlength: 2
																		},
																		'ADH_zip': {
																					required: true,
																					minlength: 2
																		},
																		'ADH_pays': {
																					required: true
																		},
																		'ADH_ville': {
																					required: true,
																					minlength: 2
																		},
																		'ADH_profession': {
																					required: function() {
																								if($("#ADH_benevolat").attr('checked')) {
																											return true;
																								} else {
																											return false;
																								}
																					}
																		},
																		'ADH_disponibilite': {
																					required: function() {
																								if($("#ADH_benevolat").attr('checked')) {
																											return true;
																								} else {
																											return false;
																								}
																					}
																		}
															}
												});
									});

						</script>
						<!-- CSS -->
						<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
						<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
						<link href="../../librairie/js/jquery/ui_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_front.css" rel="stylesheet" type="text/css" />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="global">
									<?php	include('../_header.php');	?>
									<?php	include('../_sidebar.php');	?>
									<div id="content" class="corner20-all">
												<h1><?php	echo	$title;	?></h1>
												<form id="maj" class="formulaire corner20-all" action="action.php" method="post">
															<?php	if	($configAppli['MENU']['cotisation']	==	"oui")	{	?>
																		<h3><?php	echo	_("Cotisation");	?></h3>
																		<div class="hr_form">
																					<div class="label_form"><?php	echo	_("Année de cotisation");	?></div>
																					<div class="content_form" >
																								<img src="<?php	echo	$arrayInfoCotisation[1]	?>" alt="" style="vertical-align: middle"/> <?php	echo	$anneeC;	?>
																								<div class="note"><a href="../cotisation/add.php"><?php	echo	$arrayInfoCotisation[2];	?></a>
																								</div>
																					</div>
																		</div>
															<?php	}	?>
															<h3><?php	echo	_("Informations Personnelles");	?></h3>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Genre");	?></div>
																		<div class="content_form">
																					<input type="radio" id="ADH_genre" name="ADH_genre" value="H" <?php	if	(!(strcmp("H",	$genre)))	{	echo	"CHECKED";	}	?> class="required"/> <?php	echo	_("Mr");	?>
																					<input type="radio" id="ADH_genre" name="ADH_genre" value="F" <?php	if	(!(strcmp("F",	$genre)))	{	echo	"CHECKED";	}	?> class="required"/> <?php	echo	_("Mme");	?>
																					<label for="ADH_genre" class="error" style="display:none"><?php	echo	_("Au moins une sélection requise.");	?></label>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Nom");	?></div>
																		<div class="content_form">
																					<input id="ADH_nom" name="ADH_nom" type="text" value="<?php	echo	htmlspecialchars($nom);	?>" size="30" class="required"/>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Prénom");	?></div>
																		<div class="content_form">
																					<input id="ADH_prenom" name="ADH_prenom" type="text" value="<?php	echo	htmlspecialchars($prenom);	?>" size="30" class="required"/>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form"><?php	echo	_("Identifiant");	?></div>
																		<div class="content_form"><?php	echo	($identifiant);	?></div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Mot de passe");	?></div>
																		<div class="content_form">
																					<input id="ADH_password" name="ADH_password" type="password" value="<?php	echo	($password);	?>" size="30" class="required"/>
																					<div class="note"><?php	echo	_("Entre 4 et 16 caractères.");	?></div>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Confirmation du mot de passe");	?></div>
																		<div class="content_form">
																					<input id="ADH_password_confirm" name="ADH_password_confirm" type="password" value="<?php	echo	($password);	?>" size="30" class="required"/>
																					<div class="note"><?php	echo	_("Entre 4 et 16 caractères.");	?></div>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form"><?php	echo	_("Année de naissance");	?></div>
																		<div class="content_form"><input id="ADH_annee_naissance" name="ADH_annee_naissance" type="text" value="<?php	echo	($anneeN);	?>" size="30"/></div>
															</div>
															<?php	if	($nbr_tytar_visible	>	1)	{	?>
																		<div class="hr_form">
																					<div class="label_form required"><?php	echo	_("Type Tarif");	?></div>
																					<div class="content_form">
																								<ul class="liste_add">
																											<?php	while	($row_RS3	=	mysql_fetch_assoc($RS3))	{	?>
																														<li><input type="radio" id="FK_TYTAR_id" name="FK_TYTAR_id" value="<?php	echo	$row_RS3['TYTAR_id']	?>" <?php	if	(!(strcmp($row_RS3['TYTAR_id'],	$idTytar)))	{	echo	"CHECKED";	}	?> class="required"/><?php	echo	$row_RS3['TYTAR_nom_'	.	$langue]	.	" ("	.	$row_RS3['TYTAR_ratio']	.	"%)";	?></li>
																														<li class="note" style="padding-left: 20px;"><?php	echo	$row_RS3['TYTAR_description_'	.	$langue];	?></li>
																											<?php	}	?>
																								</ul>
																					</div>
																		</div>
															<?php	}	?>
															<h3><?php	echo	_("Communication");	?></h3>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Email");	?></div>
																		<div class="content_form">
																					<input id="ADH_email" name="ADH_email" type="text" value="<?php	echo	htmlspecialchars($email);	?>" size="30" class="required"/>
																					<input type="hidden" id="old_email" name="old_email" value="<?php	echo	($email);	?>" size="30"/>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form"><?php	echo	_("Newsletter");	?></div>
																		<div class="content_form">
																					<input type="checkbox" id="NEWS_email"  name="NEWS_email" value="" <?php	if	(!is_null($newsletter))	{	echo	"CHECKED";	}	?>/>
																					<div class="note"><?php	echo	_("A cocher si vous voulez être inscrit à la newsletter.");	?></div>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Langue");	?></div>
																		<div class="content_form">
																					<input type="radio" id="ADH_langue" name="ADH_langue" value="FR" <?php	if	(!(strcmp("FR",	$langueAdh)))	{	echo	"CHECKED";	}	?> class="required"/> <?php	echo	_("Français");	?>
																					<input type="radio" id="ADH_langue" name="ADH_langue" value="EN" <?php	if	(!(strcmp("EN",	$langueAdh)))	{	echo	"CHECKED";	}	?> class="required"/> <?php	echo	_("Anglais");	?>
																					<label for="ADH_langue" class="error" style="display:none"><?php	echo	_("Au moins une sélection requise.");	?></label>
																					<div class="note"><?php	echo	_("Correspond à la langue avec laquelle vous communiquez le plus facilement.");	?></div>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Téléphone");	?></div>
																		<div class="content_form"><input id="ADH_telephone" name="ADH_telephone" type="text" value="<?php	echo	htmlspecialchars($tel);	?>" size="30" class="required"/></div>
															</div>
															<div class="hr_form">
																		<div class="label_form"><?php	echo	_("Portable");	?></div>
																		<div class="content_form"><input id="ADH_portable" name="ADH_portable" type="text" value="<?php	echo	htmlspecialchars($port);	?>" size="30"/></div>
															</div>
															<h3><?php	echo	_("Adresse");	?></h3>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Adresse 1");	?></div>
																		<div class="content_form"><input id="ADH_adresse1" name="ADH_adresse1" type="text" value="<?php	echo	htmlspecialchars($adr1);	?>" size="30" class="required"/></div>
															</div>
															<div class="hr_form">
																		<div class="label_form"><?php	echo	_("Adresse 2");	?></div>
																		<div class="content_form"><input id="ADH_adresse2" name="ADH_adresse2" type="text" value="<?php	echo	htmlspecialchars($adr2);	?>" size="30"/></div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Code postal");	?></div>
																		<div class="content_form"><input id="ADH_zip" name="ADH_zip" type="text" value="<?php	echo	htmlspecialchars($zip);	?>" size="30" class="required"/></div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Ville");	?></div>
																		<div class="content_form"><input id="ADH_ville" name="ADH_ville" type="text" value="<?php	echo	htmlspecialchars($ville);	?>" size="30" class="required"/></div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Pays");	?></div>
																		<div class="content_form">
																					<select id="FK_PAYS_id" name="FK_PAYS_id" class="required">
																								<?php	while	($row_RS2	=	mysql_fetch_assoc($RS2))	{	?>
																											<option value="<?php	echo	$row_RS2['PAYS_id']	?>"<?php	if	(!(strcmp($row_RS2['PAYS_id'],	$idPays)))	{	echo	"SELECTED";	}	?>>
																														<?php	echo	$row_RS2['PAYS_nom_'	.	$langue];	?>
																											</option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<?php	if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	?>
																		<h3><?php	echo	_("Sangha");	?></h3>
																		<div class="hr_form">
																					<div class="label_form"><?php	echo	_("Ordination");	?></div>
																					<div class="content_form">
																								<input type="checkbox" id="ADH_ordination"  name="ADH_ordination" value="" <?php	if	(!(strcmp("1",	$ordination)))	{	echo	"CHECKED";	}	?>/>
																								<div class="note"><?php	echo	_("A cocher si vous êtes nonne ou moine.");	?></div>
																					</div>
																		</div>
																		<div class="hr_form">
																					<div class="label_form"><?php	echo	_("Nom de Dharma");	?></div>
																					<div class="content_form"><input id="ADH_nom_dharma" name="ADH_nom_dharma" type="text" value="<?php	echo	htmlspecialchars($nomDharma);	?>" size="30"/>
																								<div class="note"><?php	echo	_("Laisser vide si aucun.");	?></div>
																					</div>
																		</div>
															<?php	}	?>
															<?php	if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	?>
																		<h3><?php	echo	_("Bénévolat");	?></h3>
																		<div class="hr_form">
																					<div class="content_form">
																								<input type="checkbox" id="ADH_benevolat" name="ADH_benevolat" value="" <?php	if	(!(strcmp("1",	$benevoleOk)))	{	echo	"CHECKED";	}	?>/><?php	echo	_("Vous souhaitez nous aider bénévolement");	?>
																					</div>
																		</div>
																		<div id="opt_benevolat">
																					<div class="hr_form">
																								<div class="label_form required"><?php	echo	_("Profession");	?></div>
																								<div class="content_form"><input id="ADH_profession" name="ADH_profession" type="text" value="<?php	echo	htmlspecialchars($profession);	?>" size="30" class="required"/></div>
																					</div>
																					<div class="hr_form">
																								<div class="label_form required"><?php	echo	_("Disponibilités");	?></div>
																								<div class="content_form">
																											<select id="ADH_disponibilite" name="ADH_disponibilite" class="required">
																														<option value=""><?php	echo	_("Non renseigné");	?></option>
																														<option value="OCAS"<?php	if	(!(strcmp("OCAS",	$disponibilite)))	{	echo	"SELECTED";	}	?>><?php	echo	_("Occasionnellement");	?></option>
																														<option value="VAC"<?php	if	(!(strcmp("VAC",	$disponibilite)))	{	echo	"SELECTED";	}	?>><?php	echo	_("Vacances scolaires uniquement");	?></option>
																														<option value="WE"<?php	if	(!(strcmp("WE",	$disponibilite)))	{	echo	"SELECTED";	}	?>><?php	echo	_("Week ends uniquement");	?></option>
																														<option value="ALL"<?php	if	(!(strcmp("ALL",	$disponibilite)))	{	echo	"SELECTED";	}	?>><?php	echo	_("Tout le temps");	?></option>
																											</select>
																								</div>
																					</div>
																					<div class="hr_form">
																								<div class="label_form"><?php	echo	_("Compétences");	?></div>
																								<div class="content_form">
																											<ul class="liste_add">
																														<?php
																														if	($nbr_competence	>	0)	{
																																	while	($row_RS4	=	mysql_fetch_assoc($RS4))	{
																																				?>
																																				<li><input type="checkbox" name="CMPT_id[]" value="<?php	echo	$row_RS4['CMPT_id']	?>" <?php	if	(in_array($row_RS4['CMPT_id'],	$cmpt))	{	echo	"CHECKED";	}	?>/><?php	echo	$row_RS4['CMPT_nom_'	.	$langue];	?></li>
																																				<?php
																																	}	}	else	{
																																	echo	_("Aucune compétence renseignée");
																														}
																														?>
																											</ul>
																								</div>
																					</div>
																		</div>
															<?php	}	?>
															<div class="submit_form">
																		<input name="ADH_id" type="hidden" id="ADH_id" value="<?php	echo	$id;	?>"/>
																		<input name="FK_EADH_id" type="hidden" id="FK_EADH_id" value="<?php	echo	$eadh;	?>"/>
																		<input type="submit" name="Submit" value="<?php	echo	_("mettre à jour");	?>" class="bt_submit corner10-all"/>
															</div>
												</form>
									</div>
									<?php	include('../_footer.php');	?>
						</div>
			</body>
</html>