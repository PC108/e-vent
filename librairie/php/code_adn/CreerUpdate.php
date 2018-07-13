<?php

/**
	* Crée une requête SQL UPDATE à partir de la variable $_POST
	* Ajoute aussi $AutresData dans l'update si != array();
	* Définir la table à mettre à jour
	* Utiliser le tableau $Exception pour ne pas prendre en compte certain critères recus en POST (par exemple les bouton SUBMIT)
	* Utiliser le tableau $AutresData pour mettre à jour des datas supplémentaires à celles envoyées en POST. Par exemple : $AutresData['dateins_prs'] = date("Y-m-d");
	* Utiliser le tableau $FormatData pour formater les casses d'une donnée. valeurs possible = UPPER, LOWER et LOWERFIRST
	* Utiliser le tableau $CheckArrayVide pour indiquer les champs qui contiennent des tableaux sérialisés et qui pourraient être vides.
	*
	* @example $autresData = array();
	*          $formatData['md_nom_prs'] = 'UPPER';
	*          $formatData['md_adr1_prs'] = 'LOWER';
	*          $formatData['md_adr2_prs'] = 'LOWER';
	*          $formatData['md_ville_prs'] = 'UPPER';
	*          $formatData['nom_prs'] = 'UPPER';
	*          $formatData['prenom_prs'] = 'LOWERFIRST';
	*          $formatData['email_prs'] = 'LOWER';
	*          $checkArrayVide = array('array_type_prs', 'array_participation_prs');
	*
	*          $query = adn_creerUpdate('p6_presse', 'id_prs', $id, array('action', 'id', 'submit'), $autresData, $formatData, $checkArrayVide);
	*
	* @author Atelier Du Net
	* @version 22 aout 2011
	*
	* @param <string> $table = nom de la table à mettre à jour
	* @param <string> $champID = nom du champ identifiant de la table
	* @param <int> $idToUpdate = id de l'enregistrement à mettre à jour
	* @param <array> $exception = tableau des champs du POST à ignorer (comme action, id, submit...)
	* @param <array> $autresData = data supplémentaires à mettre à jour (en dur)
	* @param <array> $formatData = format des datas
	* @param <array> $checkArrayVide = tableau des champs pouvant être vide et contenant des tableaux sérialisés (par ex: des cases à cocher)
	* @return <string> $query = requête finalisée
	*/
function	adn_creerUpdate($table,	$champID,	$idToUpdate,	$exception=array(),	$autresData=array(),	$formatData=array(),	$checkArrayVide=array())	{
		$str_update	=	"";

		// Vérifie si des choix multiples qui arrivent vides
		foreach	($checkArrayVide	as	$value)	{
				if	(!isset($_POST[$value]))	{
						$str_update	.=	$value	.	"='a:0:{}',";
				}
		}

		foreach	($_POST	as	$key	=>	$value)	{
				if	(!in_array($key,	$exception))	{

						// A garder ici pour la fonction trim qui cleane les valeurs avec uniquement des espaces + gestion des array
						if	(is_array($value))	{
								$value	=	serialize($value);
						}	else	{
								$value	=	adn_quote_smart($value);
						}

						// Traitement de la valeur
						if	($value	===	"")	{
								$str_update	.=	$key	.	"=NULL,";
						}	else	{

								if	(array_key_exists($key,	$formatData))	{
										switch	($formatData[$key])	{
												case	'UPPER':
														$value	=	mb_strtoupper($value,	'UTF-8');
														break;
												case	'LOWER':
														$value	=	mb_strtolower($value,	'UTF-8');
														break;
												case	'LOWERFIRST':
														$value	=	ucfirst(mb_strtolower($value,	'UTF-8'));
														break;
												default:
														break;
										}
								}

								$str_update	.=	$key	.	"='"	.	$value	.	"',";
						}
				}
		}

		if	(count($autresData)	>	0)	{
				foreach	($autresData	as	$key	=>	$value)	{
						if	($value	===	"")	{
								$str_update	.=	$key	.	"=NULL,";
						}	else	{
								$str_update	.=	$key	.	"='"	.	adn_quote_smart($value)	.	"',";
						}
				}
		}

		$str_update	=	rtrim($str_update,	",");

		$query	=	"UPDATE $table SET $str_update WHERE $champID='$idToUpdate'";
		return	$query;
}
?>