<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('_config_paypal.php');
require_once('paypal.class.php');
require_once('../../B/achat/_requete.php');
require_once('../../B/commande/_requete.php');
require_once('../../librairie/php/code_adn/Formatage.php');

/*	* ******************************************************** */
/*                Test droits d'accès                      */
/*	* ******************************************************** */
if	(!isset($_POST['id_cmd'])	||	!isset($_POST['link']))	{
		$_SESSION['message_user']	=	"paypal_e1_ko";
		adn_myRedirection('../commande/result.php');
}	else	{
		$id	=	$_POST['id_cmd'];
		$link	=	$_POST['link'];
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
//$query	=	mainQueryAch($id,	$connexion);
//$query.="ORDER BY TYACH_ordre, EVEN_id, JREVEN_id ASC ";
//$RSAchat	=	mysql_query($query,	$connexion)	or	die(mysql_error());

$query	=	paypalAch($id);
$RSDetailNbreAchats	=	mysql_query($query,	$connexion)	or	die(mysql_error());

$query	=	mainQueryCmd($connexion);
$query.=" WHERE CMD_id = $id";
$RSCmd	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$rowCmd	=	mysql_fetch_object($RSCmd);


/*	* ******************************************************** */
/*                   Début code paypal                     */
/*	* ******************************************************** */
// Redirection en cas de commande sans achat
if	(intval($rowCmd->totalCommande)	<=	0)	{
		$_SESSION['message_user']	=	"no_achat";
		adn_myRedirection('../commande/result.php');
}

$langue	=	$_SESSION['lang'];
// $langue	=	"en";
// $config est défini dans _config_paypal.php
$paypal	=	new	phpPayPal($config,	true);

/*	* ******************************************************** */
/*                  Obsoléte - 
	* 										génére une chaine de caractère trop longue qui provoque une erreur PAYPAL lorsqu'il y a trop d'achats         
	*/
/*	* ******************************************************** */
/*
	 while	($rowAch	=	mysql_fetch_array($RSAchat))	{
	 switch	($rowAch['FK_TYACH_id'])	{
	 case	1:	// Cotisations
	 $nom	=	$rowAch['TYACH_nom_'	.	$langue]	.	" "	.	$rowAch['TYCOT_nom_'	.	$langue];
	 break;
	 case	2:	// Dons
	 $nom	=	$rowAch['TYACH_nom_'	.	$langue]	.	" "	.	$rowAch['TYDON_nom_'	.	$langue];
	 break;
	 case	3;	// Jour evenement
	 $nom	=	$rowAch['FK_JREVEN_id']	.	" | "	.	$rowAch['EVEN_nom_'	.	$langue]	.	" | "	.	$rowAch['LEVEN_nom'];
	 break;
	 case	4:	// Hébergement
	 $nom	=	$rowAch['FK_JREVEN_id']	.	" | "	.	$rowAch['TYHEB_nom_'	.	$langue]	.	" ("	.	$rowAch['TYACH_nom_'	.	$langue]	.	")";
	 break;
	 case	5:	// Restauration
	 $nom	=	$rowAch['FK_JREVEN_id']	.	" | "	.	$rowAch['TYRES_nom_'	.	$langue]	.	" ("	.	$rowAch['TYACH_nom_'	.	$langue]	.	")";
	 break;
	 default:
	 break;
	 }
	 // Pas de description, ca prends trop de places
	 $desc	=	"";
	 $montant	=	round(($rowAch['ACH_montant']	*	$rowAch['ACH_ratio'])	/	100,	2);
	 $paypal->add_item($nom,	$desc,	$montant);
	 // Contribution ponctuelle
	 if	($rowAch['FK_TYACH_id']	==	3	&&	$rowAch['ACH_surcout']	>	0)	{
	 $montantSurcout	=	round(($rowAch['ACH_surcout']	*	$rowAch['ACH_ratio'])	/	100,	2);
	 $paypal->add_item($rowAch['FK_JREVEN_id']	.	" | "	.	_('Cotisation ponctuelle'),	$desc,	$montantSurcout);
	 }
	 }
	*/

/*	* ******************************************************** */
/*                  Information basique sur la commande            */
/*	* ******************************************************** */
$desc	=	_("dont");
while	($rowAch	=	mysql_fetch_array($RSDetailNbreAchats))	{
		$totalNbreAchat	+=	$rowAch['nbreAchat'];
		$desc	.=	" "	.	$rowAch['nbreAchat']	.	" "	.	_("achat(s) pour")	.	" "	.	$rowAch['ADH_prenom']	.	" "	.	$rowAch['ADH_nom']	.	",";
}
$desc	=	rtrim($desc,	",");
$desc	.=	".";
$nom	=	_("Cette commande contient")	.	" "	.	$rowCmd->nbreAchats	.	" "	.	_("achat(s)")	.	".";
$montant	=	$rowCmd->totalAchats;
$paypal->add_item($nom,	$desc,	$montant);

// Remise
if	($rowCmd->CMD_remise	>	0)	{
		$paypal->add_item(_("Remise exceptionelle"),	"",	"-"	.	$rowCmd->CMD_remise,	"");
}

$paypal->return_url	=	$page_return;
$paypal->cancel_url	=	$page_cancel;
$paypal->description	=	"cmd = "	.	$rowCmd->CMD_ref;
// PHIL
// jusqu'à 256 caractères. Ne gère pas le texte après un espace
$paypal->custom	=	"commande";
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
		$paypal->local_code	=	"US";
}

