<?php
/**
 * Nécessite que la fonction mysql_select_db($database_db, $db) soit lancée avant l'appel à la fonction
 *
 * Incrémente un compteur dans une base de données
 * Sert par exemple à compter le nombre d'affichage d'une page ou le nombre de téléchargements d'un fichier
 *
 * @example pour compter le nombre de téléchargement d'un puzzle : adn_compteurPlus('id_puzzle', $IdPuzzle, 'dwl_108p', '02_puzzle', $db);
 * @example pour compter le nombre de téléchargement d'un utilisateur : adn_compteurPlus('id_user', $_SESSION['IdUser'], 'nbredwl_user', '01_user', $db);
 *
 * @author Atelier Du Net
 * @version 30/04/2009
 *
 * @param <string> $ChampID = correspond à la colonne dans la table de l'objet à compter
 * @param <string> $Id = $Id correspond à l'Id de l'objet qu'on est en train de compter
 * @param <string> $ChampCPTR = $ChampCPTR correspond à la colonne dans la table du compteur
 * @param <string> $Table = table sur laquelle on agit
 * @param <ressource> $Connexion = connexion avec la base de données courante
 */
function adn_compteurPlus($ChampID, $Id, $ChampCPTR, $Table, $Connexion)
{
	$query_RS = "SELECT * FROM $Table WHERE $ChampID = '$Id'";
	$RS = mysql_query($query_RS, $Connexion) or die(mysql_error());
	$NbreRows_RS = mysql_num_rows($RS);
	if ($NbreRows_RS > 0) {
		$row = mysql_fetch_object($RS);
		$ValCompteur = $row->$ChampCPTR;
		$ValCompteur++;
		$query_RS = "UPDATE $Table SET $ChampCPTR = '$ValCompteur' WHERE $ChampID = '$Id'";
		mysql_query($query_RS, $Connexion) or die(mysql_error());
	}
}

?>