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

?>