//var_dump($paypal);
//exit;
//
// Création de la communication avec Paypal
if	($paypal->set_express_checkout())	{

		// var_dump($paypal);
		// exit;

		if	($paypal->Response['ACK']	==	'Success')	{
				// Stockage variables paypal en session
				$_SESSION['paypal_token']	=	$paypal->Response['TOKEN'];
				$_SESSION['paypal_idcmd']	=	$id;
				$_SESSION['paypal_link']	=	$link;
				// PHIL
				$_SESSION['paypal_total']	=	$rowCmd->totalCommande;

				// Redirige le visiteur sur le site de PayPal
				if	($config['sandbox'])	{
						header("Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token="	.	$paypal->Response['TOKEN']);
				}	else	{
						header("Location: https://www.paypal.com/webscr?cmd=_express-checkout&token="	.	$paypal->Response['TOKEN']);
				}
		}	else	{
				echo	'<div style="font-family: Arial, Helvetica, sans-serif; font-size:14px; padding: 0 20px;">';
				echo	'<h2>'	.	_("Echec de la connexion à PAYPAL")	.	'</h2>';
				echo	'<p style="font-family: Arial, Helvetica, sans-serif">'	.	_("La commande [set_express_checkout] a échouée. Veuillez réessayer dans quelques minutes.");
				echo	'<br />'	.	_("Si le problème persiste, veuillez contacter l'administrateur du site en lui transmettant les informations ci-dessous.")	.	'</p>';
				echo	'<p><i>'	.	_("Note : PAYPAL est limité dans la quantité d'information à transmettre. Si votre commande contient beaucoup d'achats, c'est peut-être la raison de cette erreur. Dans ce cas, veuillez créer deux commandes au lieu d'une ou bien choisissez un autre mode de paiement.")	.	'</i></p>';
				echo	'<p><a href="../commande/result.php">'	.	_('Retour')	.	'</a></p>';
				echo	'<p>&nbsp;</p>';
				echo	'<h4>'	.	_('Informations à envoyer')	.	'</h4>';
				echo	var_dump($paypal->Response);
				// echo	var_dump($paypal);
				echo	'</div>';
		}
}	else	{
		$_SESSION['message_user']	=	"paypal_ko";
		adn_myRedirection('../commande/result.php');
}
?>