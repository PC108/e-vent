<?php

/**
	* Fonction retournant l'état d'un événement en fonction de la class CSS correspondante
	*
	* @author Atelier Du Net
	* @version 03 octobre 2011
	*/
function	setEtatEvent($nbreEnVente,	$nbreDesactive,	$nbreAnnule)	{

		$totalJours	=	$nbreEnVente	+	$nbreDesactive	+	$nbreAnnule;

		if	($nbreEnVente	==	$totalJours)	{
				return	"prodok";
		}	else	if	($nbreDesactive	+	$nbreAnnule	==	$totalJours)	{
				return	"prodbad";
		}	else	{
				return	"prodalert";
		}
}

/**
	* Fonction retournant le modèle économique de l'événement. (Ne prends en compte que les jours en ligne)
	* "inconnu" si aucun jours n'a été créé ou si il n'y a aucun jours en ligne
	* "gratuit" si tous les jours sont gratuits (montant = 0)
	* "payant" si tous les jours sont payants
	* "mixte" si il y a des jours payants et d'autres gratuits
	* @author Atelier Du Net
	* @version 27 mai 2011
	*
	* @param <int> $id = identifiant du jour d'événement en modification
	* @param <resource> $connexion = identifiant de connexion MySQL
	* @return string = gratuit, payant ou mixte
	*/

//function	setBiz($id,	$connexion)	{
//
//		$query	=	"SELECT JREVEN_montant FROM t_jourevent_jreven WHERE FK_EJREVEN_id NOT IN ('1', '4') AND FK_EVEN_id=$id";
//		$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
//		$NbrRS	=	mysql_num_rows($RS);
//
//		if	($NbrRS	==	0)	{
//				$result	=	"inconnu";
//		}	else	{
//				$somme	=	0;
//				$checkJrGratuit	=	FALSE;
//				while	($row	=	mysql_fetch_object($RS))	{
//						if	($row->JREVEN_montant	==	0)	{	$checkJrGratuit	=	TRUE;	}
//						$somme	+=	$row->JREVEN_montant;
//				}
//
//				if	($somme	==	0)	{
//						$result	=	"gratuit";
//				}	else	{
//						if	($checkJrGratuit)	{
//								$result	=	"mixte";
//						}	else	{
//								$result	=	"payant";
//						}
//				}
//		}
//		return	$result;
//}
?>