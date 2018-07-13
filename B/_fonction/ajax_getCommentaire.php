<?php
//DEBUGGAGE
// header("Content-Type: text/html; charset=iso-8859-1");
//PROD
//indique que le genre de la réponse renvoyée au client sera du Texte en UTF8
header("Content-Type: text/plain; charset=utf-8");

/***********************************************************/
/*              Connexion DB  + autres            */
/***********************************************************/
include('../../F/_fonction/_shared_ajax.php');

/***********************************************************/
/*              Action                    */
/***********************************************************/
if (isset($_POST['id_cmt'])) {
    $id = $_POST['id_cmt'];

// création de la requête
    $query_RS = "select CMT_commentaire FROM t_commentaire_cmt WHERE CMT_id = $id";
    $RS = mysql_query($query_RS, $connexion) or die(mysql_error());

    if (mysql_num_rows($RS) == 0) {
	echo ('no result !');
    } else {
	$row = mysql_fetch_assoc($RS);
	echo nl2br($row['CMT_commentaire']);
    }
} else {
    echo ('bad param !');
}
// print_r($rows);
?>