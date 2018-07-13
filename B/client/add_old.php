<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$query	=	"SELECT * FROM t_client_cli";
$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$nbreRows_RS	=	mysql_num_rows($RS);

if	($nbreRows_RS	==	0)	{
// Mode ADD
			$Action	=	"add";
			$Submit	=	"Ajouter";
			$nom	=	"";
			$suffixe	=	"";
			$adr1	=	"";
			$adr2	=	"";
			$zip	=	"";
			$ville	=	"";
			$idPays	=	1;
			$tel	=	"";
			$email_ins	=	"";
			$email_contact	=	"";
}	else	{
// Mode MAJ
			$Action	=	"maj";
			$Submit	=	"Modifier";
// récupération et affichage des valeurs
			$row	=	mysql_fetch_object($RS);
			$nom	=	$row->CLI_nom;
			$suffixe	=	$row->CLI_suffixe;
			$adr1	=	$row->CLI_adresse1;
			$adr2	=	$row->CLI_adresse2;
			$zip	=	$row->CLI_zip;
			$ville	=	$row->CLI_ville;
			$idPays	=	$row->FK_PAYS_id;
			$tel	=	$row->CLI_telephone;
			$email_ins	=	$row->CLI_email_from;
			$email_contact	=	$row->CLI_email_contact;
}

$query_RS2	=	"SELECT * FROM t_pays_pays ORDER BY PAYS_tri_fr ASC";
$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['CONFIGURATION']['L2']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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
						<script type="text/javascript">
									$(document).ready(function() {

												$("#form_client").validate({
															rules: {
																		'CLI_nom': {
																					required: true,
																					minlength: 2
																		},
																		'CLI_suffixe': {
																					required: true,
																					minlength: 3,
																					maxlength: 5
																		},
																		'CLI_adresse1': {
																					required: true,
																					minlength: 2
																		},
																		'CLI_zip': {
																					required: true
																		},
																		'CLI_ville': {
																					required: true,
																					minlength: 2
																		},
																		'CLI_telephone': {
																					required: true,
																					minlength: 2
																		},
																		'CLI_email_from': {
																					required: true,
																					email: true
																		},
																		'CLI_email_contact': {
																					required: true,
																					email: true
																		}
															}
												});

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
									<form id="form_client" action="action.php" method="post">
												<div class="BoxSearch">
															<h2>Nom de la société</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Nom ou raison sociale</div>
																		<div class="content_form"><input id="CLI_nom" name="CLI_nom" type="text" value="<?php	echo	htmlspecialchars($nom);	?>" size="30" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Suffixe</div>
																		<div class="content_form"><input  id="CLI_suffixe" name="CLI_suffixe" type="text" value="<?php	echo	($suffixe);	?>" size="10" class="form_R"></div>
																		<div class="note">Apparait dans l'identifiant des adhérents et la référence des commandes</div>
															</div>
															<h2>Adresse</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Adresse 1</div>
																		<div class="content_form">
																					<input id="CLI_adresse1" name="CLI_adresse1" type="text" value="<?php	echo	htmlspecialchars($adr1);	?>" size="40" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form">Adresse 2</div>
																		<div class="content_form"><input id="CLI_adresse2" name="CLI_adresse2" type="text" value="<?php	echo	htmlspecialchars($adr2);	?>" size="40"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Code postal</div>
																		<div class="content_form"><input id="CLI_zip" name="CLI_zip" type="text" value="<?php	echo	htmlspecialchars($zip);	?>" size="30" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Ville</div>
																		<div class="content_form"><input id="CLI_ville" name="CLI_ville" type="text" value="<?php	echo	htmlspecialchars($ville);	?>" size="30" class="form_R"></div>
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
															<div class="form_hr">
																		<div class="content_form">
																					<div class="note">Cette adresse apparaîtra sur les commandes <br />et dans les informations pour le paiement par chèque et virement.</div>
																		</div>
															</div>
															<h2>Téléphone</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Téléphone</div>
																		<div class="content_form">
																					<input id="CLI_telephone" name="CLI_telephone" type="text" value="<?php	echo	htmlspecialchars($tel);	?>" size="30" class="form_R">
																					<div class="note">Ce téléphone apparaîtra dans l'encart "Nous contacter" sur toutes les pages.</div>
																		</div>
															</div>
															<h2>Emails</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Email de retour</div>
																		<div class="content_form">
																					<input id="CLI_email_from" name="CLI_email_from" type="text" value="<?php	echo	htmlspecialchars($email_ins);	?>" size="40" class="form_R">
																					<div class="note">Adresse de réponse (reply to) pour tous les emails envoyés par l'application
																								<br /> (par exemple lors de la perte d'un mot de passe ou confirmation d'une inscription.)</div>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Email de contact</div>
																		<div class="content_form">
																					<input id="CLI_email_contact" name="CLI_email_contact" type="text" value="<?php	echo	htmlspecialchars($email_contact);	?>" size="40" class="form_R">
																					<div class="note">Adresse de réception des emails envoyés par le formulaire de contact</div>
																		</div>
															</div>
															<div class="form_submit">
																		<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
																		<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
															</div>
												</div>
									</form>
						</div>
			</body>
</html>
<?php	include("../_footer.php")	?>