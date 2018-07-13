<?php

// Pages de retour de PAYPAL
if ($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1") {
    $page_return = 'http://localhost/e-venement/ftp/dev/F/paypal/return.php';
    $page_cancel = 'http://localhost/e-venement/ftp/dev/F/paypal/cancel.php';
} else {
    $page_return = 'http://dev.e-venement.com/F/paypal/return.php';
    $page_cancel = 'http://dev.e-venement.com/F/paypal/cancel.php';
}

// Interface PAYPAL
$logo_paypal = 'http://dev.e-venement.com/F/paypal/_media/logo_paypal.png';
$background_paypal = "";
$border_paypal = "";

$test = false;
// Configuration du compte de test Atelier Du Net
if ($test) {
    $config = array(
	'api_username' => 'paye.a_1310370218_biz_api1.gmail.com',
	'api_password' => '1310370254',
	'api_signature' => 'AmE5uGyxUZ.9LsOWwAYOtQg8HkfhAsho-cnjfPd5J35HIz03M.O27niw',
	'sandbox' => true
    );
}
// Voir procédure complète pour activer le compte du client dans TODO_prod.txt
else {
    $config = array(
        'api_username' => 'gestion.drukpa_api1.orange.fr',
        'api_password' => '275H57GQJL5J3GVQ',
        'api_signature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AxxLPVTIgNlUuj6U587k9C39NdO2',
        'sandbox' => false
    );
}
?>
