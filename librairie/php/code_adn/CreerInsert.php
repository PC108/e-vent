<?php
/**
 * Crée une requête SQL INSERT à partir de la variable $_POST
 * Ajoute aussi $AutresData dans l'insert si != array();
 * Définir la table d'insertion
 * Utiliser le tableau $Exception pour ne pas prendre en compte certain critères recus en POST (par exemple les bouton SUBMIT)
 * Utiliser le tableau $AutresData pour insérer des datas supplémentaires à celles envoyées en POST. Par exemple : $AutresData['dateins_prs'] = date("Y-m-d");
 * Utiliser le tableau $FormatData pour formater les casses d'une donnée. valeurs possible = UPPER, LOWER, LOWERFIRST et POINTZIP
 * Attention d'insérer la fonction AddPointZipCode() si utilisation de POINTZIP
 *
 * @example $AutresData['dateins_prs'] = date("Y-m-d");
 *          $FormatData['md_nom_prs'] = 'LOWERFIRST';
 *          $FormatData['md_ville_prs'] = 'UPPER';
 *          $FormatData['nom_prs'] = 'UPPER';
 *          $FormatData['prenom_prs'] = 'LOWERFIRST';
 *          $FormatData['email_prs'] = 'LOWER';
 *          $Query_RS2 = adn_creerInsert('p6_presse', array('submit'), $AutresData, $FormatData);
 *          $Check2 = mysql_query($Query_RS2, $connexion) or die(mysql_error());
 * 
 * @version 7 mars 2011
 * @author Atelier Du Net
 *
 * @param <string> $table = nom de la table où l'on doit faire l'insertion
 * @param <array> $exception = tableau des champs du POST à ignorer (comme action, id, submit...)
 * @param <array> $autresData = data supplémentaires à insérer (en dur).
 * @param <array> $formatData = format des datas
 * @return <string> $query = requête finalisée
 */
function adn_creerInsert($table, $exception=array(), $autresData=array(), $formatData=array()) {

    $str_champs = "";
    $str_valeurs = "";

    foreach ($_POST as $key => $value) {
	if (!in_array($key, $exception)) {

	    // A garder ici pour la fonction trim qui cleane les valeurs avec uniquement des espaces + gestion des array
	    if (is_array($value)) {
		$value = serialize($value);
	    } else {
		$value = adn_quote_smart($value);
	    }

	    if ($value != "") {

		// formatage
		if (array_key_exists($key, $formatData)) {
		    switch ($formatData[$key]) {
			case 'UPPER':
			    $value = mb_strtoupper($value, 'UTF-8');
			    break;
			case 'LOWER':
			    $value = mb_strtolower($value, 'UTF-8');
			    break;
			case 'LOWERFIRST':
			    $value = ucfirst(mb_strtolower($value, 'UTF-8'));
			    break;
			case 'POINTZIP':
			    $value = AddPointZipCode($value);
			    break;
		    }
		}

		$str_champs .= $key . ",";
		$str_valeurs .= "'" . $value . "'" . ",";
	    }
	}
    }

    if (count($autresData) > 0) {
	foreach ($autresData as $key => $value) {
	    $str_champs .= $key . ",";
	    $str_valeurs .= "'" . adn_quote_smart($value) . "'" . ",";
	}
    }

    $str_champs = rtrim($str_champs, ",");
    $str_valeurs = rtrim($str_valeurs, ",");

    $query = "INSERT INTO $table ($str_champs) VALUES ($str_valeurs)";
    return $query;
}
?>