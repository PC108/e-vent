<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CheckEmailPHP.php');
require_once('../../librairie/php/code_adn/EnvoiMail.php');
require_once('../../librairie/php/yaml/sfYaml.php');
require_once('../../librairie/php/yaml/sfYamlParser.php');

$title	=	_("Contact");

/*	* ******************************************************** */
/*             Envoi du mail                  */
/*	* ******************************************************** */
// Définition de la chaine de saut de ligne
if	(strtoupper(substr(PHP_OS,	0,	3))	===	'WIN')	{
			$str_retour	=	"\r\n";
}	else	{
			$str_retour	=	"\n";
}

// Informations personelles : oui/non
if	(isset($_SESSION['info_adherent']))	{
			if	(isset($_POST["infos_perso"])	&&	$_POST["infos_perso"]	==	"oui")	{
						$infosPerso	=	"info : [ "	.	$_SESSION['info_adherent']['ref_adh']	.	" ] [ "	.	$_SESSION['info_adherent']['prenom_adh']	.	" "	.	$_SESSION['info_adherent']['nom_adh']	.	" ]";
						if	(isset($_SESSION['info_cmd']))	{
									$infosCmd	=	"cmd : ["	.	$_SESSION['info_cmd']['ref_cmd']	.	" ]";
						}	else	{
									$infosCmd	=	"";
						}
			}	else	{
						$infosPerso	=	_("info : ne souhaite pas transmettre ses informations personnelles.");
						$infosCmd	=	"";
			}
			$mailEnvoi	=	$_SESSION['info_client']['email_contact']	.	", "	.	$_SESSION['info_adherent']['email_adh'];
}	else	{
			$infosPerso	=	_("info : envoyé avant de s'être identifié sur le site.");
			$infosCmd	=	"";
			$mailEnvoi	=	$_SESSION['info_client']['email_contact'];
}
if	(isset($_POST["Submit"]))	{
			// Récupération de l'adresse email de ReplyTo
			$msg	=	"sendContact_ok";
			if	($_POST["mail"]	==	""	||	$_POST["sujet"]	==	""	||	$_POST["text"]	==	""	||	(!adn_checkEmailPHP($_POST['mail'])))	{
						$msg	=	"bad_post";
			}	else	{
						$contenu	=	$_POST["text"]	.	$str_retour	.	$str_retour;
						$contenu	.=	$infosPerso	.	$str_retour;
						$contenu	.=	$infosCmd;

						// envoi du mail
						if	(!adn_envoiMail($mailEnvoi,	$_POST["sujet"],	$contenu,	$_POST["mail"]))	{
									$msg	=	"sendEmail_ko";
						}
			}
			$_SESSION['message_user']	=	$msg;
			adn_myRedirection("contact.php");
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
						<!-- JS NAMESPACE -->
						<?php	include('../_shared_js.php');	?>
						<script type="text/javascript">
									$(document).ready(function() {
												//Navigation
												$('#navigation #contact').addClass('ui-state-active')
												$('#navigation #contact').button( "option", "disabled", true );

												// Validate
												$("#contact").validate({
															rules: {
																		'text': {
																					required: true,
																					minlength: 10
																		},
																		'sujet': {
																					required: true,
																					minlength: 2
																		},
																		'mail': {
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
						<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="global">
									<?php	include('../_header.php');	?>
									<?php	include('../_sidebar.php');	?>
									<div id="content" class="corner20-all">
												<h1><?php	echo	$title;	?></h1>
												<form id="contact" action="contact.php" method="post">
															<h3><?php	echo	_("Nous contacter");	?></h3>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Votre email");	?></div>
																		<div class="content_form">
																					<input id="mail" name="mail" type="text" value="" size="50" class="required"/>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Sujet");	?></div>
																		<div class="content_form">
																					<input id="sujet" name="sujet" type="text" value="" size="50" class="required"/>
																		</div>
															</div>
															<div class="hr_form">
																		<div class="label_form required"><?php	echo	_("Tapez votre message");	?></div>
																		<div class="content_form">
																					<textarea id="text" name="text" cols="50" rows="10" class="required"></textarea>
																		</div>
															</div>
															<?php	if	(isset($_SESSION['info_adherent']))	{	?>
																		<div class="hr_form">
																					<div class="label_form"><?php	echo	_("Informations personnelles");	?></div>
																					<div class="content_form">
																								<span><?php	echo	_("Pour nous aider à vous reconnaître, souhaitez-vous ajouter les informations ci-dessous au contenu de votre message.");	?></span>
																								<p class="commentaire">[ <?php	echo	$_SESSION['info_adherent']['ref_adh'];	?> ] [ <?php	echo	$_SESSION['info_adherent']['prenom_adh']	.	" "	.	$_SESSION['info_adherent']['nom_adh'];	?> ]</p>
																								<?php	if	(isset($_SESSION['info_cmd']))	{	?>
																											<p class="commentaire">[ <?php	echo	$_SESSION['info_cmd']['ref_cmd'];	?> ]</p>
																								<?php	}	?>
																								<input type="radio" name="infos_perso" value="<?php	echo	_("oui")	?>" checked="checked" /> oui
																								<input type="radio" name="infos_perso" value="<?php	echo	_("non")	?>" /> non
																					</div>
																		</div>
															<?php	}	?>
															<div	class="submit_form">
																		<input	type="submit"	name="Submit"	value="<?php	echo	_("envoyer");	?>"	class="bt_submit corner10-all"/>
															</div>
												</form>
									</div>
									<?php	include('../_footer.php');
									?>
						</div>
			</body>
</html>