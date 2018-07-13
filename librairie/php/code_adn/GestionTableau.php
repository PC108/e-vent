<?php
/**
 * Pagination a partir d'une requete donnée et d'un nombre de ligne par page.
 *
 * @version 9 mars 2011
 *
 * @param <int> $maxRows = Nombre de lignes par pages.
 * @param <query> $query_RS = Requête de base sur laquelle on applique la limitation
 * 
 * @return <string> $queryString = Le nombre de résultat total trouvé par la requete, utilisée dans le GET
 * @return <query> $query_RS_Limit = Requete finale avec les limites
 * @return <int> $totalRows = Nombre de lignes totales
 * @return <url> $currentUrl = Url courante de la page
 * @return <int> $pageNum = Numéro de la page courante
 * @return <int> $totalPages = Nombre totale de pages pour la table
 * @return <int> $startRow = Numéro d'enregistrement de départ sur la page
 */
function adn_limiteAffichage($maxRows, $query_RS) {
    //SCRIPT DE LIMITATION D'AFFICHAGE AVEC NAVIGATION PAR BOUTONS

    $currentUrl = $_SERVER["PHP_SELF"];
    $maxRows = intval($maxRows);

    $pageNum = 0;

    if (isset($_GET['pageNum'])) {
	$pageNum = $_GET['pageNum'];
    }
    $startRow = $pageNum * $maxRows;
    // nombre total d'enregistrements
    if (isset($_GET['totalRows'])) {
	$totalRows = $_GET['totalRows'];
    } else {
	$all_Recordset1 = mysql_query($query_RS);
	$totalRows = mysql_num_rows($all_Recordset1);
    }
    // nombre de pages au total
    $totalPages = ceil($totalRows / $maxRows) - 1;
    // Construction de la requte avec la limite
    $query_RS_Limit = sprintf("%s LIMIT %d, %d", $query_RS, $startRow, $maxRows);
    // Gestion des pages
    $queryString = "";
    if (!empty($_SERVER['QUERY_STRING'])) {
	$params = explode("&", $_SERVER['QUERY_STRING']);
	$newParams = array();
	foreach ($params as $param) {
	    if (stristr($param, "pageNum") == false &&
		    stristr($param, "totalRows") == false) {
		array_push($newParams, $param);
	    }
	}
	if (count($newParams) != 0) {
	    $queryString = "&" . htmlentities(implode("&", $newParams));
	}
    }
    //$queryString = sprintf("&totalRows=%d%s", $totalRows, $queryString);
    $queryString = sprintf("&totalRows=%d", $totalRows);

    // A utiliser en sortie de fonction
    // $RS = mysql_query($query_RS_Limit, $connexion) or die(mysql_error());

    return array($queryString,
	$query_RS_Limit,
	$totalRows,
	$currentUrl,
	$pageNum,
	$totalPages,
	$startRow);
}

/**
 * Fonction permettant d'afficher la pagination dans une page html.
 *
 * @version 10-mars-2011
 *
 * @param <string> $currentUrl
 * @param <string> $queryString
 * @param <int> $pageNum
 * @param <int> $totalPages
 * @param <int> $startRow
 * @param <int> $maxRows
 * @param <int> $totalRows
 * @return string = chaîne contenant l'intégralité de l'affichage de la pagination
 */
function adn_navigationTableau($currentUrl, $queryString, $pageNum, $totalPages, $startRow, $maxRows, $totalRows, $changeNbreLignes) {
    $boxNavigate = '<a href="';
    // Lien vers la première page
    $boxNavigate .= sprintf("%s?pageNum=%d%s", $currentUrl, 0, $queryString);
    // Affichage du bouton première page
    $boxNavigate .= '"><img src="../_media/bo_first.png" width="16" height="16" border="0" align="absmiddle" alt="first"></a><a href="';
    // Lien vers la page précédente
    $boxNavigate .= sprintf("%s?pageNum=%d%s", $currentUrl, max(0, $pageNum - 1), $queryString);
    // Affichage du bouton page précédente
    $boxNavigate .= '"><img src="../_media/bo_previous.png" width="16" height="16" border="0" align="absmiddle" alt="previous"></a> Page <b>';
    // Affichage des informations sur les pages
    $boxNavigate .= $pageNum + 1;
    $boxNavigate .= '</b> sur <b>';
    $boxNavigate .= $totalPages + 1;
    $boxNavigate .= ' </b><a href="';
    // Lien vers la page suivante
    $boxNavigate .= sprintf("%s?pageNum=%d%s", $currentUrl, min($totalPages, $pageNum + 1), $queryString);
    // Affichage du bouton page suivante
    $boxNavigate .= '"><img src="../_media/bo_next.png" width="16" height="16" border="0" align="absmiddle" alt="next"></a><a href="';
    // Lien vers la dernière page
    $boxNavigate .= sprintf("%s?pageNum=%d%s", $currentUrl, $totalPages, $queryString);
    // Affichage du bouton dernière page
    $boxNavigate .= '"><img src="../_media/bo_last.png" width="16" height="16" border="0" align="absmiddle" alt="last"></a> | Réponse(s) <b>';
    // Affichage des informations sur les réponses
    $boxNavigate .= $startRow + 1;
    $boxNavigate .= '</b> à <b>';
    $boxNavigate .= min($startRow + $maxRows, $totalRows);
    $boxNavigate .= '</b> sur <b>';
    $boxNavigate .= $totalRows;
    $boxNavigate .= '</b>';
    if ($changeNbreLignes) {
	$boxNavigate .= ' | Afficher ';
	$boxNavigate .= '<select id="nbre_ligne">';
	$boxNavigate .= '<option value="1">1</option>';
	$boxNavigate .= '<option value="5">5</option>';
	$boxNavigate .= '<option value="10">10</option>';
	$boxNavigate .= '<option value="15">15</option>';
	$boxNavigate .= '<option value="20">20</option>';
	$boxNavigate .= '<option value="25">25</option>';
	$boxNavigate .= '<option value="30">30</option>';
	$boxNavigate .= '<option value="40">40</option>';
	$boxNavigate .= '<option value="50">50</option>';
	$boxNavigate .= '<option value="75">75</option>';
	$boxNavigate .= '<option value="100">100</option>';
	$boxNavigate .= '</select>';
	$boxNavigate .= ' lignes |';
	// Ajoute le selected
	$str = 'value="'.$maxRows;
	$positionInsert =  strpos($boxNavigate, $str) + strlen ($str ) + 1;
	$boxNavigate = substr_replace($boxNavigate, ' selected="selected"', $positionInsert, 0);

    };
    return $boxNavigate;
}
?>