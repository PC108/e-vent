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
require_once('../../B/jourevenement/_requete.php');
// Traduction
require_once('../../librairie/php/code_adn/AfficheSession.php');
require_once('../../localisation/localisation.php');

/*	* ******************************************************** */
/*              Action                    */
/*	* ******************************************************** */
if	(isset($_POST['TJ_JREVEN_id'])	&&	isset($_POST['lg']))	{
		$idJour	=	$_POST['TJ_JREVEN_id'];
		$langue	=	$_POST['lg'];
		$query	=	queryOptionRestauration($idJour,	$connexion);
		echo	getRestauration($query,	$idJour,	$langue,	$connexion);
//} else { // Désactivé car se lance lorrsqu'on charge le fichier
//    echo _('bad param!');
}

function	getRestauration($query,	$idJour,	$langue,	$connexion,	$arrayRestoChecked	=	NULL)	{

		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());

		if	(mysql_num_rows($RS)	==	0)	{
				return	_("Aucune option de restauration active pour cette date.");
		}	else	{
				$html	=	'<ul><li><img class="ic_resto" src="../_media/GEN/restauration2.png" /></li>';
				while	($row	=	mysql_fetch_array($RS))	{
						$montant	=	$row['TYRES_JREVEN_montant'];
						// Si l'la resaturation n'est pas complete, affiche la case à cocher
						if	($row['nbreAchats']	<	$row['TYRES_JREVEN_capacite']	||	$row['TYRES_JREVEN_capacite']	==	0)	{
								// Si il y a un achat correspondant, coche la case
								if	((!is_null($arrayRestoChecked))	&&	(in_array($row['TJ_TYRES_id'],	$arrayRestoChecked)))	{
										$checked	=	'" checked="checked" />';
								}	else	{
										$checked	=	'" />';
								}
								$html	.=	'<li><input type="checkbox" class="checkboxRestauration"  id-benef="'	.	$_SESSION['info_beneficiaire']['id_benef']	.	'" id-jreven="'	.	$idJour	.	'" id-resto="'	.	$row['TJ_TYRES_id']	.	'" value="'	.	$montant	.	$checked;
								// Ajout du lien vers la desscription si elle existe
								if	(!is_null($row['TYRES_description_'	.	$langue]))	{
										$classDuLien	=	'bt_info_resto pointer';
										$iconeDulien	=	'<img src="../_media/GEN/ic_info.png"/>';
								}	else	{
										$classDuLien	=	'';
										$iconeDulien	=	'';
								}
								$html	.=	'<span idinfo="restoopt-'	.	$row['TJ_TYRES_id']	.	'" class="'	.	$classDuLien	.	'">'	.	$row['TYRES_nom_'	.	$_SESSION['lang']]	.	$iconeDulien	.	'</span>';
								$html	.=	' <span class="prix fd_blanc corner10-all">+'	.	$montant	.	'€</span></li>';
						}	else	{
								$html	.=	'<li><img src="../_media/GEN/checkboxLocked.png" style="margin-left:5px"> '	.	$row['TYRES_nom_'	.	$_SESSION['lang']];
								$html	.=	' <span class="prix fd_blanc corner10-all">'	.	_('complet')	.	'</span></li>';
						}
				}
				$html	.=	'</ul>';
				return	$html;
		}
}

?>