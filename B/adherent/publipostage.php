<?php
/***********************************************************/
/*              Inclusions des fichiers                    */
/***********************************************************/
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('../../librairie/php/code_adn/TraitementCSV.php');
require_once('_requete.php');

ini_set('html_errors', 0); // Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
error_reporting(E_ALL);

$sessionFiltre = 'FiltreADH';

/***********************************************************/
/*                  Test de connexion                      */
/***********************************************************/
if ((!isset($_SESSION['user_info'])) || !in_array($_SESSION['user_info'][1], array('admin', 'adher'))) {
    $_SESSION['message_user'] = "acces_ko";
    adn_myRedirection('../login/menu.php');
}

/***********************************************************/
/*              Execution des requêtes                     */
/***********************************************************/
adn_checkEffaceFiltres($sessionFiltre);
$query	=	mainQueryAdherent();
$query = adn_creerExists($query, $sessionFiltre, 'TJ_CMPT_id', 'EXISTS', 'tj_adh_cmpt', 'TJ_ADH_id = ADH_id');
$query = adn_creerFiltre($query, $sessionFiltre, array('check_withcmt', 'select_tri', 'submit', 'chemin_retour', 'fonction', 'TJ_CMPT_id'), array('FK_CMTADH_id', 'NEWS_email'));
$query = adn_groupBy($query,'ADH_id');
$query = adn_orderBy($query, $sessionFiltre, 'ADH_id DESC');

$result = mysql_query($query, $connexion) or die(mysql_error());

/***********************************************************/
/*              Formatage du CSV                    */
/***********************************************************/
header("Content-type: text/x-csv");
header('content-disposition: attachement; filename="publipostage.csv"');

$chaine = "Nom;Prénom;Identifiant;Genre;Adresse1;Adresse2;Code postal;Ville;Pays;Email;" . adn_getStrRetour();
echo (utf8_decode($chaine));

while ($row = mysql_fetch_object($result)) {
    $nom = adn_checkData($row->ADH_nom);
    $prenom = adn_checkData($row->ADH_prenom);
    $identifiant = adn_checkData($row->ADH_identifiant);
    $genre = adn_checkData($row->ADH_genre);
    $adr1 = adn_checkData($row->ADH_adresse1);
    $adr2 = adn_checkData($row->ADH_adresse2);
    if($adr2 == NULL){ $adr2 = ""; }
    $zip = adn_checkData($row->ADH_zip);
    $ville = adn_checkData($row->ADH_ville);
    $pays = adn_checkData($row->PAYS_nom_fr);
		$mail	=	adn_checkData($row->ADH_email); /* Ajouté Pour René pour avoir toutes les adresses email sans filtre newsletter */

    // ecriture du contenu dans le .CSV
    // Pour un retour chariot opérationnel sous un serveur Windows
    $chaine = "$nom;$prenom;$identifiant;$genre;$adr1;$adr2;$zip;$ville;$pays;$mail" . adn_getStrRetour();
    echo (utf8_decode($chaine));
}
exit;
?>