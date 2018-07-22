<?php
/***********************************************************/
/*              Inclusions des fichiers                    */
/***********************************************************/
include('../_shared_action.php');

$nomDB = $configAppli['DATABASE']['nom'];

// Supprime l'ancienne base de données
$query = "DROP DATABASE IF EXISTS " . $nomDB;
$check = mysql_query($query, $connexion) or die(mysql_error());
if ($check) {
    echo utf8_decode("<p>La base de données <b>$nomDB</b> a été supprimée avec succès</p>");
} else {
    echo utf8_decode("<p>La base de données <b>$nomDB</b> n'a pu être supprimée</p>");
}

// Crée une nouvelle base de données
$query = "CREATE DATABASE IF NOT EXISTS " . $nomDB;
$check = mysql_query($query, $connexion) or die(mysql_error());
if ($check) {
    echo utf8_decode("<p>Une nouvelle base de données <b>$nomDB</b> a été créé avec succès</p>");
} else {
    echo utf8_decode("<p>La nouvelle base de données <b>$nomDB</b> n'a pu être créé</p>");
}

?>

<p><a href="http://localhost/phpmyadmin/index.php?db=<?php echo $nomDB ?>">Ouvrir dans phpMyAdmin</a></p>