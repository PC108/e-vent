<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'cmd')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L6']['nom'];

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
			<head>
						<title><?php	echo	$titre	?></title>
						<meta NAME="author" CONTENT="www.atelierdu.net" />
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<link rel="icon" type="image/png" href="../_media/favicon.png" />
						<!-- JS -->
						<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {

									});
						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<p>Le mode SAISIE DIRECTE vous permet de créer des commandes sans contraintes de dates. 
												<br />Par ce mode, vous avez donc aussi accès aux événements passés et aux événements se déroulant le jour même.
												<br />Il est utile si vous souhaitez créer des commandes en temps réel lors de l'événement ou après l'événement.</p>
									<form action="../../F/general/login.php" target="blank" method="post" style="margin:30px 0 0 25px">
												<input type="hidden" name="key" value="UYyjiuUIYTuiHUYt67yYYfrEfghGhytuGrt5eeRDfcdedryFHgfijKlkjIUYygyteGcgfc" />
												<input type="hidden" name="chemin" value="../evenement/index.php" />
												<input type="hidden" name="username" value="<?php	echo $_SESSION['user_info'][0];	?>" />
												<input type="submit" name="Submit" value="Lancer le mode SAISIE DIRECTE dans un nouvel onglet" class="submit">
									</form>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
