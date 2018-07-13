<?php

// Retourne le nom de la page courante et éventuellement les variables en GET
// ex : "http://localhost/e-venement/ftp/dev/test/SERVER.php?test=3" renvoie "SERVER.php?test=3"
function adn_getPage($url) {
    return ltrim(strrchr($url, '/'), "/");
}

?>
