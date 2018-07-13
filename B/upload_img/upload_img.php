<?php
/*	* ******************************************************** */
/*              PARAMETRES DE L'UPLOAD                     */
/*	* ******************************************************** */
$extensionsOk	=	array('png',	'gif',	'jpg',	'jpeg');	// Fichier accepté à l'upload. A ne pas modifier
$tailleMax	=	2000000;	// Taille maximum de l'image.
$repertoireDest	=	"../../upload/img/";
$prefixImgResized	=	"resized_";
$maxSize	=	700;	//Largeur ou hauteur maximum de l'image
$minSize	=	0;	//Largeur ou hauteur minimum de l'image. Attention, modifie le ratio de l'image. Mettre à 0 pour désactiver.
$nomUnique	=	TRUE;	//Si TRUE, ajoute un identifiant unique pour le nom du fichier et créé à chaque un nouveau fichier, sinon, utilise l'id passé en GET et réécrase l'ancien fichier.
$keepOriginal	=	FALSE;	// Si TRUE, stocke l'image originale dans $repertoireDest avec le $nomFichier

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
/*                  UPLOAD DE L'IMAGE                      */
/*	* ******************************************************** */
if	(isset($_FILES['userfile']))	{

		$CheckUpload	=	"Ok";

		// vérifications upload
		$ExtensionFile	=	substr(strrchr($_FILES['userfile']['name'],	'.'),	1);
		$ExtensionFile	=	strtolower($ExtensionFile);
		if	(!in_array($ExtensionFile,	$extensionsOk))	{
				$CheckUpload	=	"Le type de fichier ne correspond pas. Veuillez sélectionner un fichier de type png, gif ou jpg.";
		}
		// Gestion des erreurs
		$Erreur	=	$_FILES['userfile']['error'];
		if	($Erreur	!=	0)	{
				switch	($Erreur)	{
						case	1	:	$CheckUpload	=	"La taille du fichier dépasse la taille limite autorisée par le serveur de 1,9 Mo.";	break;
						case	2	:	$CheckUpload	=	"La taille du fichier dépasse la taille limite autorisée par le formulaire de 1,9 Mo.";	break;
						case	3	:	$CheckUpload	=	"Le fichier n'a été que partiellement téléchargé.";	break;
						case	4	:	$CheckUpload	=	"Aucun fichier n'a été téléchargé";	break;
						case	6	:	$CheckUpload	=	"Un dossier temporaire est manquant.";	break;
						case	7	:	$CheckUpload	=	"Échec de l'écriture du fichier sur le disque.";	break;
						case	8	:	$CheckUpload	=	"L'envoi de fichier est arrêté par l'extension.";	break;
				}
		}
}	else	{
		$CheckUpload	=	"Aucune image n'a été uploadée.";
}

//Create the upload directory with the right permissions if it doesn't exist
if	($CheckUpload	==	"Ok"	&&	!is_dir($repertoireDest))	{
		if	(!mkdir($repertoireDest,	0777))	{	$CheckUpload	=	"Le répertoire de destination n'a pu être créé.";	}
		if	(!chmod($repertoireDest,	0777))	{	$CheckUpload	=	"Le répertoire de destination n'a pu être créé.";	}
}

if	($CheckUpload	==	"Ok")	{
		// Crée le nom générique de l'image et le stocke en session
		$nomFichier	=	$id;
		if	($nomUnique)	{	$nomFichier	.=	uniqid();	}

		// stocke l'image originale dans $repertoireDest si $keepOriginal = TRUE
		if	($keepOriginal)	{
				$destFile	=	$repertoireDest	.	$nomFichier	.	"."	.	$ExtensionFile;
				// copie du fichier vers le répertoire de destination + gestion erreur
				if	(!move_uploaded_file($_FILES['userfile']['tmp_name'],	$destFile))	{	$CheckUpload	=	"Le fichier n'a pu être déplacé vers son répertoire de destination.";	}
		}	else	{
				$destFile	=	$_FILES['userfile']['tmp_name'];
		}
}

