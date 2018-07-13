<?php
/**
 * Affiche  le contenu des valeurs d'un Array (checkbox) dans une colonne.
 * N'utilise qu'une requête pour avoir la correspondance Id -> Val en utilisant un tableau intermédiaire.
 *
 * @author Atelier Du Net
 * @version 07/01/2011
 *
 * @param <array> $Array = Tableau des valeurs (checkbox)
 * @param <string> $Table = Table sur laquelle on fait la requete
 * @param <string> $NomChampID = Id de la table
 * @param <string> $NomChampVal = valeur de la table a chercher
 * @param <resource> $Connexion = connexion à la base de données
 * @return <array> $result
 */
function adn_afficheValFromArray($Array, $Table, $NomChampID, $NomChampVal, $Connexion) {

    $result = "";
    
    if (count($Array) == 0) {
	$result = "<i>aucun choix</i>";
    } else {
	$query_RS = "SELECT $NomChampID, $NomChampVal FROM $Table";
	$RS = mysql_query($query_RS, $Connexion) or die(mysql_error());
	$NbreRows_RS = mysql_num_rows($RS);
	if ($NbreRows_RS == 0) {
	    $result = "---";
	} else {
	    while ($row = mysql_fetch_object($RS)) {
		$array_idValeur[$row->$NomChampID] = $row->$NomChampVal;
	    }
	    foreach ($Array as $value) {
		$result .= $array_idValeur[$value] . "<br /> ";
	    }
	    // vire le dernier  "<br /> ";
	    $result = substr_replace($result, '', -7, -1);
	}
    }

    return $result;
}

?>