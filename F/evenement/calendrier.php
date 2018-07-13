<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
$title	=	_("Calendrier");

/*	* ******************************************************** */
/*              Gestion du numéro de page                  */
/*	* ******************************************************** */
// gestion du retour sur la page événement
if	(isset($_SESSION['info_adherent']))	{
			$retourPageEvent	=	'evenement.php';
}	else	{
			$retourPageEvent	=	'index.php';
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
						<script type="text/javascript">
									$(document).ready(function() {
												//Navigation
												$('#navigation #evenement').addClass('ui-state-active')
												$('#navigation #evenement').button( "option", "disabled", true );

									});
						</script>
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
									<?php	include('../_sidebar.php');	?>
									<div id="content" class="corner20-all">
												<h1><a href="<?php	echo	$retourPageEvent	?>"><?php	echo	_("Liste des événements");	?></a> | <?php	echo	_("Calendrier");	?></h1>
									</div>
									<?php	include('../_footer.php');	?>
						</div>
			</body>
</html>