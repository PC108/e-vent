<?php

// Cette fonction est utilisée dans login.php et  inscription_action.php,
// lorsque l'adhérent finalise le processus d'inscription et est connecté automatiquement au site.
function	initSessionAdh($row)	{

		$_SESSION['info_adherent']['ref_adh']	=	$row->ADH_identifiant;
		$_SESSION['info_adherent']['id_adh']	=	$row->ADH_id;
		$_SESSION['info_adherent']['nom_adh']	=	$row->ADH_nom;
		$_SESSION['info_adherent']['prenom_adh']	=	$row->ADH_prenom;
		$_SESSION['info_adherent']['email_adh']	=	$row->ADH_email;
		$_SESSION['info_adherent']['etat_adh']	=	$row->FK_EADH_id;
		$_SESSION['info_adherent']['prive_ok']	=	$row->ADH_prive;
		$_SESSION['lang']	=	strtolower($row->ADH_langue);

		if	(is_null($row->ADH_annee_cotisation))	{
				$_SESSION['info_adherent']['cotisation_adh']	=	0;
		}	else	{
				$_SESSION['info_adherent']['cotisation_adh']	=	$row->ADH_annee_cotisation;
		}
}

// Cette fonction est utilisée lorsque l'adhérent s'identifie (login.php) et lorsqu'on modifie le bénéficiaire (_amis.php)
function	initSessionBenef($row)	{

		$_SESSION['info_beneficiaire']["id_benef"]	=	$row->ADH_id;
		$_SESSION['info_beneficiaire']["nom_benef"]	=	$row->ADH_nom;
		$_SESSION['info_beneficiaire']["prenom_benef"]	=	$row->ADH_prenom;
		$_SESSION['info_beneficiaire']['ratio_tarif']	=	$row->TYTAR_ratio;

		if	(is_null($row->ADH_annee_cotisation))	{
				$_SESSION['info_beneficiaire']['cotisation_benef']	=	0;
		}	else	{
				$_SESSION['info_beneficiaire']['cotisation_benef']	=	$row->ADH_annee_cotisation;
		}
}

// Cette fonction est active dans toutes les pages. 
// Elles est utilisée dans cotisation/_add.php, cotisation/add.php et adherent/add.php 
function	checkCotisation()	{

		if	($_SESSION['info_adherent']['cotisation_adh']	>=	date("Y"))	{
				$fondCotisation	=	"#b6ee30";
				$iconeCotisation	=	"../_media/GEN/cotisation_ok.png";
				$msgCotisation	=	_("Votre cotisation pour l'année en cours est à jour.");
		}	else	{
				$fondCotisation	=	"#f3f0ef";
				$iconeCotisation	=	"../_media/GEN/cotisation_bad.png";
				$msgCotisation	=	_("Votre cotisation pour l'année en cours est en attente de réception.");
		}

		return	array($fondCotisation,	$iconeCotisation,	$msgCotisation);
}

?>
