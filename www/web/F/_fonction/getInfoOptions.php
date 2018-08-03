<?php
// evenement.php
function	getInfoOptions($connexion,	$langue)	{

		// Hébergement
		$query	=	"SELECT * FROM t_typehebergement_tyheb";
		$RS_infoTyHeb	=	mysql_query($query,	$connexion)	or	die(mysql_error());

		while	($row	=	mysql_fetch_array($RS_infoTyHeb))	{
				$infoTypeOptions["heberopt-"	.	$row['TYHEB_id']]	=	array($row['TYHEB_nom_'	.	$langue],	$row['TYHEB_description_'	.	$langue]);
		}

		// Restauration
		$query	=	"SELECT * FROM t_typerestauration_tyres";
		$RS_infoTyRes	=	mysql_query($query,	$connexion)	or	die(mysql_error());

		while	($row	=	mysql_fetch_array($RS_infoTyRes))	{
				$infoTypeOptions["restoopt-"	.	$row['TYRES_id']]	=	array($row['TYRES_nom_'	.	$langue],	$row['TYRES_description_'	.	$langue]);
		}

		return	$infoTypeOptions;
}

?>
