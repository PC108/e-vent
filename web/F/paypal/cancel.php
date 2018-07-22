<?php
/* * ******************************************************** */
/*              Inclusions des fichiers                    */
/* * ******************************************************** */
include('../_shared.php');
require_once('_requete.php');
require_once('paypal.class.php');
require_once('_config_paypal.php');

$title = _("Paiement PAYPAL annulé");

/* * ******************************************************** */
/*                Test droits d'accès                      */
/* * ******************************************************** */

if (!isset($_SESSION['paypal_token']) || !isset($_GET['token']) || !isset($_SESSION['paypal_link'])) {
    $_SESSION['message_user'] = "paypal_retour_ko";
    adn_myRedirection('../commande/result.php');
}

/* * ******************************************************** */
/*                   Début code paypal                     */
/* * ******************************************************** */

$link = $_SESSION['paypal_link'];

// $config est défini dans _config_paypal.php
$paypal = new phpPayPal($config, true);

$paypal->token = htmlentities($_GET['token'], ENT_QUOTES);

// Récupération des détails de la commande
if ($paypal->get_express_checkout_details()) {

    // $paypal->Response['ACK'] = 'TEST';
    if ($paypal->Response['ACK'] == 'Success') {

	// Insertion des infos dans la table paypal
	$query = insertPaypal($paypal->Response, $_SESSION['paypal_idcmd']);
	mysql_query($query, $connexion) or die(mysql_error());
	// Modification de la commande
	$query = updatePAYPALReturn($link, $paypal->Response['CHECKOUTSTATUS']);
	mysql_query($query, $connexion) or die(mysql_error());
    } else {
	// Modification de la commande avec simplement l'ajout de l'information PAYPAL sans reponse (id=9)
	$query = updatePAYPALReturn($link, "PasDeRetourDePaypal");
	mysql_query($query, $connexion) or die(mysql_error());
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
		<h1><?php echo _("Paiement PAYPAL annulé"); ?></h1>
		<p><?php echo _("La transaction a été annulée ou a été refusée par PAYPAL."); ?></p>
		<ul>
		    <li><?php echo _("Aucun paiement n'a été enregistré."); ?></li>
		    <li><?php echo _("Aucun débit ne sera effectué sur votre compte PAYPAL."); ?></li>
		    <li><?php echo _("Votre inscription à l'événement n'a pas été enregistrée."); ?></li>
		</ul>
		<p><?php echo _("Si vous souhaitez revenir à votre commande pour essayer un nouveau mode de paiement,"); ?> <a href="../commande/result.php"><?php echo _("cliquez ici"); ?></a></p>
		<p><?php // echo var_dump($paypal->Response); ?></p>
	    </div>
	</div>
	<?php include('../_footer.php'); ?>
    </body>
</html>

