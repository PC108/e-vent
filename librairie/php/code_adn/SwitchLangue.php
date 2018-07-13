<?php
/**
 * Bascule sur la page correspondante dans une autre langue
 * Il faut que le site soit structuré avec une langue par répertoire au même niveau.
 * Indiquer le nom du repertoire courant et du repertoire de destination.
 * Si la page de destination n'existe pas, renvoie à la page index.php du répertoire courant.
 *
 * @author Atelier Du Net
 * @version 16/11/2009
 *
 * @param <string> $CurrentLangue = répertoire courant
 * @param <string> $DestLangue = dossier de destination
 * @return string page avec la nouvelle langue
 */
function adn_switchLangue($CurrentLangue, $DestLangue)
{
    $Chemin = strstr($_SERVER['PHP_SELF'], '/'.$CurrentLangue);
    $NewChemin = str_replace("/".$CurrentLangue."/", "/".$DestLangue."/", $Chemin);
    $NewChemin = "../.." .$NewChemin;

    $Check = @fopen($NewChemin, 'r'); // Attention, ne marche que pour les chemins relatifs type "../../nomfichier.php"
    if ($Check) {
        return $NewChemin;
    } else {
        return dirname($NewChemin)."/index.php";
    }
}

// Creer la fonction adn_switchLangueGetText

// Récupérer le script de gestion de la variable $lang dans localisation.php et l'insérer ici ?

?>