<?php

//DEBUGGAGE
// header("Content-Type: text/html; charset=utf-8");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");

/*	* ******************************************************** */
/*              Connexion DB  + autres            */
/*	* ******************************************************** */
include('../../F/_fonction/_shared_ajax.php');
include('../adherent/_requete.php');

/*	* ******************************************************** */
/*              Action                    */
/*	* ******************************************************** */
// $_POST['id_adh']	=	"RC_1_DP";
// $_POST['id_adh']	=	"KC_771_DP";

if	(isset($_POST['id_adh']))	{
			$idAdh	=	$_POST['id_adh'];

// création de la requête
			$query_RS	=	mainQueryAdherent();
			$query_RS	.=	" WHERE ADH_identifiant = '$idAdh'";

			$RS	=	mysql_query($query_RS,	$connexion)	or	die(mysql_error());

			if	(mysql_num_rows($RS)	==	0)	{
						echo	('no result !');
			}	else	{
						$row	=	mysql_fetch_object($RS);

						/* Construit la réponse */
						$result	=	array();
						/* Inscription */
						$result["etat"]	=	$row->EADH_nom;
						$result["etat_couleur"]	=	$row->EADH_couleur;
						$result["cotisation"]	=	strval($row->ADH_annee_cotisation);
						$result["commande"]	=	$row->NbrCommande;
						if	($row->ADH_prive	==	1)	{
									$result["prive"]	=	'<img src="../_media/bo_locked.png" width="16" height="16" border="0"/>';
						}	else	{
									$result["prive"]	=	'<img src="../_media/bo_participe_not.png" width="16" height="16" border="0"/>';
						}
						$result["tarif"]	=	$row->TYTAR_nom_fr	.	' ('	.	$row->TYTAR_ratio	.	'%)';
						/* Info personnelles */
						switch	($row->ADH_genre)	{
									case	"H":
												$str	=	"Mr	"	.	$row->ADH_prenom	.	"	"	.	$row->ADH_nom;
												break;
									case	"F":
												$str	=	"Mme	"	.	$row->ADH_prenom	.	"	"	.	$row->ADH_nom;
												break;
						}
						$result["nom"]	=	$str;
						$result["password"]	=	$row->ADH_password;
						$result["naissance"]	=	strval($row->ADH_annee_naissance);
						/* Communication */
						$result["email"]	=	$row->ADH_email;
						if	(!is_null($row->NEWS_email))	{
									$result["newsletter"]	=	'<img src="../_media/bo_participe.png" width="16" height="16" border="0"/>';
						}	else	{
									$result["newsletter"]	=	'<img src="../_media/bo_participe_not.png" width="16" height="16" border="0"/>';
						}
						$result["tel"]	=	strval($row->ADH_telephone)	.	"	|	"	.	strval($row->ADH_portable);
						$result["langue"]	=	$row->ADH_langue;
						/* Adresse */
						$htmlAdresse	=	$row->ADH_adresse1	.	"<br/>";
						if	(!is_null($row->ADH_adresse2))	{	$htmlAdresse	.=	$row->ADH_adresse2	.	"<br/>";	}
						$htmlAdresse	.=	$row->ADH_zip	.	" "	.	$row->ADH_ville	.	"<br/>";
						$htmlAdresse	.=	$row->PAYS_nom_fr;
						$result["adresse"]	=	$htmlAdresse;
						/* Sangha */
						if	($row->ADH_ordination	==	1)	{
									$result["ordination"]	=	'<img src="../_media/bo_participe.png" width="16" height="16" border="0"/>';
						}	else	{
									$result["ordination"]	=	'<img src="../_media/bo_participe_not.png" width="16" height="16" border="0"/>';
						}
						$result["dharma"]	=	strval($row->ADH_nom_dharma);
						/* Benevolat */
						if	($row->ADH_benevolat	==	1)	{
									$result["benevolat"]	=	'<img src="../_media/bo_participe.png" width="16" height="16" border="0"/>';
						}	else	{
									$result["benevolat"]	=	'<img src="../_media/bo_participe_not.png" width="16" height="16" border="0"/>';
						}
						$result["profession"]	=	strval($row->ADH_profession);
						$result["disponibilite"]	=	strval($row->ADH_disponibilite);
						/* Competence */
						$query_RS2	=	getCompetenceAdherents($row->ADH_id);
						$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());
						if	(mysql_num_rows($RS2)	>	0)	{
									$htmlComp	=	"<ul>";
									while	($row2	=	mysql_fetch_object($RS2))	{
												$htmlComp	.=	"<li>"	.	$row2->CMPT_nom_fr	.	"</li>";
									}
									$htmlComp	.=	"</ul>";
									$result["competence"]	=	$htmlComp;
						}	else	{
									$result["competence"]	=	"";
						}
						// echo	var_dump($result);
						echo	json_encode($result);
			}
}	else	{
			echo	('bad param !');
}
?>