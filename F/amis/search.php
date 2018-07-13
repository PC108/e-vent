<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

$title	=	_("Chercher un adhérent");

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
// Check identification
if	(!isset($_SESSION['info_adherent']))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../evenement/index.php');
}


/*	* ******************************************************** */
/*              Fonctions                    */
/*	* ******************************************************** */
// Récupére les valeurs des variables des champs si on a bouclé sur search_result

$nom	=	"";
$prenom	=	"";
$mail	=	"";
$reponse_search	=	"none";

// Récupère la réponse du script < search_action.php > passé en GET pour afficher les boites de dialogue
// Ecrit le résultat dans le div caché id="retour_searchAction" dont le contenu sera vérifier par un script JavaScript


if	(isset($_GET['nom'])	||	isset($_GET['prenom'])	||	isset($_GET['mail']))	{
		$nom	=	$_GET['nom'];
		$prenom	=	$_GET['prenom'];
		$mail	=	$_GET['mail'];
		$reponse_search	=	$_GET['rep'];
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
								$('#navigation #amis').addClass('ui-state-active')
								$('#navigation #amis').button( "option", "disabled", true );

								// Init Dialog
								NS_DIALOG.initAlertDialog($('#alert_searchLinkOk'), '<?php	echo	_("ok");	?>');
								NS_DIALOG.initConfirmDialog($('#dialog_searchNoResult'), ['<?php	echo	_("créer un nouvel adhérent");	?>','<?php	echo	_("nouvelle recherche");	?>']);
								switch ($('#retour_searchAction').text()) {
										case 'noresult':
												NS_DIALOG.confirmChemin = 'amis_add.php?nom=<?php	echo	$nom;	?>&prenom=<?php	echo $prenom;	?>&email=<?php	echo	$mail;	?>';
												NS_DIALOG.openDialog($('#dialog_searchNoResult'));
												break;
										case 'linkok':
												NS_DIALOG.openDialog($('#alert_searchLinkOk'));
												break;
								};

								// Validate : on doit renseigner soit le nom ET le prénom, soit l'email au minimum. On peut renseigner les trois champs. Au lancement de la
								// page, tous les champs sont requis. Si on rempli le mail, les deux autres deviennent non requis. Et vice versa.
								$("#recherche").validate({
										rules: {
												'ADH_nom':{
														required: function(){
																if(($("#ADH_prenom").val() == "" && $("#ADH_email").val() == "") || $("#ADH_prenom").val() != "")
																		return true;
																else return false;
														}
												},
												'ADH_prenom':{
														required: function(){
																if(($("#ADH_nom").val() == "" && $("#ADH_email").val() == "") || $("#ADH_nom").val() != "")
																		return true;
																else return false;
														}
												},
												'ADH_email':{
														email: true,
														required: function(){
																if($("#ADH_prenom").val() == "" && $("#ADH_nom").val() == "")
																		return true;
																else return false;
														}
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
				<link type="text/css" href="../_css/cmd_front.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
				<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
		</head>
		<body>
				<div id="global">
						<?php	include('../_header.php');	?>
						<?php	include('../_sidebar.php');	?>
						<div id="content" class="corner20-all">
								<a href="result.php"><div class="bt_retour corner20-tl corner20-br"><?php	echo	"< "	.	_("Mes relations");	?></div></a>
								<h1><?php	echo	$title;	?></h1>
								<div class="info_print corner10-all">
										<div><img src="../_media/GEN/ic_info.png" align="absmiddle" alt="info"/> <span class="commentaire">
														<?php	echo	_("Pour créer une relation, veuillez  tout d'abord vérifier si cette personne est déjà inscrite sur la plateforme.")	.	" ";	?>
														<?php	echo	_("Si ce n'est pas le cas, demandez-lui de s'inscrire ou")	.	" "	.	'<a href="amis_add.php">'	.	_("créez un nouvel adhérent.")	.	"</a>";	?>
												</span></div>
								</div>
								<form id="recherche" class="formulaire corner20-all" action="search_result.php" method="post">
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("prénom");	?></div>
												<div class="content_form">
														<input id="ADH_prenom" name="ADH_prenom" type="text" value="<?php	echo	$prenom;	?>" size="30" class="required"/>
												</div>
										</div>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("et nom");	?></div>
												<div class="content_form">
														<input id="ADH_nom" name="ADH_nom" type="text" value="<?php	echo	$nom;	?>" size="30" class="required"/>
												</div>
										</div>
										<p><b><?php	echo	_("OU");	?></b></p>
										<div class="hr_form">
												<div class="label_form required"><?php	echo	_("email");	?></div>
												<div class="content_form">
														<input id="ADH_email" name="ADH_email" type="text" value="<?php	echo	$mail;	?>" size="30" class="required"/>
												</div>
										</div>
										<div class="submit_form">
												<input id="bt_searchAdh" type="submit" name="Submit" value="<?php	echo	_("rechercher");	?>" class="bt_submit corner10-all"/>
										</div>
								</form>
								<div id="retour_searchAction" style="display: none;"><?php	echo	$reponse_search	?></div>
								<div id="dialog_searchNoResult" title="<?php	echo	_("Résultat de la recherche");	?>">
										<?php	echo	_("Aucune nouvelle personne correspondant à votre critère de recherche n'a été trouvée sur la plateforme.");	?><br/>
										<?php	echo	_("Vous pouvez l'inscrire en créant un nouvel adhérent ou modifier vos critères de recherche.");	?>
								</div>
								<div id="alert_searchLinkOk" title="<?php	echo	_("Résultat de la recherche");	?>">
										<?php	echo	_("Cette personne a été trouvée et a été ajoutée à votre liste de relations.");	?><br/>
								</div>
						</div>
						<?php	include('../_footer.php');	?>
				</div>
		</body>
</html>