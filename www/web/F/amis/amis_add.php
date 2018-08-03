<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

$title	=	_("Créer un nouvel adhérent");

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

if	(isset($_GET["load"])	&&	$_GET["load"]	==	"ok")	{	// Permet de charger ses infos persos
		$query	=	"SELECT *
        FROM t_adherent_adh
        WHERE ADH_id='"	.	$_SESSION['info_adherent']['id_adh']	.	"'";
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
		// récupération et affichage des valeurs
		$row	=	mysql_fetch_object($RS);

		$id	=	$row->ADH_id;
		$nom	=	$row->ADH_nom;
		$prenom	=	$row->ADH_prenom;
		$password	=	$row->ADH_password;
		$genre	=	$row->ADH_genre;
		$idTytar	=	$row->FK_TYTAR_id;
		$email	=	$row->ADH_email;
		$newsletter	=	$row->ADH_email;
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
}	else	{
		$id	=	"";
		$eadh	=	"";
		if	(isset($_GET["nom"]))	{	$nom	=	$_GET["nom"];	}	else	{	$nom	=	"";	}
		if	(isset($_GET["nom"]))	{	$prenom	=	$_GET["prenom"];	}	else	{	$prenom	=	"";	}
		$password	=	"";
		$genre	=	"";
		$anneeN	=	"";
		$langueAdh	=	"";
		$idTytar	=	1;
		if	(isset($_GET["nom"]))	{	$email	=	$_GET["email"];	}	else	{	$email	=	"";	}
		$newsletter	=	"";
		$tel	=	"";
		$port	=	"";
		$adr1	=	"";
		$adr2	=	"";
		$zip	=	"";
		$ville	=	"";
		$idPays	=	1;
		$pays	=	"";
		$ordination	=	"";
		$nomDharma	=	"";
		$benevoleOk	=	"";
		$profession	=	"";
		$disponibilite	=	"";
}


$query_RS2	=	"SELECT * FROM t_pays_pays ORDER BY PAYS_tri_"	.	$langue	.	" ASC";
$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());

$query_RS3	=	"SELECT * FROM t_typetarif_tytar WHERE TYTAR_visible=1 ORDER BY TYTAR_ordre ASC";
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
								$('#navigation #amis').addClass('ui-state-active')
								$('#navigation #amis').button( "option", "disabled", true );

								//Option de formulaire
								NS_UTIL.displayFormOpt('#adresses', '#opt_adresses');
								$("#adresses").change (function() {
										NS_UTIL.openFormOpt('#adresses', '#opt_adresses');
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
												'ADH_email': {
														required: true,
														email: true
												},
												'ADH_adresse1': {
														required: function() {
																if($("#adresses").attr('checked')) {
																		return true;
																} else {
																		return false;
																}
														},
														minlength: 2
												},
												'ADH_zip': {
														required: function() {
																if($("#adresses").attr('checked')) {
																		return true;
																} else {
																		return false;
																}
														},
														minlength: 2
												},
												'ADH_pays': {
														required: function() {
																if($("#adresses").attr('checked')) {
																		return true;
																} else {
																		return false;
																}
														}
												},
												'ADH_ville': {
														required: function() {
																if($("#adresses").attr('checked')) {
																		return true;
																} else {
																		return false;
																}
														},
														minlength: 2
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
								<a href="result.php"><div class="bt_retour corner20-tl"><?php	echo	"< "	.	_("Mes relations");	?></div></a>
								<a href="search.php"><div class="bt_retour corner20-br"><?php	echo	"< "	.	_("Chercher un adhérent");	?></div></a>
								<h1><?php	echo	$title;	?></h1>
								<p>
										<a href="amis_add.php?load=ok"><div class="bt_lien corner10-all espace10"><?php	echo	_("charger le formulaire avec mes données par défaut");	?></div></a>
										<a href="amis_add.php"><div class="bt_lien corner10-all espace10"><?php	echo	_("vider le formulaire");	?></div></a>
								</p>
								<form id="maj" class="formulaire corner20-all" action="amis_action.php" method="post">
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
												<div class="content_form"><input id="ADH_email" name="ADH_email" type="text" value="<?php	echo	htmlspecialchars($email);	?>" size="30" class="required"/></div>
										</div>
										<h3><?php	echo	_("Adresse");	?></h3>
										<div class="hr_form">
												<div class="content_form">
														<input type="checkbox" id="adresses" name="adresses" value="" /><?php	echo	_("Vous souhaitez envoyer les billets à une autre adresse que la vôtre.");	?>
												</div>
										</div>
										<div id="opt_adresses">
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
										</div>
										<div class="submit_form">
												<input type="submit" name="Submit" value="<?php	echo	_("créer l'adhérent");	?>" class="bt_submit corner10-all"/>
										</div>
								</form>
						</div>
						<?php	include('../_footer.php');	?>
				</div>
		</body>
</html>