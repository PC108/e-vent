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
$query_RS	=	"UPDATE t_commande_cmd SET FK_ECMD_id=6 WHERE FK_ECMD_id =10";
mysql_query($query_RS,	$connexion)	or	die(mysql_error());

echo	"Nombre d'enregistrements modifiés avec succès : "	.	mysql_affected_rows();
?>
