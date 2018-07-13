<?php
/* * ******************************************************** */
/*              Inclusions des fichiers                    */
/* * ******************************************************** */
include('../_shared.php');

/* * ******************************************************** */
/*                  Test de connexion                      */
/* * ******************************************************** */
if ((!isset($_SESSION['user_info'])) || !in_array($_SESSION['user_info'][1], array('admin'))) {
    $_SESSION['message_user'] = "acces_ko";
    adn_myRedirection('../login/menu.php');
}

/* * ******************************************************** */
/*             Vérification des variables                  */
/* * ******************************************************** */
if ($_SERVER['SERVER_NAME'] == "localhost") {
    $Msg = "BAD";
} else {
    /* Modifiez vos parametres MySQL */
    $db_server = $hostname;
    $db_name = $database; /* pour acceder a la base vielle de 7 jours, ajoutez -s à la fin du nom, comme ceci: NomDeLaBaseSQL-s */
    $db_username = $database;
    $db_password = $password;
    $db_charset = "utf8"; /* mettre utf8 ou latin1  selon comment ta base est definie */
    /* C'est tout. Placez ce fichier par FTP quelque part sur votre serveur Web, dans un endroit discret. */
    /* Puis ouvrez-le avec votre navigateur web et suivez les instructions. */
    if (system("mysqldump --host=$db_server --user=$db_username --password=$db_password -C -Q -e --default-character-set=$db_charset $db_name | gzip -c > ../../doc/export-e-venement-$db_charset.sql.gz")

	);
}

/* * ******************************************************** */
/*                       Titre                             */
/* * ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['CONFIGURATION']['L5']['nom'];

/* * ******************************************************** */
/*              Début code de la page html                 */
/* * ******************************************************** */
?>
<!DOCTYPE html>
<html>
    <head>
	<title><?php echo $titre ?></title>
	<meta NAME="author" CONTENT="www.atelierdu.net" />
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<link rel="icon" type="image/png" href="../_media/favicon.png" />
	<!-- JS -->
	<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
	<script type="text/javascript" src="../_shared.js"></script>
	<!-- CSS -->
	<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
	<?php include("../_header.php"); ?>
	<div id="contenu">
	    <?php if ($Msg == "BAD") { ?>
    	    <p>Ce script ne fonctionne pas en local mais fonctionne sur le serveur.</p>
	    <?php } else { ?>
    	    <p>Une nouvelle archive de la base de donnée vient d'être générée.</p>
    	    <p>Pour la télécharger, <a href="../../doc/export-e-venement-<?php echo $db_charset ?>.sql.gz">cliquez ici</a></p>
	    <?php } ?>
	</div>
	<?php include("../_footer.php") ?>
    </body>
</html>