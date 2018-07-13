<?php

//simulation du  temps d'attente du serveur (2 secondes)
//sleep(1);
//DEBUGGAGE
// header("Content-Type: text/html; charset=iso-8859-1");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");
ini_set('html_errors',	0);

//Connexion a la DB
if	(isset($_POST['cle'])	&&	isset($_POST['valeur'])	&&	isset($_POST['action']))	{
			$multiSessionName	=	$_POST['multiSessionName'];
			$cle	=	$_POST['cle'];
			$valeur	=	$_POST['valeur'];
			$action	=	$_POST['action'];
}	else	{
			return	(FALSE);
}

session_name($multiSessionName);
session_start();

switch	($action)	{
			// Modif 03/2014
			// Le code ci-dessous est utilisé dans le BO (ns_back.js) uniquement.
			// La valeur manipulée est  dans un array (type clé=array(valeur1, valeur2)
			case	"toggle":	//Supprime la valeur dans l'array si elle existe sinon l'ajoute
						if	(isset($_SESSION[$cle]))	{
									$monArray	=	$_SESSION[$cle];
									if	(!in_array($valeur,	$monArray))	{
												$monArray[]	=	$valeur;
									}	else	{
												unset($monArray[array_search($valeur,	$monArray)]);
									}
						}	else	{
									$monArray[]	=	$valeur;
						}
						$_SESSION[$cle]	=	$monArray;
						break;
			// Modif 03/2014
			// Le code ci-dessous n'est pas utilisé dans le BO, par contre il est utilisé dans le FO (index.js).
			// La valeur manipulée n'est pas dans un array (type clé=array(valeur1, valeur2) mais simplement du type clé=valeur
			case	"replace":	//Créé un tableau vide si existe déjà, sinon remplace
						if	(isset($_SESSION[$cle])	&&	in_array($valeur,	$_SESSION[$cle]))	{
									unset($_SESSION[$cle]);
						}	else	{
									$monArray[]	=	$valeur;
									$_SESSION[$cle]	=	$monArray;
						}
						break;
			// Le code ci-dessous est utilisé dans le BO (ns_back.js) et le FO (evenement.js).
			// La valeur manipulée est  dans un array (type clé=array(valeur1, valeur2)
			case	"add":
						if	(isset($_SESSION[$cle]))	{
									$monArray	=	$_SESSION[$cle];
									if	(!in_array($valeur,	$monArray))	{
												$monArray[]	=	$valeur;
									}
						}	else	{
									$monArray[]	=	$valeur;
						}
						$_SESSION[$cle]	=	$monArray;
						break;
			// Le code ci-dessous est utilisé dans le BO (ns_back.js) et le FO (evenement.js).
			// La valeur manipulée est  dans un array (type clé=array(valeur1, valeur2)
			case	"del";
						if	(isset($_SESSION[$cle]))	{
									$monArray	=	$_SESSION[$cle];
									if	(in_array($valeur,	$monArray))	{
												unset($monArray[array_search($valeur,	$monArray)]);
												$_SESSION[$cle]	=	$monArray;
									}
						}
						break;
}

echo	json_encode($monArray);
?>