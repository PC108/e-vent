<?php
/***********************************************************/
/*              Inclusions des fichiers                    */
/***********************************************************/
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');

/***********************************************************/
/*             Vérification des variables                  */
/***********************************************************/
// Initialisation des variables
$checkIdentification = "pb_user";

// récupération des variables POST
// utilisation de adn_quote_smart pour la protection contre les injections sql
$user = adn_quote_smart($_POST['user']);
$mdp = adn_quote_smart($_POST['mdp']);

// création du recordset
$query_rs1 = "SELECT * FROM t_acces_acs WHERE ACS_login = '$user'";
$rs1 = mysql_query($query_rs1, $connexion) or die(mysql_error());
$nbreRows_rs1 = mysql_num_rows($rs1);

/***********************************************************/
/*              Execution des requêtes                     */
/***********************************************************/
if ($nbreRows_rs1 != 0) {
    $row = mysql_fetch_object($rs1);
    //Si $User existe, vrifie le mot de passe
    if ($row->ACS_pwd == $mdp) {
	// Si le mot de passe existe, crée la session.
	
	$idUser = $row->ACS_id;
	$_SESSION['user_info'] = array($user, $row->ACS_grp, $idUser);
	if (!isset($_COOKIE['nbre_ligne'])) {
	    setcookie('nbre_ligne', 30, time()+60*60*24*30, '/', null, false, false);
	}
	// L'identification a russie
	// Met  jour les infos sur l'accès en cours
	$date = date("Y-m-d");
	$tempsT = date("H:i");
	$compteur = $row->ACS_compteur;
	$compteur++;
	$check = 1;

	$query_rs2 = "UPDATE t_acces_acs SET ACS_compteur = '$compteur', ACS_date = '$date', ACS_time = '$tempsT' WHERE ACS_id = '$idUser'";
	$check = mysql_query($query_rs2, $connexion) or die(mysql_error());
	if ($check) {
	    $checkIdentification = "ok_menu";
	} else {
	    $checkIdentification = "pb_update_db";
	}
    } else {
	$checkIdentification = "pb_pwd";
    }
}

if($checkIdentification == "ok_menu"){
    $page = "menu.php";
} else {
    $_SESSION['message_user'] = $checkIdentification;
    $page = "login.php";
}
adn_myRedirection($page);
?>