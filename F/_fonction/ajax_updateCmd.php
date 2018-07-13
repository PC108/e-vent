<?php

//DEBUGGAGE
// header("Content-Type: text/html; charset=iso-8859-1");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");
// Desactive le formatage HTML de Xdebug pour pouvoir lire les erreurs dans le fichier .xls
ini_set('html_errors',	0);

/*	* ******************************************************** */
/*              Connexion DB  + autres            */
/*	* ******************************************************** */
include('_shared_ajax.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerDelete.php');
require_once('../../B/commande/_requete.php');
// Traduction
require_once('../../librairie/php/code_adn/AfficheSession.php');
require_once('../../localisation/localisation.php');

$msg	=	"ok";
/* -------------------------------------------------------------------------------------- */
/* Vérifie s'il existe déjà une commande en cours pour cet adhérent, sinon la crée.   */
/* -------------------------------------------------------------------------------------- */
$idAcheteur	=	$_SESSION['info_adherent']['id_adh'];

if	(!isset($_SESSION['info_cmd']))	{	// Aucune commande n'a encore été initialisée
			$date	=	date("Y-m-d");
			$lien	=	uniqid('',	true);
			$query	=	"INSERT INTO t_commande_cmd (FK_ECMD_id, FK_ADH_id, CMD_date, CMD_lien) VALUES ('2', '$idAcheteur', '$date', '$lien')";
			if	(!mysql_query($query,	$connexion))	{
						$msg	=	_("un problème est survenu lors de la création de la commande. Opération annulée.");
						$classMsg	=	"ui-state-error";
			}	else	{
						// Crée la référence de la commande
						$idCmd	=	mysql_insert_id();
						$refCmd	=	$_SESSION['info_client']['suffixe']	.	"-"	.	date("ymd")	.	"-"	.	$idCmd;
						$query	=	"UPDATE t_commande_cmd SET CMD_ref='"	.	$refCmd	.	"' WHERE CMD_id="	.	$idCmd;
						if	(!mysql_query($query,	$connexion))	{
									$msg	=	_("un problème est survenu lors de la création de la commande. Opération annulée.");
									$classMsg	=	"ui-state-error";
						}	else	{
									$_SESSION['info_cmd']['id_cmd']	=	$idCmd;
									$_SESSION['info_cmd']['ref_cmd']	=	$refCmd;
									$_SESSION['info_cmd']['date_cmd']	=	$date;
									$_SESSION['info_cmd']['lien_cmd']	=	$lien;

									// voir plus bas :
									// $_SESSION['total_cmd'] = ?;
									// $_SESSION['info_cmd'] = $infoCmd;
									//
									// Met à jour la table des logs pour la création de la commande
									// Pour info, ne gère par les logs au niveau des achats
									$majPar	=	getSiteUserId($langue);
									$majLe	=	date('Y-m-d H:i:s');
									$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'CMD', '$idCmd', 'add')";
									mysql_query($query2,	$connexion);
						}
			}
}

