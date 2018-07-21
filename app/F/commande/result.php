<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../B/commande/_requete.php');
require_once('../../B/achat/_requete.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('../_fonction/bodyCmd.php');

$title	=	_("Commande en cours");

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
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// Check si la commande existe
// Infos sur la commande
$query	=	mainQueryCmd($connexion);
$query	.=	" WHERE FK_ADH_id=$idAdh AND FK_ECMD_id IN (1,2,4,5,9) ORDER BY CMD_id DESC";	// (Sans info, En cours, PAYPAL annulé, PAYPAL refusé, PAYPAL sans info)
$RSCmd	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$NbreRows_RSCmd	=	mysql_num_rows($RSCmd);
if	($NbreRows_RSCmd	>	0)	{
// Info sur les achats
		$query	=	mainQueryAch($_SESSION['info_cmd']['id_cmd'],	$connexion);
		$query.="ORDER BY FK_ADH_id,EVEN_id,JREVEN_date_debut, TYACH_ordre, TYRES_ordre, TYHEB_ordre ";
		$RSAchat	=	mysql_query($query,	$connexion)	or	die(mysql_error());
		$NbreRows_RSAchat	=	mysql_num_rows($RSAchat);
		if	($NbreRows_RSAchat	==	0)	{
				$_SESSION['message_user']	=	"no_achat";
		}
}	else	{
		unset($_SESSION['info_cmd']);
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
								$('#navigation #commande').addClass('ui-state-active')
								$('#navigation #commande').button( "option", "disabled", true );

								// Init Dialogue
								NS_DIALOG.initAlertDialog($('#alert_paye_cheque'), '<?php	echo	_("ok");	?>');
								$("#bt_paye_cheque").click(function() {
										NS_DIALOG.openDialog($('#alert_paye_cheque'));
								});

								// Bouton Delete
								// message sur rollover
								$('.ligne_prix img').live('hover', function() {
										$('#box_popup').html($('#info_delete').html());
										NS_UTIL.displayInfoPopup($(this), $('#box_popup'), [-35,0], "corner10-top corner10-br");
								});
								// Supprime l'achat dans la commande
								$('.ligne_prix img').live('click', function() {
										var idTypeAchat = $(this).attr('id_typeachat');
										var idAdherent = $(this).attr('id_adh');
										// AJAX
										switch(idTypeAchat) {
												case "3": // Jour(s) évènement
														var idJrEvent = $(this).attr('id_jreven');
														var objData = {
																action: 'delete',
																FK_ADH_id: idAdherent,
																FK_TYACH_id: idTypeAchat,
																FK_JREVEN_id: idJrEvent
														};
														break;
												default: // Cotisation, Don, Hébergement, Restauration
														var idAchat = $(this).attr('id_achat');
														var objData = {
																action: 'delete',
																FK_ADH_id: idAdherent,
																FK_TYACH_id: idTypeAchat,
																ACH_id: idAchat
														};
														break;

										}
										NS_FRONT.updateCommande(objData, true);
								});
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
								<h1><?php	echo	$title;	?> | <a href="historique.php"><?php	echo	_("Historique des factures");	?></a></h1>
								<div class="info_print corner10-all">
										<div><img src="../_media/GEN/ic_info.png" align="absmiddle" alt="info"/> <span class="commentaire"><?php	echo	_("Tous les choix que vous effectuez sont enregistrés en temps réel dans votre commande. Ainsi, vous pouvez vous déconnecter à tout moment sans perdre d'informations.");	?></span></div>
								</div>
								<?php	if	($NbreRows_RSCmd	==	0)	{	?>
										<div class="formulaire corner20-all">
												<p><?php	echo	sprintf(_("Aucune commande n'a été initialisée ou bien votre dernière commande est en cours de traitement. Dans ce cas, vous la retrouverez dans %s l'historique des factures. %s"),	'<a href="historique.php">',	'</a>');	?></p>
												<p><?php	echo	_("Pour démarrer une nouvelle commande, ajoutez un achat.");	?></p>
												<a href="../evenement/evenement.php"><div class="bt_lien corner10-all espace10"><?php	echo	_("ajouter des achats")	?></div></a>
										</div>
								<?php	}	else	{	?>
										<ul style="margin:0; padding:0">
												<li class="bt_lien corner10-all espace10"><a href="../evenement/evenement.php"><?php	echo	_("modifier")	?></a></li>
												<li class="bt_lien corner10-all espace10"><a href="print.php?lg=<?php	echo	$langue;	?>&lien=<?php	echo	$_SESSION['info_cmd']['lien_cmd']	?>&retour=cmd_FO"><?php	echo	_("imprimer");	?></a></li>
												<?php	if	(isset($_SESSION['info_cmd'])	&&	$_SESSION['info_cmd']['total_cmd']	>	0)	{	?>
														<li class="bt_lien corner10-all espace10"><a href="#paiement"><?php	echo	_("effectuer votre paiement")	?></a></li>
												<?php	}	?>
										</ul>
										<div><?php	echo	htmlBodyCmd($_SESSION['info_client'],	$RSCmd,	$RSAchat,	$langue,	TRUE, $configAppli);	?></div>
										<?php	if	(isset($_SESSION['info_cmd'])	&&	$_SESSION['info_cmd']['total_cmd']	>	0)	{	?>
												<a name="paiement"></a>
												<h2><?php	echo	_("Effectuer votre paiement");	?></h2>
												<table border="0" width="100%">
														<tr>
																<td width="50%" align="center" valign="top" height="100px">
																		<div class="bloc_print corner20-all">
																				<?php	echo	_("par carte bancaire")	?>
																				<br /><span class="commentaire">(<?php	echo	_("solution PAYPAL")	?>)</span>
																				<form action="../paypal/paypal.php" method="post">
																						<input type="hidden" id="id_cmd" name="id_cmd" value="<?php	echo	$_SESSION['info_cmd']['id_cmd']	?>"/>
																						<input type="hidden" id="link" name="link" value="<?php	echo	$_SESSION['info_cmd']['lien_cmd']	?>"/>
																						<input style="border:0; float: none" src="https://www.paypal.com/fr_FR/FR/i/logo/PayPal_mark_60x38.gif" type=image Value=submit/>
																				</form
																		</div>
																</td>
																<td width="50%" align="center" valign="top">
																		<div class="bloc_print corner20-all"><?php	echo	_("par chèque, mandat ou espèces");	?>
																				<br /><img id="bt_paye_cheque" src="../_media/GEN/ic_cheque.png" alt="<?php	echo	_("payer par chèque");	?>" style="margin: 21px 0 16px 0;" class="pointer" />
																		</div>
																</td>
														</tr>
												</table>
										<?php	}	?>
								<?php	}	?>
		    </div>
						<?php	include('../_footer.php');	?>
				</div>
				<div id="box_popup" class="popup">--</div>
				<div id="info_delete" class="invisible"><?php	echo	_("retirer cet achat de la commande");	?></div>
				<div id="alert_paye_cheque" title="<?php	echo	_("Procéder au paiement par chèque, mandat ou espèces");	?>">
						<?php
						$html	=	'<p>'	.	_("La référence de votre commande est")	.	' <b>'	.	$_SESSION['info_cmd']['ref_cmd'] . '</b>';
						$html	.=	'<br />'	.	_("Le montant de votre commande est de")	.	' <b>'	.	$_SESSION['info_cmd']['total_cmd']	.	' € </b></p>';
						$html	.=	'<p>'	.	_("Pour payer par chèque, mandat ou espèces, imprimez votre commande.");
						$html	.=	'<br />'	.	_("Joignez-la à votre paiement et adressez le tout à :");
						// Adresse
						$html	.=	'<div class="bloc_print corner20-all" style="width:200px;margin: 10px auto;background-color:#FFFFFF"><b>'	.	$_SESSION['info_client']['nom']	.	'</b>';
						$html	.=	'<br />'	.	$_SESSION['info_client']['adr1'];
						if	($_SESSION['info_client']['adr2']	!=	"")	{
								$html	.=	'<br />'	.	$_SESSION['info_client']['adr2'];
						}
						$html	.=	'<br />'	.	$_SESSION['info_client']['zip']	.	' '	.	$_SESSION['info_client']['ville'];
						$html	.=	'<br />'	.	$_SESSION['info_client']['pays']	.	'</div>';
						// Ordre du chèque
						if	(!is_null($_SESSION['info_client']['ordre_cheque'])	&&	$_SESSION['info_client']['ordre_cheque']	!=	"")	{
								$html	.='<p>' . _("Les chèques ou mandats sont à adresser à l'attention de :") . '</p>';
								$html	.=	'<div class="bloc_print corner20-all" style="width:200px;margin: 10px auto;background-color:#FFFFFF">'	.	$_SESSION['info_client']['ordre_cheque']	.	'</div>';
						}
						echo	$html;
						?>
				</div>
		</body>
</html>