<?php

// Récupère la valeur de la langue ou l'injitie en fr.
$langue = adn_ReadStoreValFromGET('fr', 'lang');
chooseTranslate($langue);

/**
 * @version 18 Avril 2011
 * @author Atelier Du Net
 *
 * Fonction pour mettre en place l'environnement de langue afin de traduire le site avec GetText.
 *
 * @param <string> $lang = "fr"/"en"/"de"/"sp"/etc...
 */
function chooseTranslate($lang) {
    // On cherche à savoir sur quel OS tourne le serveur
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
	$os = "win";
    } else {
	$os = "unix";
    }

    // En fonction de la langue passé en paramètre et de l'os, on choisis le bon dossier de traductions
    switch ($lang) {
	case "fr":
	    if ($os == "win")
		$langage = 'French_France';
	    else
		$langage = 'fr_FR';
	    break;
	case "en":
	    if ($os == "win")
		$langage = 'English_United_States';
	    else
		$langage = 'en_US';
	    break;
	default :
	    if ($os == "win")
		$langage = 'French_France';
	    else
		$langage = 'fr_FR';
	    break;
    }

    putenv("LANG=$langage"); // On modifie la variable d'environnement
    setlocale(LC_ALL, $langage); // On modifie les informations de localisation en fonction de la langue

    $nomDesFichiersDeLangue = 'traduction_24'; // Le nom de nos fichiers .mo

    $dir = bindtextdomain($nomDesFichiersDeLangue, "../../localisation") . "<br/>"; // On indique le chemin vers les fichiers .mo
    textdomain($nomDesFichiersDeLangue); // Le nom du domaine par défaut
}

function getSiteUserId($lang) {
    switch ($lang) {
	case "fr":
	    $id = 1;
	    break;
	case "en":
	    $id = 2;
	    break;
	default :
	    $id = 1;
	    break;
    }
    return $id;
}

?>
