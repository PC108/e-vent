<?php
if ($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1") { // en local
    $hostname = "db";
    $database = "oneplayecentre";
    $port =3301;
    $username = "root";
    $password = "root";
} else { // sur le serveur
    $hostname = "";
    $database = "";
    $username = "";
    $password = "";
};
$connexion = mysqli_connect($hostname, $username, $password) or trigger_error(mysql_error(), E_USER_ERROR);
//mysql_select_db($database, $connexion);
//mysql_query("SET NAMES 'UTF8' ");

/* Vérification de la connexion */
if (mysqli_connect_errno()) {
    printf("Echec de la connexion : %s\n", mysqli_connect_error());
    exit();
}

if (!mysqli_query($connexion, "SET NAMES 'UTF8' ")) {
    printf("Message d'erreur : %s\n", mysqli_error($connexion));
}

/* Fermeture de la connexion */
mysqli_close($connexion);
?>


