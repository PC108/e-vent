<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('_config_paypal.php');
require_once('paypal.class.php');

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
// récupération des variables POST avec gestion des échappements
if	((!isset($_POST['montant_don']))	||	($_POST['montant_don']	==	"")	||	(!is_numeric($_POST['montant_don'])))	{
		$_SESSION['message_user']	=	'bad_post';
		//adn_myRedirection("../evenement/index.php");
}	else	{
		// $config est défini dans _config_paypal.php
		$paypal	=	new	phpPayPal($config,	true);

		$nom	=	_('Don anonyme');
		$idEvent	=	"";
		// Pas de description, ca prends trop de places
		$desc	=	"";
		$montant	=	intval($_POST['montant_don']);
		$paypal->add_item($nom,	$desc,	$montant,	$idEvent);

		$paypal->return_url	=	$page_return;
		$paypal->cancel_url	=	$page_cancel;
		$paypal->description	=	"cmd = aucune";

		// jusqu'à 256 caractères. Ne gère pas le texte après un espace
		$paypal->custom	=	"don_anonyme";
// Mise en forme de l'interface PAYPAL
		if	($logo_paypal	!=	"")	{
				$paypal->logo	=	$logo_paypal;
		}
		if	($background_paypal	!=	"")	{
				$paypal->background	=	$background_paypal;
		}
		if	($border_paypal	!=	"")	{
				$paypal->border	=	$border_paypal;
		}
		if	($langue	==	"fr")	{
				$paypal->local_code	=	"FR";
		}	else	{
				$paypal->local_code	=	"GB";
		}

		// Création de la communication avec Paypal
		if	($paypal->set_express_checkout())	{
				if	($paypal->Response['ACK']	==	'Success')	{
						// Stockage variables paypal en session
						$_SESSION['paypal_token']	=	$paypal->Response['TOKEN'];
						$_SESSION['paypal_idcmd']	=	"aucun";
						$_SESSION['paypal_link']	=	"aucun";
						// PHIL
						$_SESSION['paypal_total']	=	$montant;

						// Redirige le visiteur sur le site de PayPal
						if	($config['sandbox'])	{
								header("Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token="	.	$paypal->Response['TOKEN']);
						}	else	{
								header("Location: https://www.paypal.com/webscr?cmd=_express-checkout&token="	.	$paypal->Response['TOKEN']);
						}
				}	else	{
						echo	'<div style="font-family: Arial, Helvetica, sans-serif; font-size:14px; padding: 0 20px;">';
						echo	'<h2>'	.	_("Echec de la connexion à PAYPAL pour un don anonyme")	.	'</h2>';
						echo	'<p style="font-family: Arial, Helvetica, sans-serif">'	.	_("La commande [set_express_checkout] a échouée. Veuillez réessayer dans quelques minutes.");
						echo	'<br />'	.	_("Si le problème persiste, veuillez contacter l'administrateur du site en lui transmettant les informations ci-dessous.")	.	'</p>';
						echo	'<p><a href="../commande/result.php">'	.	_('Retour')	.	'</a></p>';
						echo	'<p>&nbsp;</p>';
						echo	'<h4>'	.	_('Informations à envoyer')	.	'</h4>';
						echo	var_dump($paypal->Response);
						echo	'</div>';
				}
		}	else	{
				$_SESSION['message_user']	=	"paypal_ko";
				adn_myRedirection('../commande/result.php');
		}
}
?>