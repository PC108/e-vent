<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/MyRedirection.php');

$title	=	_("Cotisations");

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../evenement/index.php');
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
								$('#navigation #cotisation').addClass('ui-state-active')
								$('#navigation #cotisation').button( "option", "disabled", true );

						});
				</script>
				<!-- CSS -->
				<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
				<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
				<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/cmd_front.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
		</head>
		<body>
				<div id="global">
						<?php	include('../_header.php');	?>
						<?php	include('../amis/_amis.php');	// A placer avant sidebar.php ?>
						<?php	include('../_sidebar.php');	?>
						<div id="content" class="corner20-all">
								<h1><?php	echo	$title;	?></h1>
								<div class="formulaire corner20-all">
										<?php
										if	($_SESSION['info_beneficiaire']['cotisation_benef']	>=	date("Y"))	{
												$iconeCotisation	=	"../_media/GEN/cotisation_ok.png";
												$msgCotisation2	=	sprintf(_("La cotisation de %s %s pour l'année en cours est à jour. Nous vous en remercions."),	$_SESSION['info_beneficiaire']['prenom_benef'],	'<b>'	.	$_SESSION['info_beneficiaire']['nom_benef']	.	'</b>');
										}	else	if	($_SESSION['info_beneficiaire']['cotisation_benef']	==	0)	{
												$iconeCotisation	=	"../_media/GEN/cotisation_bad.png";
												$msgCotisation2	=	sprintf(_("Aucune cotisation de %s %s pour %s n'a encore été payée."),	$_SESSION['info_beneficiaire']['prenom_benef'],	'<b>'	.	$_SESSION['info_beneficiaire']['nom_benef']	.	'</b>',	$_SESSION['info_client']['nom']);
												$msgCotisation2	.=	" "	.	_("Elle est donc en attente de réception. Vous pouvez l'ajouter à votre commande en utilisant le formulaire ci-dessous.");
										}	else	{
												$iconeCotisation	=	"../_media/GEN/cotisation_bad.png";
												$msgCotisation2	=	sprintf(_("La dernière cotisation de %s %s pour %s a été payée en %s."),	$_SESSION['info_beneficiaire']['prenom_benef'],	'<b>'	.	$_SESSION['info_beneficiaire']['nom_benef']	.	'</b>',	$_SESSION['info_client']['nom'],	'<b>'	.	$_SESSION['info_beneficiaire']['cotisation_benef']	.	'</b>');
												$msgCotisation2	.=	" "	.	_("Elle est donc en attente de réception. Vous pouvez l'ajouter à votre commande en utilisant le formulaire ci-dessous.");
										}
										echo	'<img	src="'	.	$iconeCotisation	.	'"	style="vertical-align: middle"	alt=""	/> '	.	$msgCotisation2;
										?>
										<p class="note"><?php	echo	_("Notez qu'un surcoût peut vous être décompté lors de l'inscription à un événement si votre cotisation n'est pas à jour.")	?></p>
								</div>

								<?php
								if	($_SESSION['info_beneficiaire']['cotisation_benef']	<	date("Y"))	{
										$query_rs2	=	"SELECT * FROM t_typecotisation_tycot WHERE TYCOT_visible=1 ORDER BY TYCOT_ordre";
										$rs2	=	mysql_query($query_rs2,	$connexion)	or	die(mysql_error());
										$NbreRows_rs2	=	mysql_num_rows($rs2);
										if	($NbreRows_rs2	>	0)	{
												?>
												<div  class="formulaire corner20-all" >
														<h3><?php	echo	_("Cotisation");	?></h3>
														<div class="hr_form">
																<div class="label_form"><?php	echo	_("Type de cotisation");	?></div>
																<div class="content_form">
																		<select id="select_type_cotisation" name="select_type_cotisation">
																				<?php	while	($row_rs2	=	mysql_fetch_assoc($rs2))	{	?>
																						<option value='<?php	echo	$row_rs2['TYCOT_id']	.	"-"	.	$row_rs2['TYCOT_montant']	?>'><?php	echo	$row_rs2['TYCOT_nom_'	.	$langue]	.	" ("	.	$row_rs2['TYCOT_montant']	.	" €)";	?></option>
																				<?php	}	?>
																		</select>
																</div>
														</div>
														<a href="javascript:NS_FRONT.payerCotisation(<?php	echo	$_SESSION['info_beneficiaire']['id_benef'];	?>)"><div class="bt_lien corner10-all espace10"/><?php	echo	_("ajouter à ma commande");	?></div></a>
										</div>
								<?php	}	else	{	?>
										<div  class="formulaire corner20-all" >
												<p><?php	echo	_("Aucun type de cotisation n'est actuellement disponible. Veuillez contacter l'administrateur du site si le problème persiste.");	?></p>
										</div>
								<?php	}	?>
						<?php	}	?>
				</div>
				<?php	include('../_footer.php');	?>
		</div>
</body>
</html>
