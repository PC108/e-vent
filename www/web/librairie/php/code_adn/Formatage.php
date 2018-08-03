<?php

/**
	* Renvoie la date formatée + le classe à afficher si on utilise un icone (voir dans e-venement.com)
	*
	* @example $infoDates = adn_afficheFromDateToDate($row['minDate'], $row['maxDate'], "DB_" . $langue);
	*
	* @version 17/05/2011
	* @author Atelier Du Net
	*
	* @param <date> $date1 = Première date à traiter
	* @param <date> $date2 = deuxième date à traiter
	* @param <string> $Type = langue
	* @return <array> $str du style : du 11/03/2011 au 12/04/2011, $classe pour la classe d'affichage (voir e-venement.com)
	*/
function	adn_afficheFromDateToDate($date1,	$date2,	$Type)	{
		$langue	=	explode("_",	$Type);
		$langue	=	strtolower($langue[1]);
		switch	($langue)	{
				case	("en"):
						$sStr1	=	"No date available";
						$sStr2	=	"on ";
						$sStr3	=	"from ";
						$sStr4	=	" to ";
						break;
				case	("fr"):
						$sStr1	=	"Aucune date disponible";
						$sStr2	=	"le ";
						$sStr3	=	"du ";
						$sStr4	=	" au ";
						break;
		}
		if	(is_null($date1)	||	is_null($date2))	{
				$str	=	$sStr1;
				$classe	=	"dateBAD";
		}	elseif	($date1	==	$date2)	{
				$str	=	$sStr2	.	adn_changeFormatDate($date1,	$Type);
				$classe	=	"dateOK";
		}	else	{
				$str	=	$sStr3	.	adn_changeFormatDate($date1,	$Type)	.	$sStr4	.	adn_changeFormatDate($date2,	$Type);
				$classe	=	"dateOK";
		}
		return	array($str,	$classe);
}

/**
	* Modifie le format d'une date.
	* Sert dans le sens DB > site ou dans le sens Formulaire > DB
	* "FR_DB" -> Transforme une date au format JJ/MM/AAAA en AAAA-MM-JJ
	* "DB_FR" -> Transforme une date au format AAAA-MM-JJ en JJ/MM/AAAA
	* "DB_EN" -> Transforme une date au format AAAA-MM-JJ en MM/JJ/AAAA
	*
	* @version 09/04/2011
	* @author Atelier Du Net
	*
	* @param <date> $date = date à traiter
	* @param <string> $Type = FR_DB, DB_FR, DB_EN
	* @return <date>
	*/
function	adn_changeFormatDate($date,	$Type)	{
		switch	($Type)	{
				case	("FR_DB"):
				case	("fr_DB"):
						list($Jour,	$Mois,	$An)	=	explode('/',	$date);
						return	($An	.	"-"	.	$Mois	.	"-"	.	$Jour);
						break;
				case	("DB_EN"):
				case	("DB_en"):
						list($An,	$Mois,	$Jour)	=	explode('-',	$date);
						return	($Mois	.	"/"	.	$Jour	.	"/"	.	$An);
						break;
				case	("DB_FR"):
				case	("DB_fr"):
						list($An,	$Mois,	$Jour)	=	explode('-',	$date);
						return	($Jour	.	"/"	.	$Mois	.	"/"	.	$An);
						break;
		}
}

/**
	* Modifie le format d'une date time.
	* Sert dans le sens DB > site ou dans le sens Formulaire > DB
	* "DB_FR" -> Transforme une DATETIME au format AAAA-MM-JJ 01:02:03 en "le JJ/MM/AAAA à 01h 02m 03s"
	* "DB_EN" -> Transforme une DATETIME au format AAAA-MM-JJ 01:02:03 en "the MM/JJ/AAAA at 01h 02m 03s"
	*
	* @version 09/04/2011
	* @author Atelier Du Net
	*
	* @param <date> $date = date à traiter
	* @param <string> $Type = DB_FR, DB_EN
	* @return <date>
	*/
function	adn_afficheDateTime($datetime,	$Type)	{
		$tmp	=	explode(" ",	$datetime);
		list($an,	$mois,	$jour)	=	explode('-',	$tmp[0]);
		list($heure,	$minute,	$seconde)	=	explode(':',	$tmp[1]);
		switch	($Type)	{
				case	("DB_EN"):
						return	("the "	.	$mois	.	"/"	.	$jour	.	"/"	.	$an	.	" at "	.	$heure	.	"h "	.	$minute	.	"m "	.	$seconde	.	"s");
						break;
				case	("DB_FR"):
						return	("le "	.	$jour	.	"/"	.	$mois	.	"/"	.	$an	.	" à "	.	$heure	.	"h "	.	$minute	.	"m "	.	$seconde	.	"s");
						break;
		}
}

/**
	* Renvoie un Montant formaté selon la notation du pays
	*
	* @version 09/04/2011
	* @author Atelier Du Net
	*
	* @param <int ou string> $Montant = montant à formater.
	* @param <date> $SepMillier = Séparateur entre les milliers
	* @param <string> $Langue = FR ou EN
	* @return <int> $Montant = montant formaté
	*/
function	adn_formatMontant($Montant,	$SepMillier,	$Langue)	{

		if	(!is_int($Montant))	{
				intval($Montant);
		}

		switch	($Langue)	{
				case	"FR":
						if	($SepMillier):
								$Montant	=	number_format($Montant,	2,	',',	' ');
								break;
						else:
								$Montant	=	number_format($Montant,	2,	',',	'');
								break;
						endif;
						break;
				case	"EN":
						if	($SepMillier):
								$Montant	=	number_format($Montant,	2,	'.',	' ');
								break;
						else:
								$Montant	=	number_format($Montant,	2,	'.',	'');
								break;
						endif;
						break;
		}

		return	$Montant;
}

/**
	* Renvoie un Montant formaté avec 2 décimale : XX.XX
	* Si float = TRUE, renvoie la valeur avec un type float, sinon avec un type string
	*
	* @version 20/09/2011
	* @author Atelier Du Net
	*
	*/
function	adn_enDecimal($montant)	{

		// return	sprintf("%01.2f",	str_replace(",",	".",	$montant));
		// return round(floatval($montant),2);
		//return	 intval($montant);
		// return $montant;
		$valeur	=	str_replace(",",	".",	sprintf("%01.2f",	$montant));
		// $valeur	=	sprintf("%01.2f",	$montant);

		return	$valeur;
}

?>