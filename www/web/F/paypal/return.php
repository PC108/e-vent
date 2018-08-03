<?php
/* * ******************************************************** */
/*              Inclusions des fichiers                    */
/* * ******************************************************** */
include('../_shared.php');
require_once('_requete.php');
require_once('paypal.class.php');
require_once('../../B/commande/_requete.php');
require_once('_config_paypal.php');

$title = _("Paiement PAYPAL accepté");

/* * ******************************************************** */
/*                Test droits d'accès                      */
/* * ******************************************************** */

if (!isset($_SESSION['paypal_token']) || !isset($_GET['PayerID']) || !isset($_GET['token']) || !isset($_SESSION['paypal_link'])) {
    $_SESSION['message_user'] = "paypal_retour_ko";
    adn_myRedirection('../commande/result.php');
}

/* * ******************************************************** */
/*                   Début code paypal                     */
/* * ******************************************************** */
// Récupération des infos stockées en session au niveau de paypal.php ou paypal_anonyme.php
$link = $_SESSION['paypal_link'];
$amt = $_SESSION['paypal_total'];

// $config est défini dans _config_paypal.php
$paypal = new phpPayPal($config, true);

$paypal->amount_total = $amt;
$paypal->local_code = strtoupper($_SESSION['lang']);
$paypal->payer_id = htmlentities($_GET['PayerID'], ENT_QUOTES);
$paypal->token = htmlentities($_GET['token'], ENT_QUOTES);

// Finalisation du paiement avec paypal
if ($paypal->do_express_checkout_payment()) {
    // $paypal->Response['ACK'] = 'TEST';
    if ($paypal->Response['ACK'] == 'Success') {
	$paypal->token = $paypal->Response['TOKEN'];

	// Récupération des détails de la commande
	if ($paypal->get_express_checkout_details()) {
	    // $paypal->Response['ACK'] = 'TEST';
	    if ($paypal->Response['ACK'] == 'Success') {

		// Insertion des infos dans la table paypal
		$query = insertPaypal($paypal->Response, $_SESSION['paypal_idcmd']);
		mysql_query($query, $connexion) or die(mysql_error());
		// Modification de la commande
		if ($link != "aucun") { // cas du don anonyme qui n'a pas de commande
		    $query = updatePAYPALReturn($link, $paypal->Response['CHECKOUTSTATUS'], floatval($paypal->Response['AMT']));
		    mysql_query($query, $connexion) or die(mysql_error());
		}
		// Si le paiement est valide, supppression des informations de la commande dans la session
		$check = stripos($paypal->Response['CHECKOUTSTATUS'], 'Completed');
		if ($check === false) {
		    // la réponse de PAYPAL a été réceptionnée mail le statut ne contient pas 'Completed' > Paiement non validé.
		} else {
		    unset($_SESSION['info_cmd']);
		    unset($_SESSION['lastOpen']);
		}
	    } else {
		// Modification de la commande avec simplement l'ajout de l'information PAYPAL sans reponse (id=9)
		if ($link != "aucun") { // cas du don anonyme qui n'a pas de commande
		    $query = updatePAYPALReturn($link, "PasDeRetourDePaypal");
		    mysql_query($query, $connexion) or die(mysql_error());
		}
	    }
	} else {
	    $_SESSION['message_user'] = "paypal_ko";
	    adn_myRedirection('../commande/result.php');
	}
    } else {
	// Modification de la commande avec simplement l'ajout de l'information PAYPAL sans reponse (id=9)
	if ($link != "aucun") { // cas du don anonyme qui n'a pas de commande
	    $query = updatePAYPALReturn($link, "PasDeRetourDePaypal");
	    mysql_query($query, $connexion) or die(mysql_error());
	}
    }
} else {
    $_SESSION['message_user'] = "paypal_ko";
    adn_myRedirection('../commande/result.php');
}

/* * ******************************************************** */
/*              Suppression variables paypal               */
/* * ******************************************************** */

// Evite faile d'actualisation de page
unset($_SESSION['paypal_token']);
unset($_SESSION['paypal_link']);

/* * ******************************************************** */
/*              Début code de la page html                 */
/* * ******************************************************** */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $langue; ?>" xml:lang="<?php echo $langue; ?>">
    <head>
	<title>e-venement.com | <?php echo $title; ?></title>
	<meta NAME="author" CONTENT="www.atelierdu.net" />
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta http-equiv="Content-Language" content="<?php echo $langue; ?>" />
	<!-- JS -->
	<?php include('../_shared_js.php'); ?>
	<!-- CSS -->
	<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
	<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
	<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
	<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
    </head>
    <body>
	<div id="global">
	    <?php include('../_header.php'); ?>
	    <?php include('../_sidebar.php'); ?>
	    <div id="content" class="corner20-all">
		<h1><?php echo _("Paiement PAYPAL accepté") ?></h1>
		<p><?php echo _("La transaction s'est déroulée avec succès."); ?></p>
		<ul>
		    <li><?php echo sprintf(_("Vous pouvez consulter cette commande dans %s l'historique des factures %s, sauf si vous avez effectué un don anonyme."), '<a href="../commande/historique.php">', '</a>')?></li>
		    <li><?php echo _("Vous allez recevoir dans les plus brefs délais les achats que vous avez commandé."); ?></li>
		</ul>
		<p><?php echo sprintf(_("Si vous souhaitez obtenir plus d'information, vous pouvez %s nous contacter %s en nous précisant la référence de votre commande."),'<a href="../contact/contact.php">','</a>') ?></p>
		<p><?php //echo var_dump($paypal->Response); ?></p>
	    </div>
	</div>
	<?php include('../_footer.php'); ?>
    </body>
</html>