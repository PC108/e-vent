<?php

/*	* ******************************************************** */
/*              Dates                   */
/*	* ******************************************************** */
date_default_timezone_set('Europe/Paris');

/*	* ******************************************************** */
/*              YAML                   */
/*	* ******************************************************** */
require_once('../../librairie/php/yaml/sfYaml.php');
require_once('../../librairie/php/yaml/sfYamlParser.php');
$yaml	=	new	sfYamlParser();
$menuInfos	=	$yaml->parse(file_get_contents('../login/menu.yml'));
$configAppli	=	$yaml->parse(file_get_contents('../../config/config2.yml'));

/*	* ******************************************************** */
/*              Connexion DB                   */
/*	* ******************************************************** */
require_once($configAppli['DATABASE']['chemin']);

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
require_once('../../librairie/php/code_adn/MyRedirection.php');
// A placer toujours après MyRedirection.php et avant adn_afficheSession()
require_once('../_session.php');

/*	* ******************************************************** */
/*             Bloquage Back-office                    */
/*	* ******************************************************** */
if	($configAppli['ACCES']['back_office']	===	"non" && !strpos($_SERVER['PHP_SELF'], "maintenance"))	{
			adn_myRedirection('../../F/general/maintenance.php');
}

/*	* ******************************************************** */
/*            Mise en session des informations génériques du site             */
/*	* ******************************************************** */
// Récupération des informations du client
if	(!isset($_SESSION['info_client']))	{
		$infoClient	=	array();
		$query	=	"
    SELECT * FROM t_client_cli";
		$RSclient	=	mysql_query($query,	$connexion)	or	die(mysql_error());
		$nbreRows_RSclient	=	mysql_num_rows($RSclient);
		if	($nbreRows_RSclient	==	0)	{
				$infoClient['nom']	=	"inconnu";
		}	else	{
				$row	=	mysql_fetch_object($RSclient);
				$infoClient['nom']	=	$row->CLI_nom;
				$_SESSION['info_client']	=	$infoClient;
		}
}

/*	* ******************************************************** */
/*              Affiche Session                   */
/*	* ******************************************************** */
// Sauf pour les pages publipostage.php et csv.php
if	((!strpos($_SERVER['PHP_SELF'],	"publipostage.php"))
								&&	(!strpos($_SERVER['PHP_SELF'],	"csv.php"))
								&&	(!strpos($_SERVER['PHP_SELF'],	"csv_mois_liste.php"))
								&&	(!strpos($_SERVER['PHP_SELF'],	"csv_event_liste.php"))
								&&	(!strpos($_SERVER['PHP_SELF'],	"csv_mois_detail.php"))
								&&	(!strpos($_SERVER['PHP_SELF'],	"csv_event_detail.php"))
								)	{
		require_once('../../librairie/php/code_adn/AfficheSession.php');
}
?>