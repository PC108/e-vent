<?php
/**
 * Vérifie si la string envoyée est de forme email.
 * Le code suivant est la version du 2 mai 2005 qui respecte les RFC 2822 et 1035
 * @link http://www.faqs.org/rfcs/rfc2822.html
 * @link http://www.faqs.org/rfcs/rfc1035.html
 *
 * @author bobocop@bobocop.cz
 * @version 30/04/2009
 *
 * @param <String> $email = email rentré en paramètre à analyser
 * @return <boolean> 1 si l'email est correct, 0 sinon.
 */
function adn_checkEmailPHP($email)
{
	$atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
	$domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)
																 
	$regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caractères autorisés avant l'arobase
	'(\.' . $atom . '+)*' .         // Suivis par zéro point ou plus
																	// séparés par des caractères autorisés avant l'arobase
	'@' .                           // Suivis d'un arobase
	'(' . $domain . '{1,63}\.)+' .  // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
																	// séparés par des points
	$domain . '{2,63}$/i';          // Suivi de 2 à 63 caractères autorisés pour le nom de domaine
	
	// test de l'adresse e-mail
	if (preg_match($regex, $email)) {
			return 1;
	} else {
			return 0;
	}
}

?>