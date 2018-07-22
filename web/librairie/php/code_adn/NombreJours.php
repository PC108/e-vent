<?php
/**
 * Retourne le nombre de jours d'écart entre deux jours
 *
 * @example $Nombres_jours =  adn_nbJours("2000-10-20", "2000-10-21");
 *
 * @author Atelier Du Net
 * @version 29/11/2009
 *
 * @param <date> $debut = date de début
 * @param <date> $fin = date de fin
 * @return <date> nombre de jours d'écart
 */
function adn_nbJours($debut, $fin) {

  $tDeb = explode("-", $debut);
  $tFin = explode("-", $fin);

  $diff = mktime(0, 0, 0, $tFin[1], $tFin[2], $tFin[0]) - 
          mktime(0, 0, 0, $tDeb[1], $tDeb[2], $tDeb[0]);
  
  return(($diff / 86400)+1);

}
?>