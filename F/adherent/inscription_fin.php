<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
$title	=	_("Fin de l'étape 1 de l'inscription");
if	(!isset ($_SESSION['message_user']))	{
		$_SESSION['message_user'] = "insEtape1_reload";
}

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php	echo	$langue;	?>" xml:lang="<?php	echo	$langue;	?>">
		<head>
				<title>e-venement.com | <?php	echo	$title;	?></title>
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
				<meta http-equiv="Content-Language" content="<?php	echo	$langue;	?>" />
				<link rel="icon" type="image/png" href="../_media/GEN/favicon.png" />
				<!-- JS -->
				<?php	include('../_shared_js.php');	?>
				<!-- CSS -->
				<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
				<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
				<link href="../_css/style_front.css" rel="stylesheet" type="text/css" />
				<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
		</head>
		<body>
				<div id="global">
						<div><?php	include('../_messages.php');	?></div>
						<p class="centrer"><a href="../evenement/index.php"><?php	echo	_("retour au site");	?></a></p>
				</div>
		</body>
</html>