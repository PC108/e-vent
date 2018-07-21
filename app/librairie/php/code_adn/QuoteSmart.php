<?php

/**
 * Commentaire : "Méthode la plus propre pour envoyer une requête à la base, indépendamment de votre configuration des guillemets magiques."
 * A utiliser pour protèger les caractères spéciaux d'une commande SQL (faille de sécurité)
 *
 * @author Manuel PHP
 * @version 03/02/2011
 *
 * @param <string> $value = valeur à entrer dans le champ SQL et à analyser afin de supprimer les caractères spéciaux
 * @return <string> $value = chaîne inoffensive après le traitement
 */
function adn_quote_smart($value) {

    // Supprime les espaces en début et fin chaine
    $value = trim($value);

    // Stripslashes
    if (get_magic_quotes_gpc ()) {
	$value = stripslashes($value);
    }
    // Protection si ce n'est pas un entier
    if (!is_numeric($value)) {
	$value = mysql_real_escape_string($value);
    }
    // Enlève les tags
    $value = str_replace(array("<", ">"), "", $value);

    return $value;
}

/**
 * Reprend la fonction adn_quote_smart en y ajoutant le contrôle suivant :
 * Si la variable est vide, remplace son contenu par la chaine de caractère $defaut
 *
 * @example $defaut = "VIDE" ou "Tous"
 *
 * @author Atelier Du Net
 * @version 21/09/2010
 *
 * @param <string> $value = valeur à entrer dans le champ SQL et à analyser afin de supprimer les caractères spéciaux
 * @param <string> $defaut = valeur de retour par défaut
 * @return <string> $value = chaîne inoffensive après le traitement
 */
function adn_quote_smart_DEFAUT($value, $defaut) {

    // Supprime les espaces en début et fin chaine
    $value = trim($value);

    if (!isSet($value)) {
	$value = $defaut;
    } else if ($value == "") {
	$value = $defaut;
    } else {
	// Stripslashes
	if (get_magic_quotes_gpc ()) {
	    $value = stripslashes($value);
	}
	// Protection si ce n'est pas un entier
	if (!is_numeric($value)) {
	    $value = mysql_real_escape_string($value);
	}
    }
    return $value;
}

?>