<?php

/*
	* Compare 2 dates au format YYYY-MM-DD en fonction du signe passé en string
	* Si la $Date2 = "today", compare avec la date d'aujourd'hui.
	*/

function	compare2dates($Date1,	$comparaison,	$Date2)	{

		// Crée les timestamps
		$TS_Date1	=	creeTimeStamp($Date1);
		if	($Date2 == "today")	{
				$TS_Date2	=	creeTimeStamp(date('Y-m-d'));	// au lieu de time() pour obtenir l'égalité
		}	else	{
				$TS_Date2	=	creeTimeStamp($Date2);
		}

		// Compare les TimeStamps
		switch	($comparaison)	{
				case	"<":
						return	($TS_Date1	<	$TS_Date2);
						break;
				case	"<=":
						return	($TS_Date1	<=	$TS_Date2);
						break;
				case	"=":
						return	($TS_Date1	==	$TS_Date2);
						break;
				case	">=":
						return	($TS_Date1	>=	$TS_Date2);
						break;
				case	">":
						return	($TS_Date1	>	$TS_Date2);
						break;
		}
}

/*
	* Crée un timestamp à partir d'une date YYYY-MM-DD.
	*/

function	creeTimeStamp($Date)	{
		$arrayDate	=	explode("-",	$Date);
		return	mktime(0,	0,	0,	$arrayDate[1],	$arrayDate[2],	$arrayDate[0]);
}

?>
