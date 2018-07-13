<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

// Recherche de tous les noms de fichiers affectés à des événements, on les stockent dans un tableau.
$query_RS1	=	"SELECT EVEN_id, EVEN_image, EVEN_nom_fr FROM t_evenement_even";
$RS1	=	mysql_query($query_RS1,	$connexion)	or	die(mysql_error());
$imgToKeep	=	array();
$eventLinkedToImg	=	array();
while	($row_RS1	=	mysql_fetch_array($RS1))	{
			if	(!in_array($row_RS1['EVEN_image'],	$imgToKeep))	{
						array_push($imgToKeep,	$row_RS1['EVEN_image']);
						// Crée un tableau contenant en clé la référence des images pour y associer les événements dans la boucle suivante
						$eventLinkedToImg[$row_RS1['EVEN_image']]	=	array();
			}
}
// Associe la liste des événements correspondnat à chaque image
mysql_data_seek($RS1,	0);
while	($row_RS1	=	mysql_fetch_array($RS1))	{
			array_push($eventLinkedToImg[$row_RS1['EVEN_image']],	$row_RS1['EVEN_nom_fr']);
}
// On parcours toutes les images dans le dossier upload et on stocke que celles qui :
// - ne sont pas . et ..
// - ne sont pas dans le tableau de la BD
// - ne sont pas des fichiers uploadés (sans préfixe)
// - ne sont pas les images par défaut
$imgToDelete	=	array();
$fileToDelete	=	array();	// S'il ya d'autres fichiers qui trainent dans le dossier par mégarde
$allFiles	=	scandir("../../upload/img");
foreach	($allFiles	as	$value)	{
			if	($value	!=	"."	&&	$value	!=	".."	&&	$value	!=	".svn"	&&	$value	!=	"crop_00defaut.jpg"	&&	$value	!=	"ic_00defaut.jpg")	{
						if	(!strstr($value,	"crop_")	&&	!strstr($value,	"ic_"))	{	// Fichier inconnu
									array_push($fileToDelete,	$value);
						}	else	{
									$nomGenerique	=	str_replace(array("crop_",	"ic_",	"resized_"),	"",	$value);
									if	(!in_array($nomGenerique,	$imgToKeep)	&&	!in_array($nomGenerique,	$imgToDelete))	{
												array_push($imgToDelete,	$nomGenerique);
									}
						}
			}
}

//var_dump($imgToDelete);
//exit;

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L7']['nom'];

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
						<script type="text/javascript" src="../_fonction/ns_back.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">

									$(document).ready(function() {
												// Alerte avant de supprimer
												NS_BACK.checkSupprime($('.bt_delete_img'), 'action.php?action=del_img');
												NS_BACK.checkSupprime($('.bt_delete_file'), 'action.php?action=del_file');
									});
						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
						<style type="text/css">
									.bloc_img_linked {
												display: inline-block;
												padding: 5px;
												border: 1px dotted #999999;
												width: 240px;
												vertical-align: top;
												margin: 0 10px 10px 0;
									}
									.bloc_img_linked ol {padding-left: 30px;}
						</style>
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<ul class="menu_gauche">
												<li class="boutons"><a href="upload_img.php">Ajouter une image <img src="../_media/bo_add.png" width="16" height="16" border="0" align="absmiddle" alt="ajouter"/></a></li>
									</ul>
									<h2>Images <b>non associées</b> à un événement</h2>
									<p class="note">Ces images peuvent être supprimées ou associées à un événement. Dans le 2ème cas, allez sur le formulaire de modification de l'événement concerné.</p>
									<?php
									if	(count($imgToDelete)	>	0)	{
												foreach	($imgToDelete	as	$value)	{
															$html	=	'<div class="bloc_img_linked">';
															$html	.=	'<img src="../../upload/img/crop_'	.	$value	.	'" />';
															$html	.=	'<img class="bt_delete_img bt_remove_img pointer" src="../_media/ic_remove.png" id_todelete="'	.	$value	.	'"/>';
															$html	.=	'</div>';
															echo	$html;
												}
									}	else	{
												echo	'<p>Aucune image à supprimer.</p>';
									}
									?>
									<h2>Autres fichiers à supprimer</h2>
									<p class="note">Si des fichiers ont été trouvés dans la liste ci-dessous, ils ne sont pas utilisés et peuvent être supprimés.</p>
									<?php
									if	(count($fileToDelete)	>	0)	{
												echo	'<ul>';
												foreach	($fileToDelete	as	$value)	{
															echo	'<li>'	.	$value	.	' <img class="bt_delete_file pointer align_img" src="../_media/bo_delete.png" id_todelete="'	.	$value	.	'" width="16" height="16" border="0"alt=""/></li>';
												}
												echo	'</ul>';
									}	else	{
												echo	'<p>Aucun fichier à supprimer.</p>';
									}
									?>
									<h2>Images <b>associées</b> à un événement ou plus</h2>
									<p class="note">Ces images ne peuvent être supprimées car elles sont associées à des événements passés ou en cours.
												<br />Vous pouvez assigner une image à plusieurs événements si vous le souhaitez.</p>
									<?php
									if	(count($imgToKeep)	>	0)	{
												foreach	($imgToKeep	as	$value)	{
															$html	=	'<div class="bloc_img_linked">';
															$html	.=	'<img src="../../upload/img/crop_'	.	$value	.	'" />';
															$html	.=	'<ol>';
															foreach	($eventLinkedToImg[$value]	as	$listEvent)	{
																		$html	.=	'<li>'	.	$listEvent	.	'</li>';
															}
															$html	.=	'</ol></div>';
															echo	$html;
												}
									}	else	{
												echo	'<p>Aucune image associée à un événement.</p>';
									}
									?>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
