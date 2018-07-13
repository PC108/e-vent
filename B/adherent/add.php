<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('_requete.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */

if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Gestion du numéro de page                  */
/*	* ******************************************************** */

// Conservation du numéro de page
if	(isSet($_GET['pageNum']))	{
		$pageNum	=	$_GET['pageNum'];
}	else	{
		$pageNum	=	0;
}

/*	* ******************************************************** */
/*               Définition des variables                  */
/*	* ******************************************************** */

// récupération de la variable GET $id
$id	=	$_GET["id"];
if	($id	==	0)	{
// Mode ADD
		$Action	=	"add";
		$Submit	=	"Ajouter";
		$nom	=	"";
		$prenom	=	"";
		$identifiant	=	"automatique";
		$password	=	"";
		$genre	=	"";
		$anneeN	=	"";
		$langue	=	"";
		$idTytar	=	1;
		$idEtat	=	5;	// incription_bo
		$Etat	=	"en cours d'incription";
		$couleur	=	"DDDDDD";
		$email	=	"";
		$newsletter	=	null;
		$tel	=	"";
		$port	=	"";
		$adr1	=	"";
		$adr2	=	"";
		$zip	=	"";
		$ville	=	"";
		$idPays	=	1;
		$ordination	=	0;
		$nomDharma	=	"";
		$anneeC	=	"";
		$benevoleOk	=	0;
		$profession	=	"";
		$disponibilite	=	"";
		$idMail	=	null;
		$prive	=	0;
}	else	{
// Mode MAJ
		$Action	=	"maj";
		$Submit	=	"Modifier";
// création du recordset
		$query	=	mainQueryAdherent();
		$query	.=	"WHERE ADH_id=$id ";
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
		$row	=	mysql_fetch_object($RS);

		$nom	=	$row->ADH_nom;
		$prenom	=	$row->ADH_prenom;
		$identifiant	=	$row->ADH_identifiant;
		$password	=	$row->ADH_password;
		$genre	=	$row->ADH_genre;
		$anneeN	=	$row->ADH_annee_naissance;
		$langue	=	$row->ADH_langue;
		$idTytar	=	$row->FK_TYTAR_id;
		$idEtat	=	$row->FK_EADH_id;
		$Etat	=	$row->EADH_nom;
		$couleur	=	$row->EADH_couleur;
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
		$anneeC	=	$row->ADH_annee_cotisation;
		$benevoleOk	=	$row->ADH_benevolat;
		$profession	=	$row->ADH_profession;
		$disponibilite	=	$row->ADH_disponibilite;
		$prive	=	$row->ADH_prive;
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

$query_RS2	=	"SELECT * FROM t_pays_pays ORDER BY PAYS_tri_fr ASC";
$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());

$query_RS3	=	"SELECT * FROM t_typetarif_tytar ORDER BY TYTAR_ordre ASC";
$RS3	=	mysql_query($query_RS3,	$connexion)	or	die(mysql_error());

$query_RS4	=	"SELECT * FROM t_competence_cmpt ORDER BY CMPT_nom_fr ASC";
$RS4	=	mysql_query($query_RS4,	$connexion)	or	die(mysql_error());
$nbr_competence	=	mysql_num_rows($RS4);

$query_RS5	=	"SELECT CMPT_id FROM t_competence_cmpt LEFT JOIN tj_adh_cmpt ON TJ_CMPT_id=CMPT_id WHERE TJ_ADH_id=$id";
$RS5	=	mysql_query($query_RS5,	$connexion)	or	die(mysql_error());
$cmpt	=	array();
while	($cmptLignes	=	mysql_fetch_array($RS5))	{
		array_push($cmpt,	$cmptLignes['CMPT_id']);
}

$query_RS6	=	"SELECT * FROM t_etatadherent_eadh ORDER BY EADH_ordre";
$RS6	=	mysql_query($query_RS6,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L1']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
		<head>
				<title><?php	echo	$titre;	?></title>
				<meta NAME="author" CONTENT="www.atelierdu.net" />
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
				<link rel="icon" type="image/png" href="../_media/favicon.png" />
				<!-- JS -->
				<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
				<script type="text/javascript" src="../_shared.js"></script>
				<!-- JS NAMESPACE -->
				<script  type="text/JavaScript" src="../../librairie/js/code_adn/ns_util.js"></script>
				<script type="text/javascript">
						$(document).ready(function() {
								//Option de formulaire
								NS_UTIL.displayFormOpt('#ADH_benevolat', '#opt_benevolat');
								$('#ADH_benevolat').change (function() {
										NS_UTIL.openFormOpt('#ADH_benevolat', '#opt_benevolat');
								});
								//Validation
								$("#form_adherent").validate({
										rules: {
												'ADH_annee_cotisation': {
														range: [1900, new Date().getFullYear()+1]
												},
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
												'ADH_zip': {
														required: true
												},
												'ADH_ville': {
														required: true,
														minlength: 2
												},
												'ADH_adresse1': {
														required: true,
														minlength: 2
												},
												'ADH_telephone': {
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
				<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
		</head>
		<body>
				<?php	include("../_header.php");	?>
				<div id="contenu">
						<form id="form_adherent" action="action.php" method="post">
								<div class="BoxSearch">
										<h2>Inscription</h2>
										<div class="form_hr" style="background-color: #<?php	echo	$couleur;	?>">
												<div class="label_form label_R">Etat</div>
												<div class="content_form">
														<input name="FK_EADH_id" type="hidden" id="FK_EADH_id" value="<?php	echo	$idEtat;	?>">
														<span ><?php	echo	($Etat);	?></span>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Année de cotisation</div>
												<div class="content_form"><input id="ADH_annee_cotisation" name="ADH_annee_cotisation" type="text" value="<?php	echo	($anneeC);	?>" size="30"></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Accès aux événements privés <img src="../_media/bo_locked.png" width="16" height="16" border="0"alt=""/><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/></div>
												<div class="content_form">
														<input type="checkbox" id="ADH_prive"  name="ADH_prive" value="" <?php	if	(!(strcmp("1",	$prive)))	{	echo	"CHECKED";	}	?>>
												</div>
										</div>
										<h2>Informations Personnelles</h2>
										<div class="form_hr">
												<div class="label_form label_R">Genre</div>
												<div class="content_form">
														<input type="radio" id="ADH_genre" name="ADH_genre" value="H" <?php	if	(!(strcmp("H",	$genre)))	{	echo	"CHECKED";	}	?>> Mr
														<input type="radio" id="ADH_genre" name="ADH_genre" value="F" <?php	if	(!(strcmp("F",	$genre)))	{	echo	"CHECKED";	}	?>> Mme
														<label for="ADH_genre" class="error" style="display:none">Au moins une sélection requise.</label>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Nom</div>
												<div class="content_form"><input id="ADH_nom" name="ADH_nom" type="text" value="<?php	echo	htmlspecialchars($nom);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Prénom</div>
												<div class="content_form"><input  id="ADH_prenom" name="ADH_prenom" type="text" value="<?php	echo	htmlspecialchars($prenom);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Identifiant</div>
												<div class="content_form"><?php	echo	($identifiant);	?>
														<div class="note">Créé automatiquement en même temps que l'inscription.</div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Mot de passe</div>
												<div class="content_form">
														<input  id="ADH_password" name="ADH_password" type="text" value="<?php	echo	($password);	?>" size="30" class="form_R">
														<div class="note">Entre 4 et 16 caractères.</div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Année de naissance</div>
												<div class="content_form"><input id="ADH_annee_naissance" name="ADH_annee_naissance" type="text" id="mdp" value="<?php	echo	($anneeN);	?>" size="30"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Type Tarif</div>
												<div class="content_form">
														<ul class="liste_add">
																		<?php	while	($row_RS3	=	mysql_fetch_assoc($RS3))	{	?>
																				<li><input type="radio" id="FK_TYTAR_id" name="FK_TYTAR_id" value="<?php	echo	$row_RS3['TYTAR_id']	?>" <?php	if	(!(strcmp($row_RS3['TYTAR_id'],	$idTytar)))	{	echo	"CHECKED";	}	?> class="required"/><?php	echo	$row_RS3['TYTAR_nom_fr']	.	" ("	.	$row_RS3['TYTAR_ratio']	.	"%)";	?></li>
																				<li class="note" style="padding-left: 20px;"><?php	echo	$row_RS3['TYTAR_description_fr'];	?></li>
																		<?php	}	?>
																</ul>
<!--														<select  id="FK_TYTAR_id" name="FK_TYTAR_id" class="form_R">
																<?php	while	($row_RS3	=	mysql_fetch_assoc($RS3))	{	?>
																		<option value="<?php	echo	$row_RS3['TYTAR_id']	?>"<?php	if	(!(strcmp($row_RS3['TYTAR_id'],	$idTytar)))	{	echo	"SELECTED";	}	?>><?php	echo	$row_RS3['TYTAR_nom_fr']	.	" ("	.	$row_RS3['TYTAR_ratio']	.	"%)"	?></option>
																<?php	}	?>
														</select>-->
														<div class="note">Pour ajouter un nouveau type de tarif, <a href="../typetarif/result.php">cliquez ici.</a></div>
												</div>
										</div>
										<h2>Communication</h2>
										<div class="form_hr">
												<div class="label_form label_R">Email</div>
												<div class="content_form"><input  id="ADH_email" name="ADH_email" type="text" value="<?php	echo	htmlspecialchars($email);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Newsletter</div>
												<div class="content_form">
														<input type="checkbox" id="NEWS_email"  name="NEWS_email" value="" <?php	if	(!is_null($newsletter))	{	echo	"CHECKED";	}	?>>
														<div class="note">A cocher si vous voulez être inscrit à la newsletter.</div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Téléphone</div>
												<div class="content_form"><input id="ADH_telephone" name="ADH_telephone" type="text" value="<?php	echo	htmlspecialchars($tel);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Portable</div>
												<div class="content_form"><input id="ADH_portable" name="ADH_portable" type="text" value="<?php	echo	htmlspecialchars($port);	?>" size="30"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Langue</div>
												<div class="content_form">
														<input type="radio"  id="ADH_langue" name="ADH_langue" value="FR" <?php	if	(!(strcmp("FR",	$langue)))	{	echo	"CHECKED";	}	?>> Français
														<input type="radio"  id="ADH_langue" name="ADH_langue" value="EN" <?php	if	(!(strcmp("EN",	$langue)))	{	echo	"CHECKED";	}	?>> Anglais
														<label for="ADH_langue" class="error" style="display:none">Au moins une sélection requise.</label>
														<div class="note">Correspond à la langue avec laquelle la personne communique.</div>
												</div>
										</div>
										<h2>Adresse</h2>
										<div class="form_hr">
												<div class="label_form label_R">Adresse 1</div>
												<div class="content_form"><input id="ADH_adresse1" name="ADH_adresse1" type="text" value="<?php	echo	htmlspecialchars($adr1);	?>" size="40" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form">Adresse 2</div>
												<div class="content_form"><input id="ADH_adresse2" name="ADH_adresse2" type="text" value="<?php	echo	htmlspecialchars($adr2);	?>" size="40"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Code postal</div>
												<div class="content_form"><input id="ADH_zip" name="ADH_zip" type="text" value="<?php	echo	htmlspecialchars($zip);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Ville</div>
												<div class="content_form"><input id="ADH_ville" name="ADH_ville" type="text" value="<?php	echo	htmlspecialchars($ville);	?>" size="30" class="form_R"></div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Pays</div>
												<div class="content_form">
														<select id="FK_PAYS_id" name="FK_PAYS_id" class="form_R">
																<?php	while	($row_RS2	=	mysql_fetch_assoc($RS2))	{	?>
																		<option value="<?php	echo	$row_RS2['PAYS_id']	?>"<?php	if	(!(strcmp($row_RS2['PAYS_id'],	$idPays)))	{	echo	"SELECTED";	}	?>>
																				<?php	echo	$row_RS2['PAYS_nom_fr']	?>
																		</option>
																<?php	}	?>
														</select>
												</div>
										</div>
										<?php	if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	?>
										<h2>Sangha</h2>
										<div class="form_hr">
												<div class="label_form">Ordination</div>
												<div class="content_form">
														<input type="checkbox" id="ADH_ordination"  name="ADH_ordination" value="" <?php	if	(!(strcmp("1",	$ordination)))	{	echo	"CHECKED";	}	?>>
														<div class="note">A cocher si vous êtes nonne ou moine.</div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Nom de Dharma</div>
												<div class="content_form"><input id="ADH_nom_dharma" name="ADH_nom_dharma" type="text" value="<?php	echo	htmlspecialchars($nomDharma);	?>" size="30">
														<div class="note">Laisser vide si aucun.</div>
												</div>
										</div>
										<?php	}	?>
										<?php	if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	?>
										<h2>Bénévolat</h2>
										<div class="form_hr">
												<div class="content_form">
														<input type="checkbox" id="ADH_benevolat" name="ADH_benevolat" value="" <?php	if	(!(strcmp("1",	$benevoleOk)))	{	echo	"CHECKED";	}	?>>Vous souhaitez nous aider bénévolement
												</div>
										</div>
										<div id="opt_benevolat">
												<div class="form_hr">
														<div class="label_form label_R">Profession</div>
														<div class="content_form"><input id="ADH_profession" name="ADH_profession" type="text" value="<?php	echo	htmlspecialchars($profession);	?>" size="30" class="form_R"></div>
												</div>
												<div class="form_hr">
														<div class="label_form label_R">Disponibilités</div>
														<div class="content_form">
																<select id="ADH_disponibilite" name="ADH_disponibilite" class="form_R">
																		<option value="">Non renseigné</option>
																		<option value="OCAS"<?php	if	(!(strcmp("OCAS",	$disponibilite)))	{	echo	"SELECTED";	}	?>>Occasionnellement</option>
																		<option value="VAC"<?php	if	(!(strcmp("VAC",	$disponibilite)))	{	echo	"SELECTED";	}	?>>Vacances scolaires uniquement</option>
																		<option value="WE"<?php	if	(!(strcmp("WE",	$disponibilite)))	{	echo	"SELECTED";	}	?>>Week ends uniquement</option>
																		<option value="ALL"<?php	if	(!(strcmp("ALL",	$disponibilite)))	{	echo	"SELECTED";	}	?>>Tout le temps</option>
																		<option value="INT"<?php	if	(!(strcmp("INT",	$disponibilite)))	{	echo	"SELECTED";	}	?>>Classification interne</option>
																</select>
														</div>
												</div>
												<div class="form_hr">
														<div class="label_form label_R">Compétences</div>
														<div class="content_form">
																<ul class="liste_add">
																		<?php
																		if	($nbr_competence	>	0)	{
																				while	($row_RS4	=	mysql_fetch_assoc($RS4))	{
																						?>
																						<li><input type="checkbox" name="CMPT_id[]" value="<?php	echo	$row_RS4['CMPT_id']	?>" <?php	if	(in_array($row_RS4['CMPT_id'],	$cmpt))	{	echo	"CHECKED";	}	?>><?php	echo	$row_RS4['CMPT_nom_fr']	?></li>
																						<?php
																				}	}	else	{
																				echo	"Aucune compétence renseignée";
																		}
																		?>
																</ul>
																<div class="note">Pour ajouter une nouvelle compétence dans la liste, <a href="../competence/result.php">cliquez ici.</a></div>
														</div>
														
												</div>
										</div>
										<?php	}	?>
										<div class="form_submit">
												<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
												<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
												<input name="ADH_id" type="hidden" id="ADH_id" value="<?php	echo	$id;	?>">
												<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
										</div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
