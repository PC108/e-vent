<?php
/**
 * Permet de construire une URL absolu pour gérer la navigateurs qui ne prennent pas en compte les URL relatifs
 *
 * @author Atelier Du Net
 * @version 12/05/2011
 * @param <type> $page = adresse ou fichier où l'on doit être redirigé
 */

function adn_myRedirection($page) {
    // Si on a pas de page en paramètre, on redirige sur la page qui a appellé adn_myRedirection.
    if (!isset($page)) {
	$chemin = "Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER["SCRIPT_NAME"];
    } else {
        // Si l'adresse en paramètre contient http://, on redirige sur cette adresse.
        if(strstr($page, "http://")) {
            $chemin = "Location: $page";
        } else {
            // Sinon on redirige sur le fichier fourni en paramètre DANS LE MEME répertoire que celui dans lequel on est déjà.
            $chemin = "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/" . $page;
        }
    }
    header($chemin);
    exit;
}

?>