/* -------------------------------------------------------------------------------------- */
/* Prépare l'ajout ou la suppression de l'achat. */
/* -------------------------------------------------------------------------------------- */
if	($msg	==	"ok")	{
			// Récupère les données de session pour $autresData
			$idCmd	=	$_SESSION['info_cmd']['id_cmd'];
			$autresData['FK_CMD_id']	=	$idCmd;
			// $idBeneficiaire = $_SESSION['info_beneficiaire']["id_benef"]; // Ne fonctionne pas pour le delete car le beneficiaire dans la session n'est pas nécessairement celui dont on enlève un achat
			$idBeneficiaire	=	$_POST['FK_ADH_id'];

			// Charge le bonne inclusion dans le message en fonction du type d'achat
			switch	($_POST['FK_TYACH_id'])	{
						case	(1)	:
									$includeMsg	=	_("cette cotisation");
									break;
						case	(2)	:
									$includeMsg	=	_("ce don");
									break;
						default:
									$includeMsg	=	_("cet achat");
									break;
			}

			// Vérifie les informations envoyées
			switch	($_POST['action'])	{

						case	'add':
									/* -------------------------------------------------------------------------------------- */
									/* ADD : Vérifie la cohérence des infos envoyées */
									/* -------------------------------------------------------------------------------------- */
									// Vérifie l'existence des informations minimums obligatoires pour créer un achat
									if	(!isset($_POST['FK_TYACH_id'])	||	!isset($_POST['ACH_montant'])	||	!isset($_POST['FK_ADH_id']))	{
												$msg	=	_("Informations incomplètes pour enregistrer ")	.	$includeMsg;
												$classMsg	=	"ui-state-error";
									}
									// Vérifie qu'on ne commande pas un jour evenement en même temps qu'une cotisation ou un don
									// ou qu'on commande un hébergement ou une restauration sans jour événement associé.
									if	($msg	==	"ok")	{
												$arrayTest	=	array('FK_TYDON_id'	=>	0,	'FK_TYCOT_id'	=>	0,	'FK_JREVEN_id'	=>	0);
												$arrayDiff	=	array_diff_key($arrayTest,	$_POST);	//normalement = 2
												if	(count($arrayDiff)	!=	count($arrayTest)	-	1)	{
															$msg	=	ucfirst($includeMsg)	.	_(" n'est pas valide. Il n'a pas été ajouté à la commande.");
															$classMsg	=	"ui-state-error";
												}
									}
									// Vérifie que cet achat pour cette personne dans cette commande n'existe pas déjà sauf pour le don
									if	($msg	==	"ok"	&&	$_POST['FK_TYACH_id']	!=	2)	{	// Don
												$query_add	=	"";
												foreach	($_POST	as	$key	=>	$value)	{
															if	(!in_array($key,	array('action')))	{	// ne prends pas en compte $_POST['action']
																		$query_add	.=	" AND "	.	$key	.	"='"	.	$value	.	"'";
															}
												}
												$query	=	"SELECT * FROM t_achat_ach WHERE FK_CMD_id='"	.	$idCmd	.	"' AND FK_ADH_id ='"	.	$idBeneficiaire	.	"'"	.	$query_add;
												$RSTest	=	mysql_query($query,	$connexion)	or	die(mysql_error());
												$nbre_RSTest	=	mysql_num_rows($RSTest);
												if	($nbre_RSTest)	{
															$msg	=	ucfirst($includeMsg)	.	_(" est déjà présent dans la commande.");
															$classMsg	=	"ui-state-error";
												}
									}
									// Si c'est un don, vérifie le contenu de $_POST['montant_don']
									if	($msg	==	"ok"	&&	isset($_POST['montant_don'])	&&	(($_POST['montant_don']	==	"")	||	(!is_numeric($_POST['montant_don']))))	{
												$msg	=	ucfirst($includeMsg)	.	_(" n'a pas un montant valide.");
												$classMsg	=	"ui-state-error";
									}
									/* -------------------------------------------------------------------------------------- */
									/* ADD + surcout : Vérifie si il faut prendre en compte le surcout pour un événement */
									/* -------------------------------------------------------------------------------------- */
									// Si l'ajout est une cotisation, supprime tous les surcouts des jours événements pour l'adhérent et la commande correspondante
									if	($msg	==	"ok"	&&	$_POST['FK_TYACH_id']	==	1)	{
												$query	=	"UPDATE t_achat_ach SET ACH_surcout=0 WHERE FK_CMD_id='"	.	$idCmd	.	"' AND FK_ADH_id ='"	.	$idBeneficiaire	.	"' AND FK_TYACH_id=3";
												$check	=	mysql_query($query,	$connexion)	or	die(mysql_error());
												if	(!$check)	{
															$msg	==	_("problème lors de la suppression des surcoûts pour cette commande");
												}
									}

									// Si c'est un jour événement, vérifie si il faut ajouter un surcout. Cette vérification se fait en 2 temps.
									if	($msg	==	"ok"	&&	$_POST['FK_TYACH_id']	==	3	&&	$_POST['ACH_surcout']	>	0)	{	// Jour événement
												// 1 / vérifie qu'il n'y a pas déjà une cotisation commandée pour cette personne
												$check	=	isCotisationsForAdherentInCommande($idCmd,	$idBeneficiaire,	$connexion);
												if	($check)	{
															$_POST['ACH_surcout']	=	0;
												}	else	{
															// 2 / vérifie si l'année de cotisation correspond à l'année en cours.
															// Si c'est le cas, remplace la valeur du surcout dans POST par 0.
															$check	=	isCotisationOk($idBeneficiaire,	$connexion);
															if	($check)	{
																		$_POST['ACH_surcout']	=	0;
															}
												}
									}

									/* -------------------------------------------------------------------------------------- */
									/* AJOUT : Ajoute l'achat */
									/* -------------------------------------------------------------------------------------- */
									if	($msg	==	"ok")	{
												// Ajoute la valeur du ratio du type de tarif du bénéficiare à renseigner en même temps que le reste de l'achat
												// Sauf si l'événement est en Plein Tarif
												$ratioBeneficaire	=	$_SESSION['info_beneficiaire']['ratio_tarif'];

												if	(in_array($_POST['FK_TYACH_id'],	array(3,	4,	5)))	{	//Jour évenement, hébergement ou restauration
															// Annule le calcul du ratio si l'événement est en plein tarif.
															// A garder ici car FK_JREVEN_id n'est envoyé en POST que pour les Jour évenement, hébergement ou restauration
															$check	=	isEventPleinTarif($_POST['FK_JREVEN_id'],	$connexion);
															if	(!$check)	{
																		$autresData['ACH_ratio']	=	$ratioBeneficaire;
															}	else	{
																		// Inutile. Déjà la valeur par défaut dans la structure de la table
																		// $autresData['ACH_ratio'] = 100;
															}
												}
												// Création de la requete
												$query	=	adn_creerInsert('t_achat_ach',	array('action'),	$autresData);
												// Execution de la requete
												if	(mysql_query($query,	$connexion))	{
															$msg	=	ucfirst($includeMsg)	.	sprintf(_(" a été %s ajouté %s à votre commande"),	"<b>",	"</b>");
															$classMsg	=	"ui-state-highlight";
												}	else	{
															$msg	=	_("Un problème est survenu lors de l'ajout de")	.	" "	.	$includeMsg	.	" "	.	_("à votre commande. Merci de vérifier.");
															$classMsg	=	"ui-state-error";
												}
									}
									break;

						case	'delete';
									// Les dons et cotisations ne peuvent être supprimés que depuis la page commande/commande.php
									// Les informations envoyées en POST sont FK_TYACH_id et ACH_id
									// Les jour événements  peuvent être supprimés depuis la page evenement/evenement.php ET commande/commande.php
									// Les informations envoyées en POST sont FK_TYACH_id et FK_JREVEN_id depuis les 2 pages
									// Les options des jours événements  peuvent être supprimés depuis la page evenement/evenement.php ET commande/commande.php
									// Depuis la page evenement/evenement.php, les informations envoyées en POST sont FK_TYACH_id et FK_JREVEN_id et (FK_TYRES_id ou FK_TYHEB_id)
									// Depuis la commande/commande.php, les informations envoyées en POSTsont FK_TYACH_id et ACH_id
									// Trop compliqué pour faire un check des des informations minimums.

									/* -------------------------------------------------------------------------------------- */
									/* DELETE : Supprime l'achat */
									/* -------------------------------------------------------------------------------------- */
									// Création de la requete
									// Important ! La présence de 'FK_TYACH_id' dans l'array exception permet de supprimer en même temps toutes les options associées au jour événement
									if	($_POST['FK_TYACH_id']	==	3)	{
												$query	=	adn_creerDelete('t_achat_ach',	array('action',	'FK_TYACH_id'),	$autresData);
									}	else	{
												$query	=	adn_creerDelete('t_achat_ach',	array('action'),	$autresData);
									}
									// echo $query;
									// Execution de la requete
									if	(mysql_query($query,	$connexion))	{
												$msg	=	ucfirst($includeMsg)	.	sprintf(_(" a été %s retiré %s à votre commande"),	"<b>",	"</b>");
												$classMsg	=	"ui-state-highlight";
									}	else	{
												$msg	=	_("Un problème est survenu lors du retrait de")	.	" "	.	$includeMsg	.	" "	.	_("à votre commande. Merci de vérifier.");
												$classMsg	=	"ui-state-error";
									}

									/* -------------------------------------------------------------------------------------- */
									/* DELETE + surcout : Vérifie si il faut prendre en compte le surcout pour un événement */
									/* -------------------------------------------------------------------------------------- */
									// Si on supprime une cotisation, vérifie qu'il n'y a plus de cotisation dans la commande
									if	($_POST['FK_TYACH_id']	==	1)	{
												$check	=	isCotisationsForAdherentInCommande($idCmd,	$idBeneficiaire,	$connexion);
												if	(!$check)	{
															// Si c'est le cas, vérifie s'il faut remettre les surcouts associés aux jours-événements
															$check	=	isCotisationOk($idBeneficiaire,	$connexion);
															if	(!$check)	{
																		$query	=	"UPDATE t_achat_ach
			    SET ACH_surcout = (SELECT JREVEN_surcout FROM t_jourevent_jreven WHERE JREVEN_id = FK_JREVEN_id)
			    WHERE FK_CMD_id='"	.	$idCmd	.	"' AND FK_ADH_id ='"	.	$idBeneficiaire	.	"' AND FK_TYACH_id=3";
																		$check	=	mysql_query($query,	$connexion)	or	die(mysql_error());
																		if	(!$check)	{
																					$msg	==	_("problème lors de la mise à jour des surcoûts pour cette commande");
																		}
															}
												}
									}

									break;	// du delete
			}
			/* -------------------------------------------------------------------------------------- */
			/* Récupère le total */
			/* -------------------------------------------------------------------------------------- */
			// Récupération du total de la commande via la requete dans '../../B/commande/_requete.php'
			$query	=	mainQueryCmd($connexion);
			$query	.=	" WHERE CMD_id="	.	$idCmd;
			$RSCmd	=	mysql_query($query,	$connexion);
			$rowCmd	=	mysql_fetch_object($RSCmd);
			$total	=	$rowCmd->totalCommande;
			$_SESSION['info_cmd']['total_cmd']	=	$total;
}

