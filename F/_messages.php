<?php

if	(isset($_SESSION['message_user']))	{
			switch	($_SESSION['message_user'])	{
						case	"acces_ko":
									$message	=	_("Vous avez été redirigé sur la page d'accueil car la page demandée n'est accessible qu'après vous être identifié.");
									break;
						case	"maj_ok":
									$message	=	_("L'enregistrement a été modifié avec succès.");
									break;
						case	"maj_ko":
									$message	=	_("Problème lors de la mise à jour de l'enregistrement.");
									break;
						case	"add_ok":
									$message	=	_("L'enregistrement a été ajouté avec succès.");
									break;
						case	"add_ko":
									$message	=	_("Problème lors de l'insertion de l'enregistrement.");
									break;
						case	"del_ok":
									$message	=	_("L'enregistrement a été supprimé avec succès.");
									break;
						case	"del_ko":
									$message	=	_("Problème lors de la suppression de l'enregistrement.");
									break;
						case	"bad_post":	// Erreur de remplissage de formulaire (désactivation javascript)
									$message	=	_("Certaine(s) information(s) saisies dans le formulaire ne sont pas valides ou manquantes. Vous pouvez activer javascript pour bénéficier d'une validation directe lors de la saisie.");
									break;
						case	"bad_get":	// Erreur de remplissage de formulaire (désactivation javascript)
									$message	=	_("Certaine(s) information(s) sont manquantes pour afficher la page demandée. vous avez été redirigé.");
									break;
						case	"checkUser_ko":	// User inconnu
									$message	=	_("L'utilisateur est inconnu. Accès refusé.");
									break;
						case	"checkPwd_ko":	// Mot de passe pas valide
									$message	=	_("Mot de passe non valide. Accès refusé.");
									break;
						case	"checkIns_ko":	// Inscription pas encore confirmée
									$message	=	_("Vous n'avez pas encore confirmé votre inscription par email. Veuillez vérifier dans votre boite de réception.");
									break;
						case	"sendId_ok":	// Envoi des identifiants/mot de passe à l'adresse donnée.
									$message	=	_("Vos identifiants et mots de passe ont été correctement envoyés à l'adresse email fournie.");
									break;
						case	"sendEmail_ko":	// Echec de l'envoi d'un mail avec un contenu générique
									$message	=	_("L'envoi du mail a échoué.");
									break;
						case	"sendIdByEmail_ko":	// Echec de l'envoi du mail contenant le nouvel identifiant.
									$message	=	_("L'envoi du mail contenant les nouveaux identifiants a échoué. Veuillez contacter l'administrateur du site si le problème persiste.");
									break;
						case	"checkId_ko":	// Cherche à envoyer les informations d'identifiants perdus à une adresse qui n'existe pas
									$message	=	_("Aucun identifiant n'a été trouvé correspondant à cette adresse email.");
									break;
						case	"addNews_ko":	// Cherche à inscrire à la newsletter une email déjà inscrite
									$message	=	_("Cette adresse email est déjà inscrite à la lettre d'information.");
									break;
						case	"addNews_ok":	// Inscription à la newsletter ok.
									$message	=	_("Cette adresse email est maintenant inscrite à la lettre d'information.");
									break;
						case	"delNews_ko":	// Cherche à désinscrire une email qui n'existe pas dans la table newsletter
									$message	=	_("Cette adresse email n'est pas inscrite à la lettre d'information.");
									break;
						case	"delNews_ok":	// Désinscription à la newsletter ok
									$message	=	_("Cette adresse email a été désinscrite de la lettre d'information.");
									break;
						case	"insEtape1_ok":	// Incription d'un adhérent au site.
									$message	=	_("Votre inscription a bien été prise en compte. Nous vous en remercions. Un email va vous être envoyé avec un lien qui vous permettra de finaliser votre inscription. Vous pouvez maintenant quitter cette page.");
									break;
						case	"insEtape1_reload":	// Actualisation de la page de fin de l'étape 1 de l'inscription.
									$message	=	_("Vous pouvez maintenant quitter cette page.");
									break;
						case	"insEtape1_ko":	// Tentative d'inscription d'un adhérent avec nom, prénom et email déjà exisatnt dans base de données
									$message	=	_("Un utilisateur avec ce nom, prénom et email s'est déjà inscrit sur le site. Un email avec les accés au compte va être envoyé à cette adresse.");
									break;
						case	"insEtape2_wait":	// Premier accés réussi sur le formulaire d'informations personnelles via le lien du mail
									$message	=	_("Votre email a été confirmé. Veuillez renseigner les champs obligatoires pour finaliser votre inscription sur le site.");
									break;
						case	"insEtape2_bad":	// Premier accés raté sur le formulaire d'informations personnelles via le lien du mail
									$message	=	_("Votre inscription est introuvable. Veuillez vous inscrire à nouveau.");
									break;
						case	"insEtape2_ok":	// Finalisation de l'inscription par l'adhérent en remplissant le formulaire d'informations personnelles
									$message	=	_("Merci pour votre inscription. Vous pouvez maintenant utiliser le site sans restrictions.");
									break;
						case	"sendContact_ok":	// Envoi du mail de contact
									$message	=	_("Votre email a été correctement envoyé.");
									break;
						case	"no_cmd":	// Aucune commande n'a été trouvée pour cet adhérent
									$message	=	_("Il n'existe aucune commande en cours. Ajouter un élément à votre commande pour l'initialiser.");
									break;
						case	"no_achat":	// Envoi du mail de contact
									$message	=	_("Actuellement, cette commande ne contient aucun achat.");
									break;
						case	"paypal_e1_ko":// Connexion à paypal échoué ou Curl non activé
									$message	=	_("Les informations pour procéder à la transaction sont incomplètes. Veuillez contacter l'administrateur du site si le problème persiste.");
									break;
						case	"paypal_ko":// Connexion à paypal échoué ou Curl non activé
									$message	=	_("Impossible de se connecter au site de paypal, rééssayez ultérieurement.");
									break;
						case	"paypal_retour_ko":// Connexion à paypal échoué ou Curl non activé
									$message	=	_("Les informations sur la transaction PAYPAL sont incomplètes. Merci de vérifier directement dans votre compte PAYPAL que la transaction s'est bien effectuée.");
									break;
						case	"addGrp_ok":	// Création d'un nouveau groupe d'adhérents
									$message	=	_("Le groupe a bien été créé. Vous pouvez maintenant rechercher et ajouter des adhérents à ce groupe.");
									break;
						case	"quitGrp_ok":	// Lorsqu'on quitte un groupe dont on n'est pas le créateur
									$message	=	_("Vous avez été retiré du groupe.");
									break;
						case	"insGrp_ko":	// Lorsqu'on essaye d'ajouter un adhérent qui existe déjà ou que l'on n'a pas modifié le formulaire
									$message	=	_("Un adhérent avec ce nom, prénom et email existe déjà.");
									break;
						case	"insAdhGrp_ok":	// Création et inscription de l'adhérent au groupe
									$message	=	_("L'inscription a réussi. Un mail a été envoyé à l'adresse fournie avec les identifiants de connexion. La personne est maintenant en relation avec vous.");
									break;
						case	"newCmd_ok":	// Création de la commande à la première identification réussie
									$message	=	_("Une commande a été initialisée pour votre compte. Pour en savoir plus, cliquez sur le bouton \"Mes commandes\"");
									break;
						case	"newCmd_ko":	// Création de la commande échouée à la première identification
									$message	=	_("Problème lors de l'initialisation de la commande. Veuillez contacter l'administrateur du site si le problème persiste.");
									break;
						case	"lieu_ko":	// Affichage d'un lieu associé à un événement
									$message	=	_("Aucune information n'est disponible concernant ce lieu.");
									break;
			}
			switch	($_SESSION['message_user'])	{
						case	"acces_ko":
						case	"maj_ok":
						case	"add_ok":
						case	"del_ok":
						case	"sendId_ok":
						case	"addNews_ok":
						case	"delNews_ok":
						case	"insEtape1_ok":
						case	"insEtape1_reload":
						case	"insEtape1_ko":
						case	"insEtape2_wait":
						case	"sendContact_ok":
						case	"insEtape2_ok":
						case	"addGrp_ok":
						case	"quitGrp_ok":
						case	"insAdhGrp_ok":
						case	"newCmd_ok":
						case	"no_cmd":
						case	"no_achat":
									$messageHTML	=	'<div class=" ui-state-highlight ui-corner-all uiphil-msg"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'	.	$message	.	'</p></div>';
									break;
						case	"maj_ko":
						case	"add_ko":
						case	"del_ko":
						case	"bad_post":
						case	"bad_get":
						case	"checkUser_ko":
						case	"checkPwd_ko":
						case	"checkIns_ko":
						case	"sendEmail_ko";
						case	"sendIdByEmail_ko":
						case	"checkId_ko":
						case	"addNews_ko":
						case	"delNews_ko":
						case	"insEtape2_bad":
						case	"insGrp_ko":
						case	"newCmd_ko":
						case	"paypal_e1_ko":
						case	"paypal_retour_ko":
						case	"paypal_ko":
						case	"lieu_ko":
									$messageHTML	=	'<div class="ui-state-error ui-corner-all uiphil-msg"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'	.	$message	.	'</p></div>';
									break;
			}
}	else	{
			$messageHTML	=	"";
}
unset($_SESSION['message_user']);
echo	$messageHTML;
?>