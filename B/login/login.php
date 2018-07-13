<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office E-venement.com pour "	.	$_SESSION['info_client']['nom'];

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
				<!-- CSS -->
				<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
				<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
				<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
		</head>
		<body>
				<?php	include("../_header.php");	?>
				<div id="contenu">
						<form name="form1" method="post" action="action.php">
								<div class="BoxLogin">
										<h2>Login</h2>
										<div class="form_hr">
												<div class="label_form label">Votre nom d'utilisateur</div>
												<div class="content_form"><input name="user" type="text" size="30" AUTOCOMPLETE=OFF></div>
										</div>
										<div class="form_hr">
												<div class="label_form label">Votre mot de passe</div>
												<div class="content_form"><input name="mdp" type="password" size="30"></div>
										</div>
										<div class="form_submit">
												<input type="submit" name="Submit" value="Valider" class="submit">
										</div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
