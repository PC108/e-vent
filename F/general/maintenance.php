<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
$title	=	_("Site en maintenance");

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
				<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
		</head>
		<body>
				<div id="global">
						<?php	include('../_header.php');	?>
						<div id="sidebar" ></div>
						<div id="content" class="corner20-all">
								<h1><?php	echo	_("Site en maintenance");	?></h1>
								<p><?php	echo	_("Le site est en cours de maintenance et n'est donc pas accessible pour le moment. Merci de votre patience !");	?></p>
						</div>
						<?php	include('../_footer.php');	?></div>
		</body>
</html>