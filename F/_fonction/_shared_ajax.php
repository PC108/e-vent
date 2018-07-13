<?php
/*	* ******************************************************** */
/*              YAML                   */
/*	* ******************************************************** */
require_once('../../librairie/php/yaml/sfYaml.php');
require_once('../../librairie/php/yaml/sfYamlParser.php');
$yaml	=	new	sfYamlParser();
$configAppli	=	$yaml->parse(file_get_contents('../../config/config2.yml'));

/*	* ******************************************************** */
/*              Connexion DB                   */
/*	* ******************************************************** */
require_once($configAppli['DATABASE']['chemin']);

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
require_once('../_session.php');
?>
