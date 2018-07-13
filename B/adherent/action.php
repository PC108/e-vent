<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/MyRedirection.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/CheckEmailPHP.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');

$autoriseDelete	=	TRUE;	// Sécurité pour action delete via le get
$table	=	't_adherent_adh';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Gestion du numéro de page                  */
/*	* ******************************************************** */

// On garde le numéro de page courant en paramètre
// POST lorsqu'on vient de add.php(add & maj), GET quand on vient de result.php (del)
if	(isSet($_POST['pageNum']))	{
			$pageNum	=	$_POST['pageNum'];
}	elseif	(isSet($_GET['pageNum']))	{
			$pageNum	=	$_GET['pageNum'];
}

/*	* ******************************************************** */
/*             Vérification des variables                  */
/*	* ******************************************************** */
$checkRequired	=	"ok";

// Vérifie $action
// récupération des variables POST avec gestion des échappements
if	(isSet($_POST['action']))	{
			$action	=	$_POST['action'];
			$id	=	$_POST['ADH_id'];

			if	(!isSet($_POST["ADH_genre"]))	{
						$checkRequired	=	"bad";
			}

			// Mise des valeurs pour les checkbox à choix unique (boolean)
			if	(isSet($_POST['ADH_benevolat']))	{
						$autresData['ADH_benevolat']	=	1;
						if	($_POST["ADH_profession"]	==	"")	{
									$checkRequired	=	"bad";
						}	else	{
									$autresData['ADH_profession']	=	$_POST['ADH_profession'];
									$autresData['ADH_disponibilite']	=	$_POST['ADH_disponibilite'];
						}
			}	else	{
						$autresData['ADH_benevolat']	=	0;
						$autresData['ADH_profession']	=	"";
						$autresData['ADH_disponibilite']	=	"";
			}

			if	(isSet($_POST['ADH_ordination']))	{
						$autresData['ADH_ordination']	=	1;
			}	else	{
						$autresData['ADH_ordination']	=	0;
			}

			if	(isSet($_POST['NEWS_email']))	{
						$newsletter	=	1;
			}	else	{
						$newsletter	=	0;
			}

			if	(isSet($_POST['ADH_prive']))	{
						$autresData['ADH_prive']	=	1;
			}	else	{
						$autresData['ADH_prive']	=	0;
			}


			// Vérification des champs obligatoires, plus le mail, plus les années
			if	(($_POST["ADH_nom"]	==	"")	||	($_POST["ADH_prenom"]	==	"")	||	($_POST["ADH_email"]	==	"")	||	($_POST["ADH_langue"]	==	"")	||	(!adn_checkEmailPHP($_POST['ADH_email']))
											||	(!($_POST["ADH_annee_naissance"]	==	""))	&&	((intval($_POST["ADH_annee_naissance"])	<	1900)	||	intval(($_POST["ADH_annee_naissance"])	>	date("Y")))
											||	(!($_POST["ADH_annee_cotisation"]	==	""))	&&	((intval($_POST["ADH_annee_cotisation"])	<	1900)	||	intval(($_POST["ADH_annee_cotisation"])	>	date("Y")	+	1))
											||	(!($_POST["ADH_password"]	==	""))	&&	((strlen($_POST["ADH_password"])	<	4)	||	(strlen($_POST["ADH_password"])	>	16))	||	($_POST["ADH_password"]	==	""))	{
						$checkRequired	=	"bad";
			}	else	{
						// $formatData['ADH_nom'] = 'UPPER';
						// $formatData['ADH_prenom'] = 'LOWERFIRST';
						// $formatData['ADH_ville'] = 'LOWERFIRST';
						// $formatData['ADH_nom_dharma'] = 'LOWERFIRST';
						// $formatData['ADH_profession'] = 'LOWERFIRST';
						// $formatData['ADH_adresse1'] = 'LOWERFIRST';
						// $formatData['ADH_adresse2'] = 'LOWERFIRST';
						$formatData['ADH_email']	=	'LOWER';
						// $formatData = array();
						$checkArrayVide	=	array();
			}
}	else	{
			$action	=	"del";
			$id	=	$_GET["id"];
}

/*	* ******************************************************** */
/*                Exécution des actions                    */
/*	* ******************************************************** */