/* -------------------------------------------------------------------------------------- */
/* Affiche la boite de dialogue  */
/* -------------------------------------------------------------------------------------- */
// Désactivation du système en slide depuis le haut de la page
/*
	 $html	=	'<div class="confirmachat corner20-bottom">';
	 $html	.=	'<div class="'	.	$classMsg	.	' corner10-all uiphil-msg">';
	 $html	.=	'<p>'	.	$msg	.	'</p>';
	 $html	.=	'</div>';
	 $html	.=	'<h2><div id="totalfromboxachat">'	.	_("Total =")	.	" "	.	$total	.	' €</div></h2>';
	 $html	.=	'<a href="../commande/result.php"><div class="bt_lien corner10-all">'	.	_("voir le détail de la commande & payer")	.	'</div></a>';
	 $html	.=	'</div>';
	*/

$html	=	'<div style="text-align: center">';
$html	.=	'<div class="'	.	$classMsg	.	' corner10-all uiphil-msg">';
$html	.=	'<p>'	.	$msg	.	'</p>';
$html	.=	'</div>';
$html	.=	'<h2><div id="totalfromboxachat">'	.	_("Total =")	.	" "	.	$total	.	' €</div></h2>';
$html	.=	'<div class="note">'	.	_("touche [échap] pour fermer cette boite de dialogue")	.	'</div>';
$html	.=	'</div>';

