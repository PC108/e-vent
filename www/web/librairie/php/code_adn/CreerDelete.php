<?php

/**
 * Crée une requête SQL DELETE à partir de la variable $_POST
 * Ajoute aussi $AutresData dans l'insert si != array();
 * Définir la table d'insertion
 * Utiliser le tableau $Exception pour ne pas prendre en compte certain critères recus en POST (par exemple les bouton SUBMIT)
 * Utiliser le tableau $AutresData pour insérer des datas supplémentaires à celles envoyées en POST. Par exemple : $AutresData['dateins_prs'] = date("Y-m-d");
 * On peut construire la requête avec des WHERE $key = $valeur ou avec des WHERE $key IS NOT NUL si on passe IS NOT NULL dans le $_POST ou  $autresData;
 *
 * @example $AutresData['champ1'] = "IS NOT NULL";
 *          $query = adn_creerInsert('t_achat_ach', array('action'), $AutresData);
 *          $Check = mysql_query($query, $connexion) or die(mysql_error());
 * 
 * @version 13 juin 2011
 * @author Atelier Du Net
 *
 * @param <string> $table = nom de la table où l'on doit faire l'insertion
 * @param <array> $exception = tableau des champs du POST à ignorer (comme action, id, submit...)
 * @param <array> $autresData = data supplémentaires à insérer (en dur) - Accepte IS NULL, IS NOT NULL.
 * @return <string> $query = requête finalisée
 */
function adn_creerDelete($table, $exception=array(), $autresData=array()) {

    $str_where = "";

    foreach ($_POST as $key => $value) {
	if (!in_array($key, $exception)) {
	    $str_where = adn_creerDeleteCreerStr($str_where, $key, $value);
	}
    }

    if (count($autresData) > 0) {
	foreach ($autresData as $key => $value) {
	    $str_where = adn_creerDeleteCreerStr($str_where, $key, $value);
	}
    }

    $query = "DELETE FROM $table WHERE $str_where";
    return $query;
    
}

// Fonction de construction associée à adn_creerDelete
function adn_creerDeleteCreerStr($query, $key, $value) {
    if ($value != "") {
	// Gestion du "AND"
	if ($query != "") {
	    $query .= " AND ";
	}
	// Creation du bout de requete
	switch ($value) {
	    case "IS NULL":
	    case "IS NOT NULL":
		$query .= $key . " " . $value;
		break;
	    default :
		$query .= $key . "=" . $value;
		break;
	}
    }
    return $query;
}

?>