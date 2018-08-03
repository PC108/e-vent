<?php
/*	* ******************************************************** */
/*              PARAMETRES DE L'UPLOAD                     */
/*	* ******************************************************** */
$repertoireDest	=	"../../upload/img/";
$prefixImgResized	=	"resized_";
$defautCible	=	array(400,	400,	TRUE);	//Dimensions et positionnement du crop par défaut. Centré = TRUE, en haut à gauche = FALSE. Le crop est carré. A vérifier avec le paramêtre <aspectRatio> s'il est défini dans imgAreaSelect
$dimensionCrop	=	array(240,	240);	//array de la largeur et hauteur de l'image cropée, qui correspond aussi à la largeur et hauteur du preview
$prefixImgCrop	=	"crop_";
$dimensionIcon	=	array(40,	40);	//array de la largeur et hauteur de l'icone. Mettre FALSE si on n'a pas besoin d'icone.
$prefixIcone	=	"ic_";

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


if	(!isset($_POST['bt_crop']))	{	// on arrive depuis upload_img.php

			/*				* ******************************************************** */
			/*              Définition des variables                   */
			/*				* ******************************************************** */
			$img	=	$_GET['img'];
//		$action	=	$_GET['action'];
//		$pageNum	=	$_GET['pageNum'];
//		$id	=	$_GET['id'];
//		$ParamEnGET	=	"?action="	.	$action	.	"&pageNum="	.	$pageNum	.	"&id="	.	$id;

			/*				* ******************************************************** */
			/*            POSITIONNEMENT OUTIL DE CROP                 */
			/*				* ******************************************************** */
			$resizedFile	=	$repertoireDest	.	$prefixImgResized	.	$img	.	".jpg";
			$tailleImg	=	getimagesize($resizedFile);
			$newLargeur	=	$tailleImg[0];
			$newHauteur	=	$tailleImg[1];

// Calcul du positionnement par défaut de la cible du crop
			if	($defautCible[0]	>	$newLargeur)	{
						$largeurCible	=	$newLargeur;
						$hauteurCible	=	$newLargeur;
			}	else	if	($defautCible[1]	>	$newHauteur)	{
						$largeurCible	=	$newHauteur;
						$hauteurCible	=	$newHauteur;
			}	else	{
						$largeurCible	=	$defautCible[0];
						$hauteurCible	=	$defautCible[1];
			}
			if	($defautCible[2])	{
						$x1_crop	=	($newLargeur	-	$largeurCible)	/	2;
						$y1_crop	=	($newHauteur	-	$hauteurCible)	/	2;
						$x2_crop	=	($newLargeur	+	$largeurCible)	/	2;
						$y2_crop	=	($newHauteur	+	$hauteurCible)	/	2;
			}	else	{
						$x1_crop	=	0;
						$y1_crop	=	0;
						$x2_crop	=	$largeurCible;
						$y2_crop	=	$hauteurCrop;
			}
}	else	{	// On boucle depuis cette page via le formulaire

			/*				* ******************************************************** */
			/*       CREATION DU CROP ET DE l'ICONE + MAJ DB           */
			/*				* ******************************************************** */
			$CheckUpload	=	"Ok";

			$x1	=	$_POST["x1"];
			$y1	=	$_POST["y1"];
			$w	=	$_POST["w"];
			$h	=	$_POST["h"];
			$resizedFile	=	$_POST["resizedFile"];
			$img	=	$_POST["img"];
			//$action	=	$_POST['action'];
			//$pageNum	=	$_POST['pagenum'];
			//$id	=	$_POST['id'];

			echo	$action;

			// Creation du crop
			$imgSource	=	imagecreatefromjpeg($resizedFile);
			$newImage	=	imagecreatetruecolor($dimensionCrop[0],	$dimensionCrop[1]);
			imagecopyresampled($newImage,	$imgSource,	0,	0,	$x1,	$y1,	$dimensionCrop[0],	$dimensionCrop[1],	$w,	$h);
			// Enregistrement du crop
			$cropFile	=	$repertoireDest	.	$prefixImgCrop	.	$img	.	".jpg";
			if	(!imagejpeg($newImage,	$cropFile,	100))	{	$CheckUpload	=	"Un problème est survenu lors de l'enregistrement du crop.";	}

			// Creation de l'icone
			if	($dimensionIcon)	{
						$newImage	=	imagecreatetruecolor($dimensionIcon[0],	$dimensionIcon[1]);
						imagecopyresampled($newImage,	$imgSource,	0,	0,	$x1,	$y1,	$dimensionIcon[0],	$dimensionIcon[1],	$w,	$h);
						// Enregistrement du crop
						$iconFile	=	$repertoireDest	.	$prefixIcone	.	$img	.	".jpg";
						if	(!imagejpeg($newImage,	$iconFile,	100))	{	$CheckUpload	=	"Un problème est survenu lors de l'enregistrement de l'icone.";	}
			}

//			// Maj de la base de donnée
//			if	($CheckUpload	==	"Ok")	{
//						$data	=	$img	.	".jpg";
//						$query	=	"UPDATE t_evenement_even SET EVEN_image='$data' WHERE EVEN_id=$id";
//						// mysql_query($query,	$connexion)	or	die(mysql_error());
//						// adn_myRedirection("../evenement/add.php?action="	.	$action	.	"&pageNum="	.	$pageNum	.	"&id="	.	$id);
//						adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
//						adn_myRedirection('result.php');
//			}
			
			adn_myRedirection('result.php');
			
}