/*	* ******************************************************** */
/*         CREATION DE L'IMAGE REDIMENSIONNEE             */
/*	* ******************************************************** */
if	($CheckUpload	==	"Ok")	{
		// Redimensionnement de l'image
		$tailleImg	=	getimagesize($destFile);
		$largeur	=	$tailleImg[0];
		$hauteur	=	$tailleImg[1];
		$Ratio	=	round($largeur	/	$hauteur,	3);

		// Recalcul des dimensions
		if	($largeur	>=	$hauteur)	{
				$newLargeur	=	$maxSize;
				$newHauteur	=	round($newLargeur	/	$Ratio);
				if	($minSize	!=	0	&&	$newHauteur	<	$minSize)	{	$newHauteur	=	$minSize;	}
		}	else	{
				$newHauteur	=	$maxSize;
				$newLargeur	=	round($newHauteur	*	$Ratio);
				if	($minSize	!=	0	&&	$newLargeur	<	$minSize)	{	$newLargeur	=	$minSize;	}
		}

		// Creation de l'image
		switch	($ExtensionFile)	{
				case	"jpg"	:
				case	"jpeg"	:
						$imgSource	=	imagecreatefromjpeg($destFile);
						break;
				case	"png"	:
						$imgSource	=	imagecreatefrompng($destFile);
						break;
				case	"gif"	:
						$imgSource	=	imagecreatefromgif($destFile);
						break;
		}
		// Creation de l'image redimensionnée
		$newImage	=	imagecreatetruecolor($newLargeur,	$newHauteur);
		if	(!imagecopyresampled($newImage,	$imgSource,	0,	0,	0,	0,	$newLargeur,	$newHauteur,	$largeur,	$hauteur))	{
				$CheckUpload	=	"Un problème est survenu lors du redimensionnement de l'image source.";
		}
}

if	($CheckUpload	==	"Ok")	{
		// Enregistrement de l'image en JPEG
		$resizedFile	=	$repertoireDest	.	$prefixImgResized	.	$nomFichier	.	".jpg";
		if	(!imagejpeg($newImage,	$resizedFile,	100))	{
				$CheckUpload	=	"Un problème est survenu lors de l'enregistrement de l'image redimensionnée.";
		}
}

/*	* ******************************************************** */
/*       Redirection                */
/*	* ******************************************************** */
if	($CheckUpload	==	"Ok")	{
		adn_myRedirection('crop.php?img='	.	$nomFichier);
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
				<script type="text/javascript" src="../_shared.js"></script>
				<!-- CSS -->
				<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
				<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
				<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
				<link href="../../librairie/js/jquery/imgAreaSelect/css/imgareaselect-default.css" rel="stylesheet" type="text/css">
		</head>
		<body>
				<?php	include("../_header.php");	?>
				<div id="contenu">
						<?php	if	(!isset($_FILES['userfile']))	{	?>
								<?php	if	($CheckUpload	==	"Ok")	{	?>
										<div class="ui-state-highlight ui-corner-all uiphil-msg">
												<p>L'image a été importée avec succès.</p>
										</div>
								<?php	}	?>
								<!-- MAX_FILE_SIZE doit prcder le champs input de type file -->
								<!-- Le nom de l'lment input dtermine le nom dans le tableau $_FILES -->
								<form enctype="multipart/form-data" action="" method="post">
										<input type="hidden" name="MAX_FILE_SIZE" value="<?php	echo	$tailleMax;	?>" />
										<div class="BoxSearch">
												<h2>Image de l'événement</h2>
												<div class="form_hr">
														<div class="label_form label_R">Image à télécharger</div>
														<div class="content_form">
																<input name="userfile" size="30" type="file">
																<div class="note">Veuillez choisir sur votre disque une image en png, gif, jpg ou jpeg de moins de 1,8 Mo.</div>
														</div>
												</div>
												<div class="form_submit">
														<input name="upload" value="Upload" type="submit" class="submit">
												</div>
										</div>
								</form>
						<?php	}	else	{	?>
								<div class="ui-state-error ui-corner-all uiphil-msg">
										<p><?php	echo	$CheckUpload	?></p>
								</div>
								<p><a href="">Essayer à nouveau</a></p>
						<?php	}	?>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
