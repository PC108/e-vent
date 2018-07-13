<?php

// Requête d'insertion gérant les champs facultatifs
function insertPaypal($reponse, $idcmd) {

    $optionalFields = array(// Tableau à compléter si on souhaite plus d'informations
	'token' => 'TOKEN',
	'montant' => 'AMT',
	'timestamp' => 'TIMESTAMP',
	'description' => 'DESC',
	'custom' => 'PAYMENTREQUEST_0_CUSTOM',
	'transaction_status' => 'CHECKOUTSTATUS',
	'payer_id' => 'PAYERID',
	'payer_email' => 'EMAIL',
	'payer_status' => 'PAYERSTATUS',
	'payer_first_name' => 'FIRSTNAME',
	'payer_last_name' => 'LASTNAME',
	'payer_contry_code' => 'COUNTRYCODE',
	'shipto_name' => 'PAYMENTREQUEST_0_SHIPTONAME',
	'shipto_street' => 'PAYMENTREQUEST_0_SHIPTOSTREET',
	'shipto_city' => 'PAYMENTREQUEST_0_SHIPTOCITY',
	'shipto_state' => 'PAYMENTREQUEST_0_SHIPTOSTATE',
	'shipto_zip' => 'PAYMENTREQUEST_0_SHIPTOZIP',
	'shipto_country_code' => 'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE',
	'shipto_country_name' => 'PAYMENTREQUEST_0_SHIPTOCOUNTRYNAME',
    );

    // construction début de la requête
    $query = "
         INSERT INTO `t_paypal_pay` (
        `PAY_id` ,
        `FK_CMD_id`";

    // construction des champs à modifier dans la table
    foreach ($optionalFields as $key => $value)
	addRowKey($query, $reponse, $key, $value);

    // Début d'ajout des valeurs
    if ($idcmd == "aucun") { // cas du don anonyme
	$query .= ") VALUES (NULL ,NULL";
    } else {
	$query .= ") VALUES (NULL ," . $idcmd;
    }

    // Ajout des valeurs facultatives
    foreach ($optionalFields as $key => $value)
	addRowValue($query, $reponse, $value);

    $query.=")";
    return $query;
}

// Requête modifiant une commande après un retour paypal
function updatePAYPALReturn($link, $status, $sum=0) {
    switch ($status) {
	// Info perso si pas de retour de PAYPAL
	case "PasDeRetourDePaypal":
	    $ecmd = 9;
	    break;
	// Info du statut retourné par PAYPAL
	case "PaymentActionNotInitiated":
	case "PaymentActionInProgress":
	    $ecmd = 4;
	    break;
	case "PaymentActionFailed":
	    $ecmd = 5;
	    break;
	case "PaymentCompleted":
	case "PaymentActionCompleted":
	    $ecmd = 3;
	    break;
	default:
	    $ecmd = 1;
	    break;
    }
    return "UPDATE t_commande_cmd SET FK_ECMD_id=$ecmd,FK_MDPAY_id='6',CMD_encaissement=$sum WHERE CMD_lien='$link'"; // 5 = Paypal
}

/* * ******************************************************** */
/*              Sous-fonctions                    */
/* * ******************************************************** */

// Ajoute la valeur du champ ssi il est définit
function addRowValue(&$query, $reponse, $champ) {
    if (isset($reponse[$champ]))
	$query.=",'$reponse[$champ]'";
}

// Ajoute la clée du champ ssi il est définit
function addRowKey(&$query, $reponse, $cle, $champ) {
    if (isset($reponse[$champ]))
	$query.=",`PAY_" . $cle . "`";
}

?>
