<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

// Recherche de tous les noms de fichiers affectés à des événements, on les stockent dans un tableau
$query	=	"SELECT EVEN_image FROM t_evenement_even";
$result	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$tabQuery	=	array();
while	($cmptLignes	=	mysql_fetch_array($result))	{
		array_push($tabQuery,	$cmptLignes['EVEN_image']);
}

// On parcours toutes les images dans le dossier upload et on stocke que celles qui :
// - ne sont pas . et ..
// - ne sont pas dans le tableau de la BD
// - ne sont pas des fichiers uploadés (sans préfixe)
// - ne sont pas les images par défaut
$MyDirectory	=	opendir("../../upload/img")	or	die('Erreur');
$tabFiles	=	array();
$prefix	=	array("crop_",	"ic_",	"resized_");
while	($entry	=	readdir($MyDirectory))	{
		if	($entry	!=	"."	&&	$entry	!=	".."	&&	$entry	!=	".svn"
										&&	(!in_array(str_replace($prefix,	"",	$entry),	$tabQuery)	||	(!strstr($entry,	"crop_")	&&	!strstr($entry,	"ic_")	&&	!strstr($entry,	"resized_")))
										&&	$entry	!=	"crop_00defaut.jpg"	&&	$entry	!=	"ic_00defaut.jpg")	{
				array_push($tabFiles,	$entry);
		}
}
closedir($MyDirectory);

// Si del=ok, on supprime tous les fichiers du tableau, puis on redirige sur la page avec le message d'état
if	(isset($_GET["del"])	&&	$_GET["del"]	==	"ok")	{
		if	(!count($tabFiles))	{
				$msg	=	"delimg_noimg";
		}	else	{
				$msg	=	"delimg_ok";
				foreach	($tabFiles	as	$value)	{
						if	(file_exists("../../upload/img/$value"))	{
								if	(!unlink("../../upload/img/$value"))	{
										$msg	=	"delimg_ko";
								}
						}	else	{
								$msg	=	"img_missing";
						}
				}
		}
		$_SESSION['message_user']	=	$msg;
		adn_myRedirection("cleanImg.php");
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['CONFIGURATION']['L4']['nom'];

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
		<head>
				<title><?php	echo	$titre;	?></title>
				<meta NAME="author" CONTENT="www.atelierdu.net" />
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
				<link rel="icon" type="image/png" href="../_media/favicon.png" />
				<!-- JS -->
				<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
				<script type="text/javascript" src="../_shared.js"></script>
				<!-- CSS -->
				<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
				<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
				<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
		</head>
		<body>
				<?php	include("../_header.php");	?>
				<div id="contenu">
						<p>Depuis cette page, vous pouvez supprimer toutes les anciennes images d'événements qui ont été remplacées et ne sont plus utilisées.</p>
						<ul class="menu_gauche">
								<li class="boutons"><a href="?del=ok">Supprimer les images ci-dessous</a></li>
						</ul>
						<h2>Résultat de la recherche</h2>
						<ul>
								<?php
								if	(count($tabFiles))	{
										foreach	($tabFiles	as	$entry)	{
												$tailleImg	=	getimagesize("../../upload/img/"	.	$entry);
												if	(strpos($entry,	"resized_")	===	0)	{
														$reduction	=	0.2;
												}	else	if	(strpos($entry,	"crop_")	===	0)	{
														$reduction	=	0.4;
												}	else	{
														$reduction	=	1;
												}
												$largeur	=	$tailleImg[0]	*	$reduction;
												$hauteur	=	$tailleImg[1]	*	$reduction;
												?>
												<li><img src="../../upload/img/<?php	echo	$entry	?>" alt="" width="<?php	echo	$largeur;	?>" height="<?php	echo	$hauteur	?>" /> Taille réelle : <?php	echo	$tailleImg[0]	?> x <?php	echo	$tailleImg[1]	?></li>
										<?php	}	?>
								</ul>
						<?php	}	else	{	?>
								<p>Aucune image à supprimer.</p>
						<?php	}	?>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
