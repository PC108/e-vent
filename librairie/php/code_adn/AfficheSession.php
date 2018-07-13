<?php

/**
	* Permet d'ajouter ou modifier dans l'URL une variable en GET sur une page. Boucle sur la page.
	* La variable est ensuite archivée en session pour être edisponible sur les autres pages
	*
	* @author Atelier Du Net
	* @version 21/06/2011
	*
	* @param <string> $valDefaut = valeur à assigner par défaut si aucune valeur en $_GET ou $_SESSION
	* @param <string> $nomSession = nom de la valeur de session ou stocker la variable
	* @return <string> la valeur
	*/
function	adn_ReadStoreValFromGET($valDefaut,	$nomSession)	{
			if	(isset($_GET[$nomSession]))	{
						$val	=	$_GET[$nomSession];
						$_SESSION[$nomSession]	=	$val;
			}	elseif	(isset($_SESSION[$nomSession])	&&	($_SESSION[$nomSession]	!=	""))	{
						$val	=	$_SESSION[$nomSession];
			}	else	{
						$val	=	$valDefaut;
						$_SESSION[$nomSession]	=	$val;
			};
			return	$val;
}

/**
	* Permet de modifier une valeur en GET dans un URL
	* Prends l'URL existant de la page et ajoute ou modifie la valeur sans toucher aux autres valeurs déjà présentes
	*
	* @author Atelier Du Net
	* @version 21/06/2011
	*
	* @param <string> $old = partie de l'URL à modifier (si il existe).
	* @param <string> $new = nouvelle partie de l'IRL à insérer ou à ajouter
	* @return <URL> URL complet à insérer dans le lien en GET
	*/
function	adn_UpdateValFromUrl($old,	$new)	{

			$query	=	$_SERVER['QUERY_STRING'];
			if	($query	==	"")	{
						$query	.=	$new;
			}	else	{
						$query	=	str_replace($old,	$new,	$query,	$count);
						if	($count	===	0)	{
									$query	.=	"&"	.	$new;
						}
			}

			return	("http://"	.	$_SERVER['HTTP_HOST']	.	$_SERVER["SCRIPT_NAME"]	.	"?"	.	$query);
}

/**
	* Affiche toutes les clés et les valeurs de la session et du cookie pour débugage
	* Mettre un include en bas des pages dont on souhaite voir le contenu de la session
	* $affiche = 0 : Désactivé;
	* $affiche = 1 : Actif uniquement en local;
	*
	* @version 12/09/2011
	* @author Atelier Du Net
	*
	* @return string = tableau en html avec les valeurs de COOKIE, de SESSION, de GET et de POST.
	*/
function	adn_afficheSession()	{

			$actif	=	1;

			//if	(($actif	==	1))	{	// Actif aussi en distant
			if	(($actif	==	1)	&&	($_SERVER['REMOTE_ADDR']	==	'127.0.0.1'))	{	// Actif uniquement en local
						$affiche	=	adn_ReadStoreValFromGET(0,	'afficheSession');

						switch	($affiche)	{
									case	2:
												$url	=	adn_UpdateValFromUrl("afficheSession=2",	"afficheSession=0");
												break;
									case	1:
												$url	=	adn_UpdateValFromUrl("afficheSession=1",	"afficheSession=2");
												break;
									case	0:
												$url	=	adn_UpdateValFromUrl("afficheSession=0",	"afficheSession=1");
												break;
						}

						// Affichage du bloc info

						$str	=	'<div style="position: fixed;top: 50px;right: 5px;width: 250px; z-index:50;">';

						switch	($affiche)	{
									case	2:
												$str	.=	'<div style="text-align:right;font-size:12px;"><a href="'	.	$url	.	'">cacher les infos</a></div>';
												break;
									case	1:
												$str	.=	'<div style="text-align:right;font-size:12px;"><a href="'	.	$url	.	'">afficher les requêtes</a></div>';
												if	(isset($_COOKIE)	&&	count($_COOKIE)	>	0)	{
															$str	.=	adn_construitTableauSession($_COOKIE,	'COOKIE');
												}	else	{
															$str	.=	'<div style="background: #FFF;border: 1px solid #000;margin-top:10px;font-size: 12px;padding: 0 7px;">Pas de valeurs dans <b>COOKIE</b></div>';
												};
												if	(isset($_SESSION)	&&	count($_SESSION)	>	0)	{
															$str	.=	adn_construitTableauSession($_SESSION,	'SESSION');
												}	else	{
															$str	.=	'<div style="background: #FFF;border: 1px solid #000;margin-top:10px;font-size: 12px;padding: 0 7px;">Pas de valeurs dans <b>SESSION</b></div>';
												};
												if	(isset($_GET)	&&	count($_GET)	>	0)	{
															$str	.=	adn_construitTableauSession($_GET,	'GET');
												}	else	{
															$str	.=	'<div style="background: #FFF;border: 1px solid #000;margin-top:10px;font-size: 12px;padding: 0 7px;">Pas de valeurs dans <b>GET</b></div>';
												};
												if	(isset($_POST)	&&	count($_POST)	>	0)	{
															$str	.=	adn_construitTableauSession($_POST,	'POST');
												}	else	{
															$str	.=	'<div style="background: #FFF;border: 1px solid #000;margin-top:10px;font-size: 12px;padding: 0 7px;">Pas de valeurs dans <b>POST</b></div>';
												};
												break;
									case	0:
												$str	.=	'<div style="text-align:right;font-size:12px;"><a href="'	.	$url	.	'">afficher les infos</a></div>';
												break;
						}

						$str	.=	"</div>";
						return	$str;
			}
}

/**
	* Tableau de COOKIE ou de SESSION
	*
	* @version 12 avril 2011
	* @author Atelier Du Net
	*
	* @param <array> $data = $_SESSION ou $_COOKIE
	* @param <string> $titre = SESSION ou COOKIE
	* @return string = Tableau en html de COOKIE ou de SESSION
	*/
function	adn_construitTableauSession($data,	$titre)	{

			$sstr	=	$titre;
			$sstr	.=	'<div style="background: #FFF;border: 1px solid #000;overflow-x: scroll;">';
			$sstr	.=	'<table cellpadding="0" cellspacing="0" width="90%">';
			foreach	($data	as	$cle	=>	$valeur)	{
						if	(is_array($valeur))	{
									$valeur	=	print_r($valeur,	true);
						}
						$sstr	.=	'<tr>';
						$sstr	.=	'<td nowrap="nowrap"  style="font-size: 12px;color: #000;border-bottom: 1px solid #CCC;padding: 0 7px;"><b>'	.	$cle	.	'</b></td>';
						$sstr	.=	'<td nowrap="nowrap"  style="font-size: 12px;color: #000;border-bottom: 1px solid #CCC;padding: 0 7px;">'	.	$valeur	.	'</td>';
						$sstr	.=	'</tr>';
			}
			$sstr	.=	"</table>";
			$sstr	.=	"</div>";

			return	$sstr;
}

?>