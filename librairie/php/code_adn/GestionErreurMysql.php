<?php

/**
	* Gestion de l'éxécution d'une requete mySql et de ses erreurs.
	* Ne pas mettre cette fonction sur les requetes de type SELECT ou retournant un jeu de résultats. Dans ce cas là, on doit retourner une ressource.
	*
	* ATTENTION : Nécessite d'avoir mis session_start(); en début de page
	*
	* @version 17 juin 2011
	* @author Atelier Du Net
	*
	* @param <string> $query = requête à éxécuter
	* @param <ressource> $connexion = connexion à la base de données courante
	* @param <array> $codeMessages = tableau de 2 entrées comprenant le code du message ok en 1 et le code du message bad en 2
	* @param <string> $sessionMessages = tableau contenant les 2 noms de session pour stocker le code du message en 1 et l'erreur renvoyée en 2
	* @return <string> retourne true pour le chemin normal, false en cas d'erreur.
	*/
function	adn_mysql_query($query,	$connexion,	$codeMessages,	$sessionMessages)	{
			if	(mysql_query($query,	$connexion))	{
						// Supprime les information d'erreur stoquées en session si nécessaire
						if	(isset($_SESSION[$sessionMessages[1]]))	{
									unset($_SESSION[$sessionMessages[1]]);
						}
						$_SESSION[$sessionMessages[0]]	=	$codeMessages[0];
						return	true;
			}	else	{
						$errno	=	mysql_errno($connexion);
						$err	=	mysql_error($connexion);

						$codeMessageKo	=	adn_mysql_error($errno,	$codeMessages[1]);
						$_SESSION[$sessionMessages[0]]	=	$codeMessageKo;

						if	(isset($err)	&&	isset($errno))	{
									$_SESSION[$sessionMessages[1]]	=	$errno	.	" : "	.	$err;
						}
						return	false;
			}
}

/**
	* Gestion des codes de message d'erreurs en fonction des numéros d'erreurs MySql
	*
	* @version 16 juin 2011
	* @author Atelier Du Net
	*
	* @param <int> $errno = numéro de l'erreur MySql générée
	* @param <string> $msg = message d'erreur standard
	* @return <string> code de message soit d'erreur standard, soit d'erreur personnalisée
	*/
function	adn_mysql_error($errno,	$codeMessage)	{
			switch	($errno)	{
						case	1451:
						case	1217:
									return	"delConstraints_ko";
									break;
						case	1452:
						case	1216:
									return	"bug";
									break;
						default	:
									return	$codeMessage;
									break;
			}
}

?>