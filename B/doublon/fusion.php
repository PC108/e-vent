<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../adherent/_requete.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'adher')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*       Variables d'affichages de messages                */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L6']['nom'];

/*	* ******************************************************** */
/*             Vérification des variables                  */
/*	* ******************************************************** */
$msgError	=	FALSE;
if	(isSet($_POST['adherent_keep'])	&&	$_POST['adherent_keep']	!=	"")	{
			$adhToKeep	=	$_POST['adherent_keep'];
			// Charge les infos de l'adherent à garder
			$query_RS_keep	=	mainQueryAdherent();
			$query_RS_keep	.=	" WHERE ADH_identifiant = '$adhToKeep'";
			$RS_keep	=	mysql_query($query_RS_keep,	$connexion)	or	die(mysql_error());
			if	(mysql_num_rows($RS_keep)	==	0)	{
						$msgContent	=	"L'identifiant de l'adhérent à garder est invalide";
						$msgError	=	TRUE;
			}	else	{
						$row_keep	=	mysql_fetch_object($RS_keep);
			}
}	else	{
			$msgContent	=	"L'identifiant de l'adhérent à garder est manquant.";
			$msgError	=	TRUE;
}
if	(isSet($_POST['adherent_merge'])	&&	$_POST['adherent_merge']	!=	"")	{
			$adhToMerge	=	$_POST['adherent_merge'];
			// Charge les infos de l'adherent à fusionner
			$query_RS_merge	=	mainQueryAdherent();
			$query_RS_merge	.=	" WHERE ADH_identifiant = '$adhToMerge'";
			$RS_merge	=	mysql_query($query_RS_merge,	$connexion)	or	die(mysql_error());
			if	(mysql_num_rows($RS_merge)	==	0)	{
						$msgContent	=	"L'identifiant de l'adhérent à fusionner est invalide";
						$msgError	=	TRUE;
			}	else	{
						$row_merge	=	mysql_fetch_object($RS_merge);
			}
}	else	{
			$msgContent	=	"L'identifiant de l'adhérent à fusionner est manquant.";
			$msgError	=	TRUE;
}

