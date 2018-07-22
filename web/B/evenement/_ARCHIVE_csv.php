<?php
/// Non utilisé pour le moment
/// Laissé là pour développement futur si besoin
/// Etat du 5 mail 2011
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('../../librairie/php/code_adn/TraitementCSV.php');

if ((!isset($_SESSION['user_info'])) || !in_array($_SESSION['user_info'][1], array('admin', 'event'))) {
    $_SESSION['message_user'] = "acces_ko";
    adn_myRedirection('../login/menu.php');
}
?>
<?php
// Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
ini_set('html_errors', 0);

/** Error reporting */
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="evenement_' . date("Ymd_Hi") . '.csv"');

// Requetes
include("_requete.php");
$query_RS = adn_creerFiltre($query, 'FiltreEVEN', array('check_withcmt', 'select_tri', 'submit', 'chemin_retour'), array(), NULL, 'EVEN_id DESC');
$result = mysql_query($query_RS, $connexion) or die(mysql_error());

// Mise en place des titres
$chaine = "Evenement;;;;;;;;" . adn_getStrRetour();
echo (utf8_decode($chaine));

// mise en place des sous titres
$chaine = "Etat;Nom en français;Nom en anglais;Places;Descriptif en français;Descriptif en anglais;Cotisation;Lien;" . adn_getStrRetour();
echo (utf8_decode($chaine));

while ($row = mysql_fetch_object($result)) {
    $etat = adn_checkData($row->EEVEN_nom);
    $nomFr = adn_checkData($row->EVEN_nom_fr);
    $nomEn = adn_checkData($row->EVEN_nom_en);
    $places = adn_checkData($row->EVEN_places);
    $descrFr = adn_checkData($row->EVEN_descriptif_fr);
    $descrEn = adn_checkData($row->EVEN_descriptif_en);
    $cot = adn_checkData($row->EVEN_cotisation);
    $lien = adn_checkData($row->EVEN_lien);

    $chaine = "$etat;$nomFr;$nomEn;$places;$descrFr;$descrEn;$cot;$lien" . adn_getStrRetour();
    echo (utf8_decode($chaine));

    if (isSet($_GET['com']) && $_GET['com'] == "1" && $row->FK_CMTEVEN_id != 0) {
	$chaine = "Commentaire : ";
	$query = "SELECT CMT_commentaire FROM t_commentaire_cmt WHERE CMT_id = $row->FK_CMTEVEN_id";
	$rs = mysql_query($query, $connexion) or die(mysql_error());
	$res = mysql_fetch_object($rs);
	$chaine .= adn_checkData($res->CMT_commentaire);
	$chaine .= ";".adn_getStrRetour();
        echo (utf8_decode($chaine));
    }
}
exit;
?>