echo	$html;

/* -------------------------------------------------------------------------------------- */
/* FONCTIONS */
/* -------------------------------------------------------------------------------------- */

function	isCotisationsForAdherentInCommande($idCmd,	$idAdh,	$connexion)	{
			$query	=	"SELECT * from t_achat_ach WHERE FK_TYACH_id=1 AND  FK_CMD_id='"	.	$idCmd	.	"' AND FK_ADH_id='"	.	$idAdh	.	"'";
			$RSTest	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			$nbre_RSTest	=	mysql_num_rows($RSTest);
			if	($nbre_RSTest	>	0)	{	// Une cotisation est déjà commandée pour cette personne...
						return	TRUE;
			}	else	{
						return	FALSE;
			}
}

function	isCotisationOk($idAdh,	$connexion)	{
			$query	=	"SELECT ADH_annee_cotisation FROM t_adherent_adh WHERE ADH_id='"	.	$idAdh	.	"'";
			$RSTest	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			$rowTest	=	mysql_fetch_object($RSTest);
			if	((!is_null($rowTest->ADH_annee_cotisation))	&&	($rowTest->ADH_annee_cotisation	==	date("Y")))	{
						return	TRUE;
			}	else	{
						return	FALSE;
			}
}

function	isEventPleinTarif($idJrEvent,	$connexion)	{
			$query	=	"SELECT t2.JREVEN_id, t1.EVEN_pleintarif FROM t_evenement_even AS t1
						LEFT JOIN t_jourevent_jreven AS t2 ON t1.EVEN_id = t2.FK_EVEN_id
						WHERE  t2.JREVEN_id = '"	.	$idJrEvent	.	"'";
			$RSTest	=	mysql_query($query,	$connexion)	or	die(mysql_error());
			$rowTest	=	mysql_fetch_object($RSTest);
			if	($rowTest->EVEN_pleintarif == 1)	{	// L'événement est plein tarif
						return	TRUE;
			}	else	{
						return	FALSE;
			}
}
?>