/*	* ******************************************************** */
/*                Exécution des actions                    */
/*	* ******************************************************** */
if	($msgError	===	FALSE)	{
// FUSION DES INFOS DE L'ADHERENT.
// Initialisation
			$idAdhKeep	=	$row_keep->ADH_id;
			$identAdhKeep	=	$row_keep->ADH_identifiant;
			$idAdhMerge	=	$row_merge->ADH_id;
			$idCommentaireMerge	=	$row_merge->FK_CMTADH_id;

// Fusion des amis vers l'adhérent Keep
			$query1	=	"UPDATE t_amis_amis SET FK_ADHAMI_id = '$idAdhKeep' WHERE FK_ADHAMI_id = '$idAdhMerge'";
			mysql_query($query1,	$connexion)	or	die(mysql_error());
			$query2	=	"UPDATE t_amis_amis SET FK_ADH_id = '$idAdhKeep' WHERE FK_ADH_id = '$idAdhMerge'";
			mysql_query($query2,	$connexion)	or	die(mysql_error());

// Fusion des commandes et des achats vers l'adhérent Keep
			$query4A	=	"UPDATE t_commande_cmd SET FK_ADH_id = '$idAdhKeep' WHERE FK_ADH_id = '$idAdhMerge'";
			mysql_query($query4A,	$connexion)	or	die(mysql_error());
			$query4B	=	"UPDATE t_achat_ach SET FK_ADH_id = '$idAdhKeep' WHERE FK_ADH_id = '$idAdhMerge'";
			mysql_query($query4B,	$connexion)	or	die(mysql_error());

// Fusion des compétences vers l'adhérent Keep
// Gère le cas ou il existe la même compétence chez les 2 adhérents par IGNORE + DELETE
			$query5A	=	"UPDATE IGNORE tj_adh_cmpt SET TJ_ADH_id = '$idAdhKeep' WHERE TJ_ADH_id = '$idAdhMerge'";
			mysql_query($query5A,	$connexion)	or	die(mysql_error());
			$query5B	=	"DELETE FROM tj_adh_cmpt WHERE TJ_ADH_id = '$idAdhMerge'";
			mysql_query($query5B,	$connexion)	or	die(mysql_error());

// Ajout de la newsletter si vide pour l'adhérent Keep
			if	(is_null($row_keep->NEWS_email)	&&	!is_null($row_merge->NEWS_email))	{
						$query7	=	"INSERT INTO t_newsletter_news (NEWS_email,NEWS_langue) VALUES('$row_keep->ADH_email','$row_keep->ADH_langue');";
						mysql_query($query7,	$connexion)	or	die(mysql_error());
			}

// Fusion des infos de l'adhérent Merge vers l'adhérent Keep
			$newInfoAdherent	=	array();
			$newInfoAdherent['FK_EADH_id']	=	6;	// état "fusion"
			if	($row_keep->ADH_prive	==	"0"	&&	$row_merge->ADH_prive	==	"1")	{	$newInfoAdherent['ADH_prive']	=	1;	}
			if	($row_keep->ADH_annee_naissance	==	""	&&	$row_merge->ADH_annee_naissance	!=	"")	{	$newInfoAdherent['ADH_annee_naissance']	=	$row_merge->ADH_annee_naissance;	}
			if	($row_keep->ADH_ordination	==	"0"	&&	$row_merge->ADH_ordination	==	"1")	{	$newInfoAdherent['ADH_ordination']	=	1;	}
			if	($row_keep->ADH_nom_dharma	==	""	&&	$row_merge->ADH_nom_dharma	!=	"")	{	$newInfoAdherent['ADH_nom_dharma']	=	$row_merge->ADH_nom_dharma;	}
			if	($row_keep->ADH_adresse1	==	""	&&	$row_merge->ADH_adresse1	!=	"")	{
						$newInfoAdherent['ADH_adresse1']	=	$row_merge->ADH_adresse1;
						$newInfoAdherent['ADH_adresse2']	=	$row_merge->ADH_adresse2;
						$newInfoAdherent['ADH_zip']	=	$row_merge->ADH_zip;
						$newInfoAdherent['ADH_ville']	=	$row_merge->ADH_ville;
						$newInfoAdherent['FK_PAYS_id']	=	$row_merge->FK_PAYS_id;
			}
			if	($row_keep->ADH_telephone	==	""	&&	$row_merge->ADH_telephone	!=	"")	{	$newInfoAdherent['ADH_telephone']	=	$row_merge->ADH_telephone;	}
			if	($row_keep->ADH_portable	==	""	&&	$row_merge->ADH_portable	!=	"")	{	$newInfoAdherent['ADH_portable']	=	$row_merge->ADH_portable;	}
			if	($row_keep->ADH_annee_cotisation	==	""	&&	$row_merge->ADH_annee_cotisation	!=	"")	{	$newInfoAdherent['ADH_annee_cotisation']	=	$row_merge->ADH_annee_cotisation;	}
			if	($row_keep->ADH_profession	==	""	&&	$row_merge->ADH_profession	!=	"")	{	$newInfoAdherent['ADH_profession']	=	$row_merge->ADH_profession;	}
			if	($row_keep->ADH_disponibilite	==	""	&&	$row_merge->ADH_disponibilite	!=	"")	{	$newInfoAdherent['ADH_disponibilite']	=	$row_merge->ADH_disponibilite;	}
			if	($row_keep->ADH_benevolat	==	"0"	&&	$row_merge->ADH_benevolat	==	"1")	{	$newInfoAdherent['ADH_benevolat']	=	1;	}

			$str_update	=	"";
			foreach	($newInfoAdherent	as	$key	=>	$value)	{
						if	($value	===	"")	{
									$str_update	.=	$key	.	"=NULL,";
						}	else	{
									$str_update	.=	$key	.	"='"	.	$value	.	"',";
						}
			}
			$str_update	=	rtrim($str_update,	",");
			$query7	=	"UPDATE t_adherent_adh SET $str_update WHERE ADH_id ='$idAdhKeep'";
			mysql_query($query7,	$connexion)	or	die(mysql_error());

// Suppression de l'adhérent Merge
			$query8	=	"DELETE FROM t_adherent_adh WHERE ADH_id = '$idAdhMerge'";
			mysql_query($query8,	$connexion)	or	die(mysql_error());

// Suppression des commentaires de l'adhérent Merge (après la suppression de l'adhérent - query 8)
			$query9	=	"DELETE FROM t_commentaire_cmt WHERE CMT_id = '$idCommentaireMerge'";
			mysql_query($query9,	$connexion)	or	die(mysql_error());
}

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
			<head>
						<title><?php	echo	$titre	?></title>
						<meta NAME="author" CONTENT="www.atelierdu.net" />
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<link rel="icon" type="image/png" href="../_media/favicon.png" />
						<!-- JS -->
						<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.core.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.position.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.widget.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.dialog.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.autocomplete.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/external/jquery.cookie.js"></script>
						<script type="text/javascript" src="../../librairie/js/code_adn/ns_util.js"></script>
						<script type="text/javascript" src="../_fonction/ns_back.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript" src="../_aide.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												NS_BACK.showInfoAdherent('<?php	echo	$identAdhKeep;	?>', "keep");
									});
						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
						<style type="text/css">
									.table_merge {width: 50%}
						</style>
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<?php	if	($msgError)	{	?>
												<div class="ui-state-error ui-corner-all uiphil-msg">
															<p><?php	echo	$msgContent	?></p>
															<p class="SubmitMsg"><a href="compare.php">Merci de réessayer</a></p>
												</div>
									<?php	}	else	{	?>
												<table class="table_merge">
															<tr>
																		<td>
																					<h2>Adhérent fusionné</h2>
																					<div>L'adhérent fusionné a gardé l'identifiant <b><?php	echo	$identAdhKeep;	?></b> et réunit les informations suivantes</div>
																		</td>
															</tr>
															<tr>
																		<td>
																					<div id="info_keep">
																								<?php	include("_fiche.php");	?>
																					</div>
																		</td>
															</tr>
												</table>
												<div><a href="compare.php">retour</a></div>
												<div id="help_icon" class="bt_help pointer"><img src="../_media/bo_help_32.png"/></div>
												<div id="help" title='<img src="../_media/bo_help_32.png" align="absmiddle"/> aide'>
															<p>Commande :  <span class="note">Ne sont pris en compte ici que les commandes directes créés par l'adhérent depuis son espace privé. <br />Les commandes où l'adhérent pourrait apparaitre comme ami ne sont pas décomptées.</span></p>
												</div>
									<?php	}	?>
						</div>
						<?php	include("../_footer.php");	?>
			</body>
</html>