/*	* ******************************************************** */
/*       Titre                */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L5']['nom'];

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
						<script type="text/javascript" src="../../librairie/js/jquery/imgAreaSelect/scripts/jquery.imgareaselect.pack.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">
									function preview(img, selection) {

												var scaleX =  $('#preview').width() / selection.width;
												var scaleY = $('#preview').height() / selection.height;

												$('#preview img').css({
															width: Math.round(scaleX * img.width) + 'px',
															height: Math.round(scaleY * img.height) + 'px',
															marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
															marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
												});
												$('#x1').val(selection.x1);
												$('#y1').val(selection.y1);
												$('#w').val(selection.width);
												$('#h').val(selection.height);
									};

									$(document).ready(function () {

												$('#uploaded_image').imgAreaSelect({
															instance: true,
															aspectRatio: '1:1',
															handles: 'corners',
															x1: <?php	echo	$x1_crop	?>,
															y1: <?php	echo	$y1_crop	?>,
															x2: <?php	echo	$x2_crop	?>,
															y2: <?php	echo	$y2_crop	?>,
															show: true,
															onInit: preview,
															onSelectChange: preview
												});

									});

						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
						<link href="../../librairie/js/jquery/imgAreaSelect/css/imgareaselect-default.css" rel="stylesheet" type="text/css">
						<style type="text/css">
									#uploaded_image {
												float: left;
												margin-right: 10px;
									}
									#preview {
												/*float:left;*/
												/*position:relative;*/
												overflow:hidden;
												width: <?php	echo	$dimensionCrop[0];	?>px;
												height: <?php	echo	$dimensionCrop[1];	?>px;
									}
						</style>
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<div class="BoxSearch" style="width: inherit">
												<div class="BoxCrop">
															<img id="uploaded_image" src="<?php	echo	$resizedFile	?>" width="<?php	echo	$newLargeur	?>" height="<?php	echo	$newHauteur	?>" alt=""/>
															<div id="preview">
																		<img src="<?php	echo	$resizedFile;	?>" style="position: relative;" alt="Preview" />
															</div>
															<p><a href="upload_img.php"><img src="../_media/bo_nofiltre.png" width="16" height="16" border="0" align="absmiddle" alt="Modifier"/> Changer l'image </a></p>
															<div style="clear: both"></div>
												</div>
												<div class="form_submit" style="margin: 0">
															<form action="" method="post">
																		<input type="hidden" name="x1" value="" id="x1" />
																		<input type="hidden" name="y1" value="" id="y1" />
																		<input type="hidden" name="w" value="" id="w" />
																		<input type="hidden" name="h" value="" id="h" />
																		<input type="hidden" name="resizedFile" value="<?php	echo	$resizedFile;	?>" />
																		<input type="hidden" name="img" value="<?php	echo	$img;	?>" />
<!-- <input type="hidden" name="action" value="<?php	echo	$action;	?>" />-->
<!-- <input type="hidden" name="pagenum" value="<?php	echo	$pageNum;	?>" />-->
<!--																		<input type="hidden" name="id" value="<?php	echo	$id;	?>" />-->
																		<input type="submit" name="bt_crop" value="Valider le cadrage" id="bt_crop" class="submit"/>
															</form>
												</div>
									</div>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
