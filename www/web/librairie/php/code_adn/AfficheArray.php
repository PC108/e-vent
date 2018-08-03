<?php
/**
 * Affiche simplement le contenu d'un Array (checkbox) dans une colonne
 *
 * @author Atelier Du Net
 * @version 30 décembre 2010
 *
 * @param <array> $Array = tableau passé en paramètre
 * @return string
 */
function adn_afficheArray($Array) {

    $result = "";

    if (count($Array) > 0) {
	foreach ($Array as $value) {
	    $result .= $value . "<br /> ";
	}
	// vire le dernier  "<br /> ";
	$result = substr_replace($result, '', -7, -1);
    } else {
	$result = "---";
    }

    return $result;

}
?>