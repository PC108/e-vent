<?php
/**
 * Supprime les retours de chariots dans une String
 * Utile pour créer une extraction .CSV à partir d'une table contenant des Text Area
 * Protège la variable
 *
 * @author Atelier Du Net
 * @version 27/05/2009
 *
 * @param <string> $String = chaîne à traiter
 * @return <string> Chaîne sans retour chariot
 */
function adn_supprimeRetourChariot($String)
{
   $Bad = array("\r\n", "\n", "\r");
   $NewStr = str_replace($Bad, " ", $String);
   return $NewStr;
}
?>