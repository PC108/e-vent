<?php
/***********************************************************/
/*              Inclusions des fichiers                    */
/***********************************************************/
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');

$table = 't_acces_acs';
$autoriseDelete	=	TRUE;	// Sécurité pour action delete via le get

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/***********************************************************/
/*              Gestion du numéro de page                  */
/***********************************************************/

// On garde le numéro de page courant en paramètre
// POST lorsqu'on vient de add.php(add & maj), GET quand on vient de result.php (del)
if (isSet($_POST['pageNum'])) {
    $pageNum = $_POST['pageNum'];
} elseif (isSet($_GET['pageNum'])) {
    $pageNum = $_GET['pageNum'];
}

/***********************************************************/
/*             Vérification des variables                  */
/***********************************************************/
$msg = "ok";

// Vérifie $action
// récupération des variables POST avec gestion des échappements
if (isSet($_POST['action'])) {
    $action = $_POST['action'];
    $id = $_POST['ACS_id'];

// Vérification des champs obligatoires, plus le mail, plus les années
    if (($_POST["ACS_login"] == "") || ($_POST["ACS_pwd"] == "")) {
	$msg = "bad_post";
    } else {
	$formatData = array();
	$checkArrayVide = array();

	// Vérification que le login n'existe pas déjà
	$login = $_POST["ACS_login"];
	$query_rs1 = "SELECT * FROM t_acces_acs WHERE ACS_login = '$login'";
	$rs1 = mysql_query($query_rs1, $connexion) or die(mysql_error());
	$nbreRows_rs1 = mysql_num_rows($rs1);
	if ($nbreRows_rs1) {
	    $msg = "login_already_exist";
	}
    }
} else {
    $action = "del";
    $id = $_GET["id"];
}
/***********************************************************/
/*                Exécution des actions                    */
/***********************************************************/

if ($msg != "ok") {
    $_SESSION['message_user'] = $msg;
} else {
    switch ($action) {
	case "maj":
	    $query = adn_creerUpdate($table, 'ACS_id', $id, array('action', 'ACS_id', 'Submit', 'pageNum'), $autresData, $formatData, $checkArrayVide);
	    adn_mysql_query($query, $connexion, array('maj_ok', 'maj_ko'), array('message_user', 'message_debug'));
	    break;
	case "add":
	    $query = adn_creerInsert($table, array('action', 'ACS_id', 'Submit', 'pageNum'), $autresData, $formatData);
	    adn_mysql_query($query, $connexion, array('add_ok', 'add_ko'), array('message_user', 'message_debug'));
	    break;
	case "del":
	    if ($autoriseDelete) {
		$query = "DELETE FROM " . $table . " WHERE ACS_id='" . mysql_real_escape_string($id) . "'";
		adn_mysql_query($query, $connexion, array('del_ok', 'del_ko'), array('message_user', 'message_debug'));
	    }
	    break;
    }
}

adn_myRedirection("result.php?pageNum=" . $pageNum);
?>