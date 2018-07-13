<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CheckEmailPHP.php');
require_once('../../librairie/php/code_adn/EnvoiMail.php');
require_once('../../librairie/php/yaml/sfYaml.php');
require_once('../../librairie/php/yaml/sfYamlParser.php');

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$idClient	=	$_SESSION['info_client']['suffixe'];
$mailEnvoi	=	$_SESSION['info_client']['email_from'];

if	(isset($_GET['id']))	{	// ETAPE 2 : L'adhérent vient de confirmer son inscription via le lien
		$code	=	$_GET['id'];
		$query1	=	"
		SELECT *,
		(SELECT TYTAR_ratio FROM t_typetarif_tytar WHERE TYTAR_id = FK_TYTAR_id) AS TYTAR_ratio
		FROM t_adherent_adh WHERE ADH_lien='$code'";
		$result1	=	mysql_query($query1,	$connexion)	or	die(mysql_error());
		$nbreResult	=	mysql_num_rows($result1);
		if	($nbreResult	==	1)	{	// on a trouvé l'inscription
				$row	=	mysql_fetch_object($result1);
				// Crée la session de l'adhérent et du bénéficiaire et initialise l'identification automatique
				initSessionAdh($row);
				initSessionBenef($row);
				// Vérifie l'état de l'adhérent au moment ou il clique sur le lien
				switch	($row->FK_EADH_id)	{
						case	1:	// état "demande"
								// Modifie l'état de l'adhérent en 2
								$idAdh	=	$row->ADH_id;
								$query2	=	"UPDATE t_adherent_adh SET FK_EADH_id=2 WHERE ADH_id=$idAdh";
								adn_mysql_query($query2,	$connexion,	array('insEtape2_wait',	'add_ko'),	array('message_user',	'message_debug'));
								adn_myRedirection("add.php");
								break;
						case	2:	// état "email_ok"
								$_SESSION['message_user']	=	'insEtape2_wait';
								adn_myRedirection("add.php");
								break;
						case	3:	// état "complet"
								// Comme l'identification n'est pas encore faite, renvois sur la page d'accueil
								$_SESSION['message_user']	=	'insEtape2_ok';
								adn_myRedirection("../evenement/evenement.php");
								break;
				}
		}	else	{	// on n'a pas trouvé l'inscription
				$_SESSION['message_user']	=	'insEtape2_bad';
				adn_myRedirection("inscription.php");
		}
}	else	{	// ETAPE 1 : Crée l'adhérent
		if	(!isset($_POST["ADH_genre"])	||	($_POST["ADH_nom"]	==	"")	||	($_POST["ADH_genre"]	==	"")	||	($_POST["ADH_prenom"]	==	"")	||	($_POST["ADH_email"]	==	"")
										||	($_POST["ADH_langue"]	==	"")	||	(!adn_checkEmailPHP($_POST['ADH_email']))	||	(!($_POST["ADH_password"]	==	""))	&&	((strlen($_POST["ADH_password"])	<	4)
										||	(strlen($_POST["ADH_password"])	>	16))	||	($_POST["ADH_password"]	==	"")	||	($_POST["ADH_password_confirm"]	==	"")	||	($_POST["ADH_password"]	!=	$_POST["ADH_password_confirm"]))	{
				$_SESSION['message_user']	=	'bad_post';
				adn_myRedirection("inscription.php");
		}	else	{
				if	(strtoupper(substr(PHP_OS,	0,	3))	===	'WIN')	{
						$str_retour	=	"\r\n";
				}	else	{
						$str_retour	=	"\n";
				}

				// On check si on a déjà une inscription avec même nom, prénom et mail.
				$query0	=	"
				SELECT FK_EADH_id, ADH_email, ADH_nom, ADH_prenom, ADH_identifiant, ADH_password 
				FROM t_adherent_adh 
				WHERE ADH_email='"	.	mb_strtolower($_POST['ADH_email'],	'UTF-8')	.
												"' AND ADH_nom='"	.	mb_strtoupper($_POST['ADH_nom'],	'UTF-8')	.
												"' AND ADH_prenom='"	.	ucfirst(mb_strtolower($_POST['ADH_prenom'],	'UTF-8'))	.
												"' AND FK_EADH_id > 1";
				$result0	=	mysql_query($query0,	$connexion)	or	die(mysql_error());

				if	(mysql_num_rows($result0))	{	// Si oui, on envoi un mail avec login, mot de passe pour l'adresse mail donnée.
						$row	=	mysql_fetch_object($result0);
						$_SESSION['message_user']	=	'insEtape1_ko';
						// récupère l'url du site
						$url	=	"http://"	.	$_SERVER['HTTP_HOST'];

						$titre	=	_("Récupération de vos informations personnelles sur e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];
						$text	=	_("Bonjour ")	.	$str_retour	.	$str_retour;
						$text	.=	_("Un compte existe déjà sur le site contenant le nom, prénom et adresse email que vous avez saisi lors de votre inscription.")	.	$str_retour;
						$text	.=	_("Ce compte contient les informations suivantes :")	.	$str_retour;
						$text	.=	_("Nom")	.	" : "	.	$row->ADH_nom	.	$str_retour;
						$text	.=	_("Prénom")	.	" : "	.	$row->ADH_prenom	.	$str_retour;
						$text	.=	_("Mail")	.	" : "	.	$row->ADH_email	.	$str_retour;
						$text	.=	_("Login")	.	" : "	.	$row->ADH_identifiant	.	$str_retour;
						$text	.=	_("Mot de passe")	.	" : "	.	$row->ADH_password	.	$str_retour	.	$str_retour;
						$text	.=	_("Si ce compte vous appartient, merci d'utiliser ces identifiants pour vous connecter au site.")	.	$str_retour;
						$text	.=	$url	.	$str_retour	.	$str_retour;
						$text	.=	_("Cordialement.")	.	$str_retour;
						$text	.=	_("L'équipe de e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];

						if	(!adn_envoiMail($_POST['ADH_email'],	$titre,	$text,	$mailEnvoi))	{
								$_SESSION['message_user']	=	'sendIdByEmail_ko';
						}
				}	else	{	// Sinon, crée l'inscription et envoi un mail pour l'étape 2
						$formatData['ADH_nom']	=	'UPPER';
						$formatData['ADH_prenom']	=	'LOWERFIRST';
						$formatData['ADH_email']	=	'LOWER';
						$checkArrayVide	=	array();
						// $checkRequired = "ok";

						$code	=	md5(uniqid());
						$autreData['ADH_lien']	=	$code;

						$mail	=	$_POST['ADH_email'];
						$langue	=	$_POST['ADH_langue'];

						$majPar	=	getSiteUserId($langue);
						$majLe	=	date('Y-m-d H:i:s');

						if	(isSet($_POST['MAIL_newsletter']))	{
								$newsletter	=	1;
						}	else	{
								$newsletter	=	0;
						}
						// Recherche si l'email existe déjà dans la table newsletter
						$query1	=	"SELECT * FROM t_newsletter_news WHERE NEWS_email='$mail'";
						$result1	=	mysql_query($query1,	$connexion)	or	die(mysql_error());
						// Insertion du mail et de la langue dans la table newsletter
						if	(!mysql_num_rows($result1)	&&	$newsletter	==	1)	{
								$query2	=	"INSERT INTO t_newsletter_news (NEWS_email, NEWS_langue) VALUES ('$mail', '$langue')";
								$check	=	adn_mysql_query($query2,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
						}

						// Création du nouvel adhérant, et récupération de l'id
						$query	=	adn_creerInsert('t_adherent_adh',	array('action',	'ADH_id',	'Submit',	'ADH_password_confirm'),	$autreData,	$formatData);
						$check	=	adn_mysql_query($query,	$connexion,	array('insEtape1_ok',	'add_ko'),	array('message_user',	'message_debug'));
						$newId	=	mysql_insert_id();

						// Création de l'identifiant unique et mise à jour pour finaliser l'ajout
						if	($check)	{
								$idAdh	=	mb_strtoupper($_POST["ADH_prenom"][0])	.	mb_strtoupper($_POST["ADH_nom"][0])	.	"_"	.	$newId	.	"_"	.	$idClient;
								$query3	=	"UPDATE t_adherent_adh SET ADH_identifiant='$idAdh' WHERE "	.	"ADH_id=$newId";
								$check	=	adn_mysql_query($query3,	$connexion,	array('insEtape1_ok',	'add_ko'),	array('message_user',	'message_debug'));
						}

						// Mise à jour de la table log
						if	($check)	{
								$query4	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'ADH', '$newId', 'add')";
								$check	=	adn_mysql_query($query4,	$connexion,	array('insEtape1_ok',	'add_ko'),	array('message_user',	'message_debug'));
								$newLogId	=	mysql_insert_id();
						}

						// Envoie du mail
						if	($check)	{
								$lien	=	"http://"	.	$_SERVER['HTTP_HOST']	.	$_SERVER["PHP_SELF"]	.	"?id="	.	$code;

								$titre	=	_("Inscription sur e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];
								$text	=	_("Bonjour ")	.	$str_retour	.	$str_retour;
								$text	.=	_("Vous avez été correctement inscrit sur e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom']	.	$str_retour	.	$str_retour;
								$text	.=	_("Veuillez confirmer votre inscription en suivant ce lien :")	.	$str_retour;
								$text	.=	$lien	.	$str_retour	.	$str_retour;
								$text	.=	_("Votre identifiant est :")	.	" "	.	$idAdh	.	$str_retour;
								$text	.=	_("Votre mot de passe est :")	.	" "	.	$_POST['ADH_password']	.	$str_retour	.	$str_retour;
								$text	.=	_("Cordialement.")	.	$str_retour;
								$text	.=	_("L'équipe de e-venement.com pour")	.	" "	.	$_SESSION['info_client']['nom'];

								if	(!adn_envoiMail($_POST['ADH_email'],	$titre,	$text,	$mailEnvoi))	{
										$_SESSION['message_user']	=	'sendIdByEmail_ko';
								}
						}
				}
		}
		adn_myRedirection("inscription_fin.php");
}
?>