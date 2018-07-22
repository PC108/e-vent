<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
$title	=	_("S'inscrire");

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
								// Validate
								$("#inscription").validate({
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
												}
										}
								});
						});
				</script>
				<!-- CSS -->
				<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
				<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
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
								<p><?php	echo	_("Merci de renseigner le formulaire ci-dessous pour recevoir par mail votre identifiant de connexion.");	?></p>
								<form id="inscription" action="inscription_action.php" method="post" class="formulaire corner20-all">
										<h3><?php	echo	_("Informations personnelles");	?></h3>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("Genre");	?></div>
												<div class="content_form">
														<input type="radio" id="ADH_genre" name="ADH_genre" value="H"/> <?php	echo	_("Mr");	?>
														<input type="radio" id="ADH_genre" name="ADH_genre" value="F"/> <?php	echo	_("Mme");	?>
														<label for="ADH_genre" class="error" style="display:none"><?php	echo	_("Au moins une sélection requise.");	?></label>
												</div>
										</div>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("Nom");	?></div>
												<div class="content_form">
														<input id="ADH_nom" name="ADH_nom" type="text" value="" size="30" class="required"/>
												</div>
										</div>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("Prénom");	?></div>
												<div class="content_form">
														<input id="ADH_prenom" name="ADH_prenom" type="text" value="" size="30" class="required"/>
												</div>
										</div>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("Mot de passe");	?></div>
												<div class="content_form">
														<input id="ADH_password" name="ADH_password" type="password" value="" size="30" class="required"/>
														<div class="note"><?php	echo	_("Entre 4 et 16 caractères.");	?></div>
												</div>
										</div>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("Confirmez votre mot de passe");	?></div>
												<div class="content_form">
														<input id="ADH_password_confirm" name="ADH_password_confirm" type="password" value="" size="30" class="required"/>
														<div class="note"><?php	echo	_("Entre 4 et 16 caractères.");	?></div>
												</div>
										</div>

										<h3><?php	echo	_("Communication");	?></h3>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("Adresse mail");	?></div>
												<div class="content_form">
														<input id="ADH_email" name="ADH_email" type="text" value="" size="30" class="required"/>
												</div>
										</div>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("Newsletter");	?></div>
												<div class="content_form">
														<input type="checkbox" id="MAIL_newsletter"  name="MAIL_newsletter" value=""/>
														<div class="note"><?php	echo	_("A cocher si vous voulez être inscrit à la newsletter.");	?></div>
												</div>
										</div>

										<div class="submit_form">
												<input name="ADH_langue" type="hidden" id="ADH_langue" value="<?php	echo	strtoupper($langue);	?>"/>
												<input type="submit" name="Submit" value="<?php	echo	_("envoyer");	?>" class="bt_submit corner10-all"/>
										</div>
								</form>

						</div>

						<?php	include('../_footer.php');	?>
				</div>
		</body>
</html>