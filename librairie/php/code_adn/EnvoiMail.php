<?php
/**
 * Fonction pour envoyer un email simple
 * Note : le @ avant la fonction mail signifie qu'on n'affiche pas les messages d'erreurs. On évite ainsi l'affichage des warnings.
 * En revanche une erreur fatale ne sera pas affichée et provoquera l'interruption du code. A manipuler avec prudence.
 *
 * Note : Utiliser la fonction adn_checkEmailPHP avant d'utiliser cette fonction
 *
 * @author Atelier Du Net
 * @version 16 mai 2011
 *
 * @param <string> $destinataire = destinataire(s) du mail. Si plusieurs, séparer les adresses avec une virgule.
 * @param <string> $sujet = sujet du mail.
 * @param <string> $message = texte du mail.
 * @param <string> $from = adresse(s) servant à envoyer le mail. Si plusieurs, séparer les adresses avec une virgule.
 * @return <boolean> TRUE si la fonction mail a envoyé le mail (même si ça ne veut pas dire que l'email arrivera à destination), FALSE sinon.
 *
 * @link http://php.net/manual/fr/function.mail.php
 */
function adn_envoiMail($destinataire, $sujet, $message, $from){
    // Headers pour eviter de passer pour du spam
    $headers = "From: $from" . "\r\n" .
	"Reply-To: $from" . "\r\n" .
	"X-Mailer: PHP/" . phpversion();

    // Protection contre les injections d'header
    // http://www.phpsecure.info/v2/article/MailHeadersInject.php
    if (strpos ($from, "\r") || strpos ($from, "\n")){
        return(FALSE);
    }
    // Utilisation du @ pour empecher l'affichage des warnings
    return @mail($destinataire,utf8_decode($sujet),utf8_decode($message),$headers);
}
?>