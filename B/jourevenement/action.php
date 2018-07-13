<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('../../librairie/php/code_adn/NombreJours.php');

$autoriseDelete	=	TRUE;	// Sécurité pour action delete via le get
$table	=	't_jourevent_jreven';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
		$_SESSION['message_user']	=	"acces_ko";
		adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Gestion du numéro de page                  */
/*	* ******************************************************** */
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

// récupération des variables POST avec gestion des échappements
if	(isSet($_POST['action']))	{
		$action	=	$_POST['action'];
		$id	=	$_POST['JREVEN_id'];

		$autresData['JREVEN_date_debut']	=	adn_changeFormatDate($_POST['JREVEN_date_debut'],	"FR_DB");
		$autresData['JREVEN_date_fin']	=	adn_changeFormatDate($_POST['JREVEN_date_fin'],	"FR_DB");

		// Vérification des champs obligatoires, plus le mail, plus les années
		if	(($_POST["JREVEN_date_debut"]	==	"")
										||	($_POST["JREVEN_date_fin"]	==	"")
										||	($_POST["JREVEN_montant"]	==	"")
										||	($_POST["FK_LEVEN_id"]	==	"")
										||	($_POST["JREVEN_places"]	==	"")
										||	($_POST["JREVEN_places"]	<	0)
										||	adn_nbJours($_POST['alternateDeb'],	$_POST['alternateFin'])	<	1)	{	// fonction externe NombreJours.php
				$checkRequired	=	"bad";
		}	else	{
				$formatData	=	array();
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
		$majPar	=	$_SESSION['user_info'][2];
		$majLe	=	date('Y-m-d H:i:s');

		switch	($action)	{
				case	"maj":
						$JREVEN_id	=	$_POST['JREVEN_id'];
						// étape 1
						// Récupération des ID la table TYHEB pour vérifier ensuite si des case ont été décochées
						// et supprimer les enregistrements correspondants dans la table jointe.
						$query	=	"SELECT TYHEB_id FROM t_typehebergement_tyheb";
						$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
						$nbreRows_RS	=	mysql_num_rows($RS);
						if	($nbreRows_RS)	{
								while	($row	=	mysql_fetch_object($RS))	{
										$idsTYHEB[]	=	$row->TYHEB_id;
								}
								// étape 2
								// Crée, met à jour ou supprime les infos relatives à la table TYHEB dans la table jointe
								foreach	($idsTYHEB	as	$value)	{
										// Verifie si l'enregistrement correspondant à la boucle est présent dans la table jointe
										// Vérifie aussi si il a un achat associé au cas ou on voudrait le supprimer
										$query	=	"
										SELECT * 
										FROM tj_tyheb_jreven 
										LEFT JOIN t_achat_ach
										ON TJ_JREVEN_id = FK_JREVEN_id 
										AND TJ_TYHEB_id = FK_TYHEB_id 
										WHERE TJ_JREVEN_id = $JREVEN_id AND TJ_TYHEB_id = $value";
										$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
										$nbreRep	=	mysql_num_rows($RS);
										$clePOST	=	"TYHEB_"	.	$value;
										if	(array_key_exists($clePOST,	$_POST))	{
												// Met à jour la table jointe
												$montantHeb	=	$_POST[$clePOST][1];
												$capaciteHeb	=	$_POST[$clePOST][2];
												if	($nbreRep	==	0)	{	//crée l'enregsitrement
														$query	=	"INSERT INTO tj_tyheb_jreven (TJ_TYHEB_id, TJ_JREVEN_id, TYHEB_JREVEN_montant, TYHEB_JREVEN_capacite) VALUES ('$value', '$JREVEN_id', '$montantHeb', '$capaciteHeb')";
														$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
												}	else	{	//met à jour l'enregistrement
														$query	=	"UPDATE tj_tyheb_jreven SET TYHEB_JREVEN_montant='$montantHeb', TYHEB_JREVEN_capacite='$capaciteHeb'  WHERE TJ_TYHEB_id=$value AND TJ_JREVEN_id=$JREVEN_id";
														$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
												}
												// supprime les valeurs du $_POST
												unset($_POST[$clePOST]);
										}	else	{
												// Vérifie si l'enregistrement existe dans la table jointe et s'il n'a pas d'achat associés. Si oui, le supprime.
												$row	=	mysql_fetch_object($RS);
												if	(is_null($row->ACH_id))	{	// Pas d'achats associés
														$query	=	"DELETE FROM tj_tyheb_jreven WHERE TJ_TYHEB_id=$value AND TJ_JREVEN_id=$JREVEN_id";
														$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
												}	else	{
														$check	=	0;
														$_SESSION['message_user']	=	"del_option_lie";
														adn_myRedirection('add.php?idEven='	.	$_POST['FK_EVEN_id']	.	'&idJour='	.	$_POST['JREVEN_id']	.	'&pageNum='	.	$_POST['pageNum']);
														exit;
												}
										}
								}
						} else {
								$check = 1;
						}

						if	($check)	{
								// étape 3
								// même chose que étape 1 pour la table TYRES
								$query	=	"SELECT TYRES_id FROM t_typerestauration_tyres";
								$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
								$nbreRows_RS	=	mysql_num_rows($RS);
								if	($nbreRows_RS)	{
										while	($row	=	mysql_fetch_object($RS))	{
												$idsTYRES[]	=	$row->TYRES_id;
										}
										// étape4
										// même chose que étape 4 pour la table TYRES
										foreach	($idsTYRES	as	$value)	{
												// Verifie si l'enregistrement correspondant à la boucle est présent dans la table jointe
												// Vérifie aussi si il a un achat associé au cas ou on voudrait le supprimer
												$query	=	"
												SELECT * 
												FROM tj_tyres_jreven 
												LEFT JOIN t_achat_ach
												ON TJ_JREVEN_id = FK_JREVEN_id 
												AND TJ_TYRES_id = FK_TYRES_id 
												WHERE TJ_JREVEN_id = $JREVEN_id AND TJ_TYRES_id = $value";
												$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
												$nbreRep	=	mysql_num_rows($RS);
												$clePOST	=	"TYRES_"	.	$value;
												if	(array_key_exists($clePOST,	$_POST))	{
														// Met à jour la table jointe
														$montantRes	=	$_POST[$clePOST][1];
														$capaciteRes	=	$_POST[$clePOST][2];
														if	($nbreRep	==	0)	{	//crée l'enregsitrement
																$query	=	"INSERT INTO tj_tyres_jreven (TJ_TYRES_id, TJ_JREVEN_id, TYRES_JREVEN_montant, TYRES_JREVEN_capacite) VALUES ('$value', '$JREVEN_id', '$montantRes', '$capaciteRes')";
																$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
														}	else	{	//met à jour l'enregistrement
																$query	=	"UPDATE tj_tyres_jreven SET TYRES_JREVEN_montant='$montantRes', TYRES_JREVEN_capacite='$capaciteRes'  WHERE TJ_TYRES_id=$value AND TJ_JREVEN_id=$JREVEN_id";
																$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
														}
														// supprime les valeurs du $_POST
														unset($_POST[$clePOST]);
												}	else	{
														// Vérifie si l'enregistrement existe dans la table jointe et s'il n'a pas d'achat associés. Si oui, le supprime.
														$row	=	mysql_fetch_object($RS);
														if	(is_null($row->ACH_id))	{	// Pas d'achats associés
																$query	=	"DELETE FROM tj_tyres_jreven WHERE TJ_TYRES_id=$value AND TJ_JREVEN_id=$JREVEN_id";
																$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
														}	else	{
																$check	=	0;
																$_SESSION['message_user']	=	"del_option_lie";
																adn_myRedirection('add.php?idEven='	.	$_POST['FK_EVEN_id']	.	'&idJour='	.	$_POST['JREVEN_id']	.	'&pageNum='	.	$_POST['pageNum']);
																exit;
														}
												}
										}
								}
						} else {
								$check = 1;
						}

						if	($check)	{
								// étape 5
								// Met à jour la table principale
								$query	=	adn_creerUpdate($table,	'JREVEN_id',	$id,	array('action',	'JREVEN_id',	'Submit',	'pageNum',	'JREVEN_date_debut',	'JREVEN_date_fin',	'alternateDeb',	'alternateFin'),	$autresData,	$formatData,	$checkArrayVide);
								$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
						}

						if	($check)	{
								// Mise à jour des logs
								$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'JREVEN', '$id', '$action')";
								$check	=	adn_mysql_query($query2,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
						}
						break;

				case	"add":
						// Traitement du $_POST pour extraire toutes les données ne concernant pas la table principale
						// et création des tableaux $infoHEB et infoRES
						// Pour la table HEBERGEMENT
						$newPost	=	array();
						$infoHEB	=	array();
						foreach	($_POST	as	$cle	=>	$value)	{
								$pos	=	strpos($cle,	"TYHEB_");
								if	($pos	===	FALSE)	{
										$newPost[$cle]	=	$value;
								}	else	{
										$info	=	explode("_",	$cle);
										$idHeb	=	$info[1];
										$montantHeb	=	$value[1];
										$capaciteHeb	=	$value[2];
										$infoHEB[$idHeb]	=	array($montantHeb,	$capaciteHeb);
								}
						}
						// Actualisation de $_POST sans les infos sur l'hébergement
						$_POST	=	$newPost;
						// Pour la table RESTAURATION
						$newPost	=	array();
						$infoRES	=	array();
						foreach	($_POST	as	$cle	=>	$value)	{
								$pos	=	strpos($cle,	"TYRES_");
								if	($pos	===	FALSE)	{
										$newPost[$cle]	=	$value;
								}	else	{
										$info	=	explode("_",	$cle);
										$idRes	=	$info[1];
										$montantRes	=	$value[1];
										$capaciteRes	=	$value[2];
										$infoRES[$idRes]	=	array($montantRes,	$capaciteRes);
								}
						}
						// Actualisation de $_POST sans les infos sur l'hébergement
						$_POST	=	$newPost;
						// Mise à jour de la table principale pour récupérer le nouvel id
						$query	=	adn_creerInsert($table,	array('action',	'JREVEN_id',	'Submit',	'pageNum',	'JREVEN_date_debut',	'JREVEN_date_fin',	'alternateDeb',	'alternateFin'),	$autresData,	$formatData);
						$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));

						// Mise à jour de la table Hébergement
						if	($check)	{
								$JREVEN_id	=	mysql_insert_id();
								foreach	($infoHEB	as	$cle	=>	$value)	{
										$idHeb	=	$cle;
										$montantHeb	=	$value[0];
										$capaciteHeb	=	$value[1];
										$query	=	"INSERT INTO tj_tyheb_jreven (TJ_TYHEB_id, TJ_JREVEN_id, TYHEB_JREVEN_montant, TYHEB_JREVEN_capacite) VALUES ('$idHeb', '$JREVEN_id', '$montantHeb', '$capaciteHeb')";
										$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
								}
						}

						// Mise à jour de la table Restauration
						if	($check)	{
								foreach	($infoRES	as	$cle	=>	$value)	{
										$idRes	=	$cle;
										$montantRes	=	$value[0];
										$capaciteRes	=	$value[1];
										$query	=	"INSERT INTO tj_tyres_jreven (TJ_TYRES_id, TJ_JREVEN_id, TYRES_JREVEN_montant, TYRES_JREVEN_capacite) VALUES ('$idRes', '$JREVEN_id', '$montantRes', '$capaciteRes')";
										$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
								}
						}

						// Mise à jour des logs
						if	($check)	{
								$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'JREVEN', '$JREVEN_id', '$action')";
								$check	=	adn_mysql_query($query2,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
						}
						break;

				case	"del":
						if	($autoriseDelete)	{
								$query	=	"SELECT FK_EVEN_id FROM $table WHERE JREVEN_id = $id";
								$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
								$row	=	mysql_fetch_object($RS);
								$idEven	=	$row->FK_EVEN_id;

								$query	=	"DELETE FROM "	.	$table	.	" WHERE JREVEN_id='"	.	mysql_real_escape_string($id)	.	"'";
								$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));

								if	($check)	{
										$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'JREVEN', '$id', '$action')";
										adn_mysql_query($query2,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
								}
						}
						break;
		}
}

$page	=	"../evenement/result.php?pageNum="	.	$pageNum;
adn_myRedirection($page);
?>