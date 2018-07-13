<?php
/*	* ******************************************************** */
/*              PARAMETRES DE L'UPLOAD                     */
/*	* ******************************************************** */
$extensionsOk	=	array('pdf');	// Fichier accepté à l'upload. A ne pas modifier
$tailleMax	=	2000000;	// Taille maximum de l'image.
$repertoireDest	=	"../../upload/doc/";
$prefixImgResized	=	"doc_";
$nomUnique	=	TRUE;	//Si TRUE, ajoute un identifiant unique pour le nom du fichier et créé à chaque nouveau fichier, sinon, utilise l'id passé en GET et réécrase l'ancien fichier.

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Définition des variables                   */
/*	* ******************************************************** */
$pageNum	=	$_GET['pageNum'];
$ParamEnGET	=	"?pageNum="	.	$pageNum;

/*	* ******************************************************** */
/*                  UPLOAD DE L'IMAGE                      */
/*	* ******************************************************** */
if	(isset($_FILES['userfile']))	{

			$CheckUpload	=	"Ok";

			// vérifications upload
			$ExtensionFile	=	substr(strrchr($_FILES['userfile']['name'],	'.'),	1);
			$ExtensionFile	=	strtolower($ExtensionFile);
			if	(!in_array($ExtensionFile,	$extensionsOk))	{
						$CheckUpload	=	"Le type de fichier ne correspond pas. Veuillez sélectionner un fichier de type pdf.";
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
			$CheckUpload	=	"Aucun document n'a été uploadé.";
}

//Create the upload directory with the right permissions if it doesn't exist
if	($CheckUpload	==	"Ok"	&&	!is_dir($repertoireDest))	{
			if	(!mkdir($repertoireDest,	0777))	{	$CheckUpload	=	"Le répertoire de destination n'a pu être créé.";	}
			if	(!chmod($repertoireDest,	0777))	{	$CheckUpload	=	"Le répertoire de destination n'a pu être créé.";	}
}

if	($CheckUpload	==	"Ok")	{
			$nomFile	=	uniqid()	.	"."	.	$ExtensionFile;
			// copie du fichier vers le répertoire de destination + gestion erreur
			if	(!move_uploaded_file($_FILES['userfile']['tmp_name'],	$repertoireDest	.	$nomFile))	{	$CheckUpload	=	"Le fichier n'a pu être déplacé vers son répertoire de destination.";	}
}

/*	* ******************************************************** */
/*       Mise à jour de la base de donnée                */
/*	* ******************************************************** */
if	($CheckUpload	==	"Ok")	{
			$nom	=	$_POST['DOCEVEN_nom'];
			$langue	=	$_POST['DOCEVEN_langue'];
			$majPar	=	$_SESSION['user_info'][2];
			$majLe	=	date('Y-m-d H:i:s');

			$query	=	"INSERT INTO t_doceven_doceven (DOCEVEN_file, DOCEVEN_nom, DOCEVEN_langue, DOCEVEN_type) VALUES ('$nomFile', '$nom', '$langue', '$ExtensionFile')";
			$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));

			// Mise à jour de la table log
			if	($check)	{
						$newId	=	mysql_insert_id();

						$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'DOCEVEN', '$newId', 'add')";
						adn_mysql_query($query2,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
			}

			adn_myRedirection('result.php' .	$ParamEnGET);
}

/*	* ******************************************************** */
/*       Titre                */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L6']['nom'];

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
						<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												$("#form_docevent").validate({
															rules: {
																		'DOCEVEN_nom': {
																					required: true
																		},
																		'DOCEVEN_langue': {
																					required: true
																		},
																		'userfile': {
																					required: true
																		}
															}
												});

									});

						</script>
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
												<form id="form_docevent" enctype="multipart/form-data" action="<?php	echo	$ParamEnGET;	?>" method="post">
															<input type="hidden" name="MAX_FILE_SIZE" value="<?php	echo	$tailleMax;	?>" />
															<div class="BoxSearch">
																		<h2>Document associé</h2>
																		<div class="form_hr">
																					<div class="label_form label_R">Nom</div>
																					<div class="content_form"><input id="DOCEVEN_nom" name="DOCEVEN_nom" type="text" value="" size="40" class="form_R"></div>
																		</div>
																		<div class="form_hr">
																					<div class="label_form label_R">Langue du doc</div>
																					<div class="content_form">
																								<select  id="DOCEVEN_langue" name="DOCEVEN_langue" class="form_R">
																											<option value="">choisir une langue</option>
																											<option value="FR">en français (FR)</option>
																											<option value="EN">en anglais (EN)</option>
																								</select>
																					</div>
																		</div>
																		<div class="form_hr">
																					<div class="label_form label_R">Document à uploader sur le serveur</div>
																					<div class="content_form">
																								<input name="userfile" size="30" type="file">
																								<div class="note">Veuillez choisir sur votre disque un fichier en pdf de moins de 1,8 Mo.</div>
																					</div>
																		</div>
																		<div class="form_submit">
																					<input name="Uploader" value="Uploader" type="submit" class="submit">
																		</div>
															</div>
												</form>
									<?php	}	else	{	?>
												<div class="ui-state-error ui-corner-all uiphil-msg">
															<p><?php	echo	$CheckUpload	?></p>
												</div>
												<p><a href="<?php	echo	$ParamEnGET;	?>">Essayer à nouveau</a></p>
									<?php	}	?>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
