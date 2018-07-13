<?php
/// Fonctions de traitement pour l'envoi de données en CSV

/**
 * Fonction qui retourne le caractère spécial correspondant au saut de ligne suivant l'OS.
 *
 * @return string
 */
function adn_getStrRetour(){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $str_retour = "\r\n";
    } else {
        $str_retour = "\n";
    }
    return $str_retour;
}

/**
 * Fonction de traitement des données à exporter en CSV
 *  - Si un chiffre commence par 0, on met un "." devant pour pas qu'excel formate mal les données
 *  - On remplace les caractères indésirables (saut de ligne, ";")
 *
 * @author Atelier Du Net
 * @version 5 mai 2011
 * 
 * @param <String> $str = Chaine à traiter
 * @return <String> Chaine traitée
 */
function adn_checkData($str){
    $trt = "";
    if(strlen($str) > 0){
        if(substr_compare($str, "0", 0, 1) == 0)
            $trt = ".".$str;
        else
            $trt = $str;
    }
    return str_replace(array("\r\n", "\n", "\r")," ",str_replace(";", "", $trt));;
}
?>
