<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

$title	=	_("Résultat de la recherche");

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../evenement/index.php');
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

// Création du formulaire de recherche
if	((isset($_POST['ADH_nom'])	&&	$_POST['ADH_nom']	!=	""	&&	isset($_POST['ADH_prenom'])	&&	$_POST['ADH_prenom']	!=	"")
								||	(isset($_POST['ADH_email'])	&&	$_POST['ADH_email']	!=	""))	{

		// Ne recherche que les adhérents dont l'état est confirmé et qui ne sont pas déjà lié
		$query	=	"
						SELECT *
						FROM t_adherent_adh AS adherent
						LEFT JOIN t_pays_pays AS pays ON FK_PAYS_id = PAYS_id
						LEFT JOIN t_amis_amis AS amis ON ADH_id = FK_ADHAMI_id
						WHERE adherent.FK_EADH_id >= 3
						AND ADH_id != "	.	$_SESSION['info_adherent']['id_adh']	.	"
						AND (
								amis.FK_ADH_id IS NULL
								OR amis.FK_ADH_id <>"	.	$_SESSION['info_adherent']['id_adh']	.	")";

		if	(isset($_POST['ADH_nom'])	&&	$_POST['ADH_nom']	!=	"")	{
				$nom	=	$_POST['ADH_nom'];
				$query	.=	" AND ADH_nom LIKE '"	.	$nom	.	"'";
		}	else	{
				$nom	=	"";
		}
		if	(isset($_POST['ADH_prenom'])	&&	$_POST['ADH_prenom']	!=	"")	{
				$prenom	=	$_POST['ADH_prenom'];
				$query	.=	" AND ADH_prenom LIKE '"	.	$prenom	.	"'";
		}	else	{
				$prenom	=	"";
		}
		if	(isset($_POST['ADH_email'])	&&	$_POST['ADH_email']	!=	"")	{
				$mail	=	$_POST['ADH_email'];
				$query	.=	" AND ADH_email LIKE '"	.	$mail	.	"'";
		}	else	{
				$mail	=	"";
		}
		$query	.=" GROUP BY ADH_id";
		$query	.=" ORDER BY ADH_nom, ADH_prenom ASC";
		//		echo $query;
		//		exit;
		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
		$nbreRows	=	mysql_num_rows($RS);

		if	($nbreRows	==	0)	{	// on revient sur search.php et on affiche la boite de dialogue
				adn_myRedirection("search.php?nom=$nom&prenom=$prenom&mail=$mail&rep=noresult");
		}
// Si il y aplus d'un résultat, va afficher sur cette page la liste des résultats
}	else	{
		$_SESSION['message_user']	=	"bad_post";
		adn_myRedirection("search.php");
}

/*	* ******************************************************** */
/*              Initialisation des variables                   */
/*	* ******************************************************** */
$str_Description	=	"";

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
				<script type="text/javascript" src="../../librairie/js/code_adn/ns_util.js"></script>
				<script type="text/javascript" src="../../B/_fonction/ns_back.js"></script>
				<script type="text/javascript">
						$(document).ready(function() {
								//Navigation
								$('#navigation #amis').addClass('ui-state-active')
								$('#navigation #amis').button( "option", "disabled", true );

								// Affichage des descriptions
								NS_BACK.initBoxDescriptions(0, 35, false);

								// Validate
								$("#addAdh").validate({
										rules: {
												'arrayAdhLiens[]': {
														required: true
												}
										}
								});

						});
				</script>
				<!-- CSS -->
				<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
				<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
				<link href="../../librairie/js/jquery/ui_css/jquery-ui.css" type="text/css" rel="stylesheet" />
				<link href="../_css/style_front.css" rel="stylesheet" type="text/css" />
				<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
		</head>
		<body>
				<div id="global">
						<?php	include('../_header.php');	?>
						<?php	include('../_sidebar.php');	?>
						<div id="content" class="corner20-all">
								<a href="result.php"><div class="bt_retour corner20-tl"><?php	echo	"< "	.	_("Mes relations");	?></div></a>
								<a href="search.php"><div class="bt_retour corner20-br"><?php	echo	"< "	.	_("Chercher un adhérent");	?></div></a>
								<h1><?php	echo	$title;	?></h1>
								<p><?php
						echo	"<b>"	.	$nbreRows	.	"</b> "	.	_("personne(s) trouvée(s) correspondant à la recherche :");
						if	($nom	!=	"")	{	echo	(" <b>"	.	$prenom	.	" "	.	$nom)	.	"</b>";	}
						if	($mail	!=	"")	{	echo	(" <b>"	.	$mail)	.	"</b>";	}
						?></p>
								<form id="addAdh" action="action.php" method="post" class="formulaire corner20-all">
										<table>
												<?php
												while	($row	=	mysql_fetch_array($RS))	{
														$str_Description	.=	'<div id="info_'	.	$row['ADH_id']	.	'" class="invisible">';
														$str_Description	.=	$row['ADH_adresse1']	.	'<br />';
														if	(!is_null($row['ADH_adresse2']))	{	$str_Description	.=	$row['ADH_adresse2']	.	'<br />';	}
														$str_Description	.=	$row['ADH_zip']	.	" "	.	$row['ADH_ville']	.	'<br />';
														$str_Description	.=	$row['PAYS_nom_'	.	$langue];
														$str_Description	.=	'</i></div>';
														?>
														<tr>
																<td>
																		<input type="checkbox" name="arrayAdhLiens[]" value="<?php	echo	$row['ADH_id'];	?>"></input>
																</td>
																<td>
																		<div class="cellule cell_txt corner10-all">
																				<?php	echo	$row['ADH_prenom']	.	" "	.	$row['ADH_nom'];	?>
																		</div>
																</td>
																<td>
																		<div class="js_description cellule cell_txt pointer corner10-all" id="info_<?php	echo	$row['ADH_id']	?>"> <img src="../_media/GEN/ic_info.png" alt="info"></img></div>
																</td>
														</tr>
												<?php	}	?>
										</table>
										<label for="arrayAdhLiens[]" class="error" style="display:none"><?php	echo	_("Au moins une sélection requise.");	?></label>
										<div class="submit_form">
												<input  id="action" name="action" type="hidden" value="add"/>
												<input type="submit" name="Submit" value="<?php	echo	_("ajouter à mes relations");	?>" class="bt_submit corner10-all"/>
										</div>
								</form>

		    </div>
						<?php	include('../_footer.php');	?>
				</div>
				<div id="box_description" class="popup corner10-all" style="padding-right: 12px;"><?php	echo	$str_Description;	?></div>
		</body>
</html>