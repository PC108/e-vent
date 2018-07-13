<?php

if ($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1") { // en local
    $hostname = "localhost";
    $database = "event-database";
    $username = "root";
    $password = "";
} else { // sur le serveur
    $hostname = "";
    $database = "";
    $username = "";
    $password = "";
};

$connexion = mysql_connect($hostname, $username, $password) or trigger_error(mysql_error(), E_USER_ERROR);

mysql_select_db($database, $connexion);
mysql_query("SET NAMES 'UTF8' ");
?>