if	($checkRequired	==	"bad")	{
			$_SESSION['message_user']	=	"bad_post";
}	else	{
			if	($action	!=	"del")	{
						$mail	=	$_POST['ADH_email'];
						$langue	=	$_POST['ADH_langue'];
			}

			$majPar	=	$_SESSION['user_info'][2];
			$majLe	=	date('Y-m-d H:i:s');
			switch	($action)	{

						case	"maj":
									// Mise à jour de la table adhérent
									$query1	=	adn_creerUpdate($table,	'ADH_id',	$id,	array('action',	'ADH_id',	'Submit',	'ADH_ordination',	'ADH_benevolat',	'ADH_disponibilite',	'ADH_profession',	'NEWS_email',	'CMPT_id',	'pageNum',	'ADH_prive'),	$autresData,	$formatData,	$checkArrayVide);
									$check	=	adn_mysql_query($query1,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));

									// Suppression des compétences associés à l'adhérent dans la table jointe
									if	($check)	{
												$query2	=	"DELETE FROM tj_adh_cmpt WHERE TJ_ADH_id='"	.	mysql_real_escape_string($id)	.	"'";
												$check	=	adn_mysql_query($query2,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}

									// Mise à jour de la table jointe des compétences et adhérents
									if	(isSet($_POST['CMPT_id'])	&&	$autresData['ADH_benevolat']	==	1)	{
												foreach	($_POST['CMPT_id']	as	$value)	{
															if	($check)	{
																		$query3	=	"INSERT INTO tj_adh_cmpt (TJ_ADH_id, TJ_CMPT_id) VALUES ('$id' ,'$value')";
																		$check	=	adn_mysql_query($query3,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
															}
												}
									}

									// Mise à jour de la table t_newsletter_news
									if	($check)	{
												$query4	=	"SELECT * FROM t_newsletter_news WHERE NEWS_email = (SELECT ADH_email FROM t_adherent_adh WHERE ADH_id = $id)";
												$result	=	mysql_query($query4,	$connexion)	or	die(mysql_error());
												if	(!mysql_num_rows($result))	{
															if	($newsletter)	{
																		//ADD
																		$query5	=	"INSERT INTO t_newsletter_news (NEWS_email, NEWS_langue) VALUES ('$mail', '$langue')";
																		$check	=	adn_mysql_query($query5,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
															}	else	{
																		$check	=	1;
															}
												}
												if	(mysql_num_rows($result))	{
															if	($newsletter)	{
																		//MAJ
																		$query5	=	"UPDATE t_newsletter_news SET NEWS_email='$mail', NEWS_langue='$langue' WHERE NEWS_email = (SELECT ADH_email FROM t_adherent_adh WHERE ADH_id = $id)";
																		$check	=	adn_mysql_query($query5,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
															}	else	{
																		//DEL
																		$query5	=	"DELETE FROM t_newsletter_news WHERE NEWS_email = (SELECT ADH_email FROM t_adherent_adh WHERE ADH_id = $id)";
																		$check	=	adn_mysql_query($query5,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
															}
												}
									}

									// Mise à jour de la table log
									if	($check)	{
												$query6	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'ADH', '$id', '$action')";
												adn_mysql_query($query6,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}
									break;

						case	"add":
									// Recherche si l'email existe déjà dans la table newsletter
									$query1	=	"SELECT * FROM t_newsletter_news WHERE NEWS_email='$mail'";
									$result1	=	mysql_query($query1,	$connexion)	or	die(mysql_error());
									$check	=	$result1;
									// Insertion du mail et de la langue dans la table newsletter
									if	(!mysql_num_rows($result1)	&&	$newsletter	==	1)	{
												$query2	=	"INSERT INTO t_newsletter_news (NEWS_email, NEWS_langue) VALUES ('$mail', '$langue')";
												$check	=	adn_mysql_query($query2,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
									}

									// Création du nouvel adhérant
									if	($check)	{
												// Créer aussi un code unique au cas où la personne devient une realtion dans une autre commande. Sinon bug lorsqu'on bascule sur cette personne.
												$code	=	md5(uniqid());
												$autresData['ADH_lien']	=	$code;
												
												$query	=	adn_creerInsert($table,	array('action',	'ADH_id',	'Submit',	'ADH_ordination',	'ADH_benevolat',	'ADH_disponibilite',	'ADH_profession',	'CMPT_id',	'pageNum',	'ADH_prive'),	$autresData,	$formatData);
												$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
									}

									// Création de l'identifiant unique et mise à jour pour finaliser l'ajout
									if	($check)	{
												// récupération de l'id
												$newId	=	mysql_insert_id();
												// récupération du suffixe du client
												$query_client	=	"SELECT CLI_suffixe FROM t_client_cli";
												$RS_client	=	mysql_query($query_client,	$connexion)	or	die(mysql_error());
												$row	=	mysql_fetch_object($RS_client);
												// Maj dans la base de données
												$idAdh	=	mb_strtoupper($_POST["ADH_prenom"][0])	.	mb_strtoupper($_POST["ADH_nom"][0])	.	"_"	.	$newId	.	"_"	.	$row->CLI_suffixe;
												$query3	=	"UPDATE $table SET ADH_identifiant='$idAdh' WHERE ADH_id=$newId";
												$check	=	adn_mysql_query($query3,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}

									// Insertions des compétences liées à l'adhérent dans la table jointe
									if	($check)	{
												if	(isSet($_POST['CMPT_id']))	{
															$query4	=	"INSERT INTO tj_adh_cmpt (TJ_ADH_id, TJ_CMPT_id) VALUES ";
															foreach	($_POST['CMPT_id']	as	$value)	{
																		$query4	.=	"('$newId' ,'$value'),";
															}
															$check	=	adn_mysql_query(rtrim($query4,	','),	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
												}
									}

									// Mise à jour de la table log
									if	($check)	{
												$query5	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'ADH', '$newId', '$action')";
												adn_mysql_query($query5,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
									}
									break;

						case	"del":
									if	($autoriseDelete)	{
												$query	=	"DELETE FROM "	.	$table	.	" WHERE ADH_id='"	.	mysql_real_escape_string($id)	.	"'";
												$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));

												if	($check)	{
															$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'ADH', '$id', '$action')";
															adn_mysql_query($query2,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
												}
									}
									break;
			}
}

$page	=	"result.php?pageNum="	.	$pageNum;
if	($action	==	"add")	{	$page	.=	"&clean=1";	}
adn_myRedirection($page);
?>