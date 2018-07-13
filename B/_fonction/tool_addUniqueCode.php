<?php
//DEBUGGAGE
// header("Content-Type: text/html; charset=iso-8859-1");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");

/*	* ******************************************************** */
/*              Connexion DB  + autres            */
/*	* ******************************************************** */
include('../../F/_fonction/_shared_ajax.php');

/*	* ******************************************************** */
/*              Action                    */
/*	* ******************************************************** */
// création de la requête
$query_RS1	=	"SELECT ADH_id FROM t_adherent_adh WHERE ADH_lien IS NULL";
$RS1	=	mysql_query($query_RS1,	$connexion)	or	die(mysql_error());
$nbreUpdate	=	0;

while	($row	=	mysql_fetch_object($RS1))	{

			$code	=	md5(uniqid());
			$id	=	$row->ADH_id;
			$query_RS2	=	"UPDATE t_adherent_adh SET ADH_lien='$code' WHERE ADH_id = '$id'";
			mysql_query($query_RS2,	$connexion)	or	die(mysql_error());
			
			$nbreUpdate ++;
}

echo	"Nombre d'enregistrements modifiés avec succès : "	.	$nbreUpdate;
?>
