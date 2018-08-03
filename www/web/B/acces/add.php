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
/*              Gestion du numéro de page                  */
/*	* ******************************************************** */
// Conservation du numéro de page
if	(isSet($_GET['pageNum']))	{
			$pageNum	=	$_GET['pageNum'];
}	else	{
			$pageNum	=	0;
}

/*	* ******************************************************** */
/*               Définition des variables                  */
/*	* ******************************************************** */
$id	=	$_GET["id"];
if	($id	==	0)	{
// Mode ADD
			$Action	=	"add";
			$Submit	=	"Ajouter";
			$login	=	"";
			$pwd	=	"";
			$grp	=	"";
}	else	{
// Mode MAJ
			$Action	=	"maj";
			$Submit	=	"Modifier";
// création du recordset
			$query	=	"SELECT * FROM t_acces_acs WHERE ACS_id=$id";
			$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
// récupération et affichage des valeurs
			$row	=	mysql_fetch_object($RS);

			$login	=	$row->ACS_login;
			$pwd	=	$row->ACS_pwd;
			$grp	=	$row->ACS_grp;
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['CONFIGURATION']['L1']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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

												$("#form_acces").validate({
															rules: {
																		'ACS_login': {
																					required: true
																		},
																		'ACS_pwd': {
																					required: true
																		}
															}
												});

									});

						</script>
						<!-- CSS -->
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<form id="form_acces" action="action.php" method="post">
												<div class="BoxSearch">
															<h2>Accés</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Login</div>
																		<div class="content_form"><input id="ACS_login" name="ACS_login" type="text" value="<?php	echo	($login);	?>" size="30" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Password</div>
																		<div class="content_form"><input  id="ACS_pwd" name="ACS_pwd" type="text" value="<?php	echo	($pwd);	?>" size="30" class="form_R"></div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Groupe</div>
																		<div class="content_form">
																					<select name="ACS_grp" class="form_R">
																								<option value="admin"<?php	if	(!(strcmp("admin",	$grp)))	{	echo	"SELECTED";	}	?>>Administrateur (tout les droits)</option>
																								<option value="adher"<?php	if	(!(strcmp("adher",	$grp)))	{	echo	"SELECTED";	}	?>>Gestion des adhérents uniquement</option>
																								<option value="cmd"<?php	if	(!(strcmp("cmd",	$grp)))	{	echo	"SELECTED";	}	?>>Gestion des commandes uniquement</option>
																								<option value="event"<?php	if	(!(strcmp("event",	$grp)))	{	echo	"SELECTED";	}	?>>Gestion des événements uniquement</option>
																								<option value="stat"<?php	if	(!(strcmp("stat",	$grp)))	{	echo	"SELECTED";	}	?>>Accès aux statistiques uniquement</option>
																								<option value="site"<?php	if	(!(strcmp("site",	$grp)))	{	echo	"SELECTED";	}	?>>Aucun droit, utilisateur du front</option>
																					</select>
																		</div>
															</div>

															<div class="form_submit">
																		<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
																		<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
																		<input name="ACS_id" type="hidden" id="id" value="<?php	echo	$id;	?>">
																		<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
															</div>
												</div>
									</form>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
