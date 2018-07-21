<?php

/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared_action.php');
require_once('../../librairie/php/code_adn/GestionErreurMysql.php');
require_once('../../librairie/php/code_adn/QuoteSmart.php');
require_once('../../librairie/php/code_adn/CreerUpdate.php');
require_once('../../librairie/php/code_adn/CreerInsert.php');

$autoriseDelete	=	TRUE;	// Sécurité pour action delete via le get
$table	=	't_evenement_even';

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
			$id	=	$_POST['EVEN_id'];

			// Mise des valeurs pour les checkbox à choix unique (boolean)
			if	(isSet($_POST['EVEN_prive']))	{
						$autresData['EVEN_prive']	=	1;
			}	else	{
						$autresData['EVEN_prive']	=	0;
			}

			if	(isSet($_POST['EVEN_pleintarif']))	{
						$autresData['EVEN_pleintarif']	=	1;
			}	else	{
						$autresData['EVEN_pleintarif']	=	0;
			}

			// récupération des documents à lier
			if	(isSet($_POST['document']))	{
						$documents	=	$_POST['document'];
			}	else	{
						$documents	=	array();
			}

// Vérification des champs obligatoires, plus le mail, plus les années
			if	(($_POST["EVEN_nom_fr"]	==	"")	||	($_POST["EVEN_nom_en"]	==	"")	||	($_POST["EVEN_descriptif_fr"]	==	"")	||	($_POST["EVEN_descriptif_en"]	==	""))	{
						$checkRequired	=	"bad";
			}	else	{
						// $formatData['EVEN_nom_fr'] = 'LOWERFIRST';
						// $formatData['EVEN_nom_en'] = 'LOWERFIRST';
						// $formatData['EVEN_descriptif_fr'] = 'LOWERFIRST';
						$formatData	=	array();
						$checkArrayVide	=	array();
			}
}	else	{
			$action	=	"del";
			$id	=	$_GET["id"];
}
/*	* ******************************************************** */
/*                Fonction de mise à jour des liens vers les docs                   */
/*	* ******************************************************** */

// Cette fonction est utilisées dans "add" et "maj"
function	majDocumentsLiees($id,	$documents,	$connexion)	{

			$check	=	true;

			// Commence par supprimer les anciens liens à des documents
			$query	=	"DELETE FROM tj_even_doceven WHERE TJ_EVEN_id='"	.	$id	.	"'";
			$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
			// Ajoute les nouveaux liens
			if	($check	&&	count($documents)	>	0)	{
						$strValues	=	"";
						foreach	($documents	as	$value)	{
									$strValues	.=	"("	.	$id	.	","	.	$value	.	"),";
						}
						$strValues	=	rtrim($strValues,	",");
						$query	=	"INSERT INTO tj_even_doceven (TJ_EVEN_id, TJ_DOCEVEN_id) VALUES $strValues";
						$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
			}

			return	$check;
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
									$query	=	adn_creerUpdate($table,	'EVEN_id',	$id,	array('action',	'EVEN_id',	'Submit',	'pageNum',	'EVEN_prive',	'document'),	$autresData,	$formatData,	$checkArrayVide);
									$check	=	adn_mysql_query($query,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));

									// Mis à jour de la liste des documents liés
									$check	=	majDocumentsLiees($id,	$documents,	$connexion);

									// Mise à jour de la table log
									if	($check)	{
												$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'EVEN', '$id', '$action')";
												adn_mysql_query($query2,	$connexion,	array('maj_ok',	'maj_ko'),	array('message_user',	'message_debug'));
									}
									break;
						case	"add":
									$query	=	adn_creerInsert($table,	array('action',	'EVEN_id',	'Submit',	'pageNum',	'EVEN_prive',	'document'),	$autresData,	$formatData);
									$check	=	adn_mysql_query($query,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));

									// Mis à jour de la liste des documents liés
									if	($check)	{
												$newId	=	mysql_insert_id();
												$check	=	majDocumentsLiees($newId,	$documents,	$connexion);
									}

									// Mise à jour de la table log
									if	($check)	{
												$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'EVEN', '$newId', '$action')";
												adn_mysql_query($query2,	$connexion,	array('add_ok',	'add_ko'),	array('message_user',	'message_debug'));
									}
									break;
						case	"del":
									if	($autoriseDelete)	{
												$query	=	"DELETE FROM "	.	$table	.	" WHERE EVEN_id='"	.	mysql_real_escape_string($id)	.	"'";
												$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));

												if	($check)	{
															$query2	=	"INSERT INTO t_log_log (FK_ACS_id, LOG_date, LOG_table, LOG_idrow, LOG_action) VALUES ('$majPar', '$majLe', 'EVEN', '$id', '$action')";
															$check	=	adn_mysql_query($query,	$connexion,	array('del_ok',	'del_ko'),	array('message_user',	'message_debug'));
												}
									}
									break;
			}
}

$page	=	"result.php?pageNum="	.	$pageNum;
if	($action	==	"add")	{	$page	.=	"&clean=1";	}
adn_myRedirection($page);
?>