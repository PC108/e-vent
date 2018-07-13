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
		$query	=	queryOptionHebergement($idJour,	$connexion);
		echo	getHebergement($query,	$idJour,	$langue,	$connexion);
//} else { // Désactivé car se lance lorrsqu'on charge le fichier
//    echo _('bad param!');
}

function	getHebergement($query,	$idJour,	$langue,	$connexion,	$idHeberChecked	=	NULL)	{

		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());

		if	(mysql_num_rows($RS)	==	0)	{
				return	_("Aucune option d'hébergement active pour cette date.");
		}	else	{
				$groupeRadio	=	"tyheb-jreven-"	.	$idJour;
				$html	=	'<ul><li><img class="ic_heber" src="../_media/GEN/hebergement2.png" /></li>';
				$html	.=	'<li><input type="radio" class="radioHebergement" id-benef="'	.	$_SESSION['info_beneficiaire']['id_benef']	.	'" id-jreven="'	.	$idJour	.	'" name="'	.	$groupeRadio	.	'" value ="aucun" checked="checked">'	.	_("Aucun choix")	.	'</li>';
				while	($row	=	mysql_fetch_array($RS))	{
						$montant	=	$row['TYHEB_JREVEN_montant'];
						// Si l'hebergement n'est pas complet, affiche la case à cocher
						if	($row['nbreAchats']	<	$row['TYHEB_JREVEN_capacite']	||	$row['TYHEB_JREVEN_capacite']	==	0)	{
								// Si il y a un achat correspondant, coche la case
								if	((!is_null($idHeberChecked))	&&	($row['TJ_TYHEB_id']	==	$idHeberChecked))	{
										$checked	=	'" checked="checked" />';
								}	else	{
										$checked	=	'" />';
								}
								$html	.=	'<li><input type="radio" class="radioHebergement" name="'	.	$groupeRadio	.	'" id-benef="'	.	$_SESSION['info_beneficiaire']['id_benef']	.	'" id-jreven="'	.	$idJour	.	'" id-heber="'	.	$row['TJ_TYHEB_id']	.	'" montant="'	.	$montant	.	$checked;
								// Ajout du lien vers la desscription si elle existe
								if	(!is_null($row['TYHEB_description_'	.	$langue]))	{
										$classDuLien	=	'bt_info_resto pointer';
										$iconeDulien	=	'<img src="../_media/GEN/ic_info.png"/>';
								}	else	{
										$classDuLien	=	'';
										$iconeDulien	=	'';
								}
								$html	.=	'<span idinfo="heberopt-'	.	$row['TJ_TYHEB_id']	.	'" class="'	.	$classDuLien	.	'">'	.	$row['TYHEB_nom_'	.	$_SESSION['lang']]	.	$iconeDulien	.	'</span>';
								$html	.=	' <span class="prix fd_blanc corner10-all">+'	.	$montant	.	'€</span></li>';
						}	else	{
								$html	.=	'<li><img src="../_media/GEN/checkboxLocked.png" style="margin-left:5px"> '	.	$row['TYHEB_nom_'	.	$_SESSION['lang']];
								$html	.=	' <span class="prix fd_blanc corner10-all">'	.	_('complet')	.	'</span></li>';
						}
				}
				$html	.=	'</ul>';
				return	$html;
		}
}

?>