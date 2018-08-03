<?php
/**
 * Nécessite que la fonction mysql_select_db($database_db, $db) soit lancée avant l'appel à la fonction
 * 
 * Compte le nombre d'affichage par jour et l'écrit dans la base de donnée
 * Crée un nouvel enregistrement tous les jours et recommence à zéro
 *
 * @example pour compter le nombre d'affichage d'une page par jour : adn_compteurParDate('id_cpt', 'date_cpt', 'homepage_cpt', '00_compteur', $db);
 *
 * @author Atelier Du Net
 * @version 30/04/2009
 *
 * @param <string> $ChampID = correspond à la colonne de l'Id dans la table Compteur
 * @param <string> $ChampDATE = $ChampDATE correspond à la colonne de la date dans la table Compteur
 * @param <string> $ChampCPTR = correspond à la colonne du compteur dans la table Compteur
 * @param <string> $Table = table sur laquelle on agit
 * @param <ressource> $Connexion = connexion avec la base de données courante
 */
function adn_compteurParDate($ChampID, $ChampDATE, $ChampCPTR, $Table, $Connexion)
{
	$Date = date("Y-m-d");
	$query_RS = "SELECT * FROM $Table WHERE $ChampDATE = '$Date'";
	$RS = mysql_query($query_RS, $Connexion) or die(mysql_error());
	$NbreRows_RS = mysql_num_rows($RS);
	if ($NbreRows_RS > 0) {
		$row = mysql_fetch_object($RS);
		$ValID = $row->$ChampID;
		$ValCompteur = $row->$ChampCPTR;
		$ValCompteur++;
		$query_RS = "UPDATE $Table SET $ChampCPTR = '$ValCompteur' WHERE $ChampID = '$ValID'";
		mysql_query($query_RS, $Connexion) or die(mysql_error());
	} else {
		$query_RS = "INSERT INTO $Table ($ChampDATE, $ChampCPTR) VALUES ('$Date', '1')";
		mysql_query($query_RS, $Connexion) or die(mysql_error());
	}
}
?>