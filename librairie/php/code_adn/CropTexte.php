<?php
/**
 * Crope une phrase au nombre de caractères définis et rajoute (...) s'il y a eu crop
 *
 * @version 07/10/2009
 * @author Atelier Du Net
 *
 * @param <type> $String = Chaîne à croper
 * @param <type> $CharLimit = Limite du nombre de caractères
 * @return <string> $NewString = chaîne cropée
 */
function adn_cropTexte ($String, $CharLimit)
{
	$NbreChar = strlen($String);
	if ($NbreChar > $CharLimit) {
		//recherche le prochain " "
		$CharFin = strpos($String, ' ', $CharLimit); 
		if ($CharFin) {
			$NewString = substr($String, 0, $CharFin)."..."; //crope au dernier espace
		} else {
			$NewString = substr($String, 0, $CharLimit)."..."; //s'il n'y a pas d'espace, crope exactement au nombre de caractères limites
		}
	} else {
		$NewString = $String;
	}
	return $NewString;
}
?>