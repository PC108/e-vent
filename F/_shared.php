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
require_once('../../librairie/php/code_adn/AfficheSession.php');
require_once('../../librairie/php/code_adn/MyRedirection.php');
require_once('../../localisation/localisation.php');
require_once('_fonction/initSessionAdh.php');

/*	* ******************************************************** */
/*             Bloquage Front-office                    */
/*	* ******************************************************** */
if	($configAppli['ACCES']['front_office']	===	"non"	&&	!strpos($_SERVER['PHP_SELF'],	"maintenance"))	{
			adn_myRedirection('../general/maintenance.php');
}

/*	* ******************************************************** */
/*            Mise en session des informations génériques du site             */
/*	* ******************************************************** */
// Récupération des informations du client
if	(!isset($_SESSION['info_client']))	{
			$infoClient	=	array();
			$query	=	"
    SELECT *,
    (SELECT PAYS_nom_$langue FROM t_pays_pays WHERE FK_PAYS_id=PAYS_id) AS pays
    FROM t_client_cli";
			$RSclient	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			$nbreRows_RSclient	=	mysql_num_rows($RSclient);
			if	($nbreRows_RSclient	==	0)	{
						header("Content-Type: text/html; charset=utf-8");
						echo("<p>Configuration de l'application e-venement.com : ");
						echo('<br />Veuillez renseigner les informations de la société depuis l\'onglet CONFIGURATION du <a href="../../B/login/login.php">back-office</a> pour initialiser l\'application.</p>');
						exit;
			}	else	{
						$row	=	mysql_fetch_object($RSclient);
						$infoClient['nom']	=	$row->CLI_nom;
						$infoClient['suffixe']	=	$row->CLI_suffixe;
						$infoClient['adr1']	=	$row->CLI_adresse1;
						$infoClient['adr2']	=	$row->CLI_adresse2;
						$infoClient['zip']	=	$row->CLI_zip;
						$infoClient['ville']	=	$row->CLI_ville;
						$infoClient['pays']	=	$row->pays;
						$infoClient['tel']	=	$row->CLI_telephone;
						$infoClient['email_from']	=	$row->CLI_email_from;
						$infoClient['email_contact']	=	$row->CLI_email_contact;
						$infoClient['ordre_cheque']	=	$row->CLI_ordre_cheque;
						$_SESSION['info_client']	=	$infoClient;
			}
}
?>