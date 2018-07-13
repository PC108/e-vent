<?php
/* * ******************************************************** */
/*              Inclusions des fichiers                    */
/* * ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/CheckEmailPHP.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');

$autoriseDelete	=	FALSE;	// Sécurité pour action delete via le get
$table = 't_client_cli';

/* * ******************************************************** */
/*                  Test de connexion                      */
/* * ******************************************************** */
if ((!isset($_SESSION['user_info'])) || !in_array($_SESSION['user_info'][1], array('admin'))) {
    $_SESSION['message_user'] = "acces_ko";
    adn_myRedirection('../login/menu.php');
}

/* * ******************************************************** */
/*             Vérification des variables                  */
/* * ******************************************************** */
$checkRequired = "ok";
$id = 1;
$autresData = array();

// récupération des variables POST avec gestion des échappements
if (isSet($_POST['action'])) {
    $action = $_POST['action'];

// Vérification des champs obligatoires
    if (
	    ($_POST["CLI_nom"] == "")
	    || ($_POST["CLI_suffixe"] == "")
	    || ($_POST["CLI_adresse1"] == "")
	    || ($_POST["CLI_zip"] == "")
	    || ($_POST["CLI_ville"] == "")
	    || ($_POST["CLI_telephone"] == "")
	    || ($_POST["CLI_email_from"] == "") || (!adn_checkEmailPHP($_POST['CLI_email_from']))
	    || ($_POST["CLI_email_contact"] == "") || (!adn_checkEmailPHP($_POST['CLI_email_contact']))
    ) {
	$checkRequired = "bad";
    } else {
	$formatData['CLI_suffixe'] = 'UPPER';
	$checkArrayVide = array();
    }
} else {
    $action = "del";
}
/* * ******************************************************** */
/*              Execution des requêtes                     */
/* * ******************************************************** */
if ($checkRequired == "bad") {
    $_SESSION['message_user'] = "bad_post";
} else {
    switch ($action) {
	case "maj":
	    $query = adn_creerUpdate($table, 'CLI_id', $id, array('action', $tableId . 'CLI_id', 'Submit', 'pageNum'), $autresData, $formatData, $checkArrayVide);
	    adn_mysql_query($query, $connexion, array('maj_ok', 'maj_ko'), array('message_user', 'message_debug'));
	    break;
	case "add":
	    $query = adn_creerInsert($table, array('action', 'CLI_id', 'Submit', 'pageNum'), $autresData, $formatData);
	    adn_mysql_query($query, $connexion, array('add_ok', 'add_ko'), array('message_user', 'message_debug'));
	    break;
	case "del":
	    if ($autoriseDelete) {
		$query = "DELETE FROM " . $table . " WHERE CLI_id='" . mysql_real_escape_string($id) . "'";
		adn_mysql_query($query, $connexion, array('del_ok', 'del_ko'), array('message_user', 'message_debug'));
	    }
	    break;
    }
}

adn_myRedirection("maj.php");
?>