<?php

if	(isset($_SESSION['message_user']))	{
			switch	($_SESSION['message_user'])	{
						case	"bug":
									$message	=	"Une erreur s'est produite dans l'éxécution de cette page. <br/>";
									$message	.=	'Merci de contacter Atelier du .net (http://www.atelierdu.net) en lui transmettant les informations suivantes : <br/>';
									$message	.=	"- l'adresse url de cette page <br/>";
									$message	.=	"- la ligne d'erreur qui s'est inscrite en rouge en bas de la page.";
									break;
						case	"maj_ok":
									$message	=	"L'enregistrement a été modifié avec succès.";
									break;
						case	"maj_ko":
									$message	=	"Problème lors de la mise à jour de l'enregistrement.";
									break;
						case	"add_ok":
									$message	=	"L'enregistrement a été ajouté avec succès.";
									break;
						case	"add_ko":
									$message	=	"Problème lors de l'insertion de l'enregistrement.";
									break;
						case	"del_ok":
									$message	=	"L'enregistrement a été supprimé avec succès.";
									break;
						case	"del_ko":
									$message	=	"Problème lors de la suppression de l'enregistrement.";
									break;
						case	"delConstraints_ko":
									$message	=	"Vous ne pouvez pas supprimer cet enregistrement car il est relié à d'autres données.";
									break;
						case	"com_mod_ok":
									$message	=	"Le commentaire a été modifié avec succès.";
									break;
						case	"com_mod_ko":
									$message	=	"Problème lors de la mise à jour du commentaire.";
									break;
						case	"com_add_ok":
									$message	=	"Le commentaire a été ajouté avec succès.";
									break;
						case	"com_add_ko":
									$message	=	"Problème lors de l'insertion du commentaire.";
									break;
						case	"com_del_ok":
									$message	=	"Le commentaire a été supprimé avec succès.";
									break;
						case	"com_del_ko":
									$message	=	"Problème lors de la suppression du commentaire.";
									break;
						case	"bad_post":
									$message	=	"Certaine(s) information(s) saisies dans le formulaire ne sont pas valides ou manquantes. Votre action n'a pu aboutir. Vous pouvez activer javascript pour bénéficier d'une validation directe lors de la saisie.";
									break;
						case	"bad_get":	// Erreur de remplissage de formulaire (désactivation javascript)
									$message	=	_("Certaine(s) information(s) sont manquantes pour afficher la page demandée. vous avez été redirigé.");
									break;
						case	"pb_user":
									if	(isSet($_POST['user']))	{
												$message	=	"L'utilisateur "	.	$_POST['user']	.	" est inconnu. Accès refusé.";
									}	else	{
												$message	=	"L'utilisateur est inconnu. Accès refusé.";
									}
									break;
						case	"pb_pwd":
									$message	=	"Mot de passe non valide. Accès refusé.";
									break;
						case	"pb_update_db":
									$message	=	"Probléme d'accés la base. Merci de réessayer.";
									break;
						case	"deladh_ok":
									$message	=	"Les inscriptions ont été supprimés avec succès.";
									break;
						case	"deladh_noadh":
									$message	=	"Aucune inscription à supprimer.";
									break;
						case	"img_missing":
									$message	=	"L'image a effacer n'existe pas.";
									break;
						case	"delimg_ok":
									$message	=	"Les images ont été supprimées avec succés.";
									break;
						case	"delimg_noimg":
									$message	=	"Aucune image à supprimer.";
									break;
						case	"delimg_ko":
									$message	=	"Problème lors de la suppression des images.";
									break;
						case	"acces_ko":
									$message	=	"L'identifiant que vous utilisez n'a pas accès à ce groupe de pages.";
									break;
						case	"accesMenu_ko":
									$message	=	"L'identifiant que vous utilisez n'a pas accès au menu. Veuillez entrer un autre identifiant.";
									break;
						case	"login_already_exist":
									$message	=	"Ce login est déjà utilisé. Merci d'en proposer un autre.";
									break;
						case	"del_option_lie":
									$message	=	"Vous tentez de supprimer une option déjà associée à un achat. Opération annulée.";
						case	"cotisation_ko":
									$message	=	"Les fonctions associées aux cotisations sont désactivées.";
						case	"don_ko":
									$message	=	"Les fonctions associées aux dons sont désactivées.";
						case	"benevolat_ko":
									$message	=	"Les fonctions associées aux inscriptions des bénévoles sont désactivées.";
			}
			switch	($_SESSION['message_user'])	{
						case	"maj_ok":
						case	"add_ok":
						case	"del_ok":
						case	"com_mod_ok":
						case	"com_add_ok":
						case	"com_del_ok":
						case	"deladh_ok":
						case	"delimg_ok":
						case	"delimg_noimg":
						case	"deladh_noadh":
									$messageHTML	=	'<div class=" ui-state-highlight ui-corner-all uiphil-msg">';
									$icon	=	'info';
									break;
						case	"bug":
						case	"maj_ko":
						case	"add_ko":
						case	"del_ko":
						case	"delConstraints_ko":
						case	"com_mod_ko":
						case	"com_add_ko":
						case	"com_del_ko":
						case	"bad_post":
						case	"bad_get":
						case	"pb_user":
						case	"pb_pwd":
						case	"pb_update_db":
						case	"no_result":
						case	"img_missing":
						case	"delimg_ko":
						case	"acces_ko":
						case	"accesMenu_ko":
						case	"login_already_exist":
						case	"del_option_lie":
						case	"cotisation_ko":
						case	"don_ko":
						case	"benevolat_ko":
									$messageHTML	=	'<div class="ui-state-error ui-corner-all uiphil-msg">';
									$icon	=	'alert';
									break;
			}
			$messageHTML	.=	'<p><span class="ui-icon ui-icon-'	.	$icon	.	'" style="float: left; margin-right: .3em;"></span>'	.	$message	.	'</p></div>';
			if	(isset($_SESSION['message_debug']))	{
						$messageDebug	=	'<p class="msg_debug"><b>Erreur : </b>'	.	$_SESSION['message_debug']	.	'</p>';
						unset($_SESSION['message_debug']);
			}
}	else	{
			$messageHTML	=	"";
}
unset($_SESSION['message_user']);
echo	$messageHTML;
?>