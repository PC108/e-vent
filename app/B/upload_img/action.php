<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*             Vérification des variables                  */
/*	* ******************************************************** */
$checkRequired	=	"ok";

// récupération des variables GET
if	(isSet($_GET["action"])	&&	isSet($_GET["id"]))	{
			$action	=	$_GET['action'];
			$id	=	$_GET['id'];
}	else	{
			$checkRequired	=	"bad";
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
if	($checkRequired	==	"bad")	{
			$_SESSION['message_user']	=	"bad_post";
}	else	{
			$repertoireDest	=	"../../upload/img/";
			switch	($action)	{
						case	"del_img":
									$prefix	=	array("crop_",	"ic_",	"resized_",	"");	// Supprime tou sles fichiers possibles de l'image
									$check	=	true;
									foreach	($prefix	as	$value)	{
												if	($check)	{
															if	(file_exists($repertoireDest	.	$value	.	$id))	{
																		$check	=	unlink($repertoireDest	.	$value	.	$id);
															}
												}
									}
									break;

						case	"del_file":
									if	(file_exists($repertoireDest	.	$id))	{	// Supprime uniquement le fichier
												$check	=	unlink($repertoireDest	.	$id);
									}
									break;
			}

			if	($check)	{
						$_SESSION['message_user']	=	"del_ok";
			}	else	{
						$_SESSION['message_user']	=	"del_ko";
			}
}
 adn_myRedirection("result.php");
?>