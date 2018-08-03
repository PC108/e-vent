<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');
require_once('_requete.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'PAY';
$sessionFiltre	=	'FiltrePAY';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'cmd')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Gestion du numéro de page                  */
/*	* ******************************************************** */
// Conservation du numéro de page (pour les retours à la page précédente)
if	(isSet($_GET['pageNum']))	{
			$pageNum	=	$_GET['pageNum'];
}	else	{
			$pageNum	=	0;
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

// Gestion des filtres. Attention de respecter l'ordre des fonctions.
adn_checkEffaceFiltres($sessionFiltre);
$infoFiltre	=	adn_afficheNbreFiltres($sessionFiltre,	array('fonction',	'submit'));

$query_RS1	=	queryGetStatus();
$RS1	=	mysql_query($query_RS1,	$connexion)	or	die(mysql_error());


/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L2']['nom']	.	"  - Rechercher un enregistrement";

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
			<head>
						<title><?php	echo	$titre	?></title>
						<meta NAME="author" CONTENT="www.atelierdu.net" />
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<link rel="icon" type="image/png" href="../_media/favicon.png" />
						<!-- JS -->
						<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.core.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.position.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.widget.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.dialog.min.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript" src="../_aide.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												$('input:text').click(function(){
															if ($(this).attr('value') == "Tous") {
																		$(this).val("");
															}
												});

									});
						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css">
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<ul class="menu_gauche">
												<li class="boutons"><a href="?clean=1">Annuler les filtres actifs (<?php	echo	$infoFiltre;	?>) <img src="../_media/bo_nofiltre.png" width="16" height="16" border="0" align="absmiddle" alt="annuler les filtres"/></a></li>
												<li class="boutons"><a href="result.php?pageNum=<?php	echo	$pageNum	?>">Retour à la liste <img src="../_media/bo_suivant.png" width="16" height="16" border="0" align="absmiddle" alt="retour"/></a></li>
									</ul>
									<form name="form1" method="post" action="result.php">
												<div class="BoxSearch">
															<h2>Retour Paypal <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></h2>
															<div class="form_hr">
																		<div class="label_form label">Le référence de la commande a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("CMD_ref",	$sessionFiltre)	?>">
																					<input name="CMD_ref" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'CMD_ref',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le montant = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("PAY_montant",	$sessionFiltre)	?>">
																					<input name="PAY_montant" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'PAY_montant',	'Tous',	'CHAMP')	?>" size="10">
																					<div class="note">Pour une recherche exacte, saisir le montant avec des décimales.
																								<br />Par exemple, pour trouver 22 €, saisissez 22.00</div>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le statut = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("PAY_transaction_status",	$sessionFiltre)	?>">
																					<select name="PAY_transaction_status">
																								<option value="Tous" selected>Tous</option>
																								<?php	while	($row_RS1	=	mysql_fetch_assoc($RS1))	{	?>
																											<option value="<?php	echo	$row_RS1['PAY_transaction_status']	?>"<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'PAY_transaction_status',	$row_RS1['PAY_transaction_status'],	'LISTE')	?>><?php	echo	$row_RS1['PAY_transaction_status']	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<div class="form_submit">
																		<input name="fonction" type="hidden" value="adn_creerFiltre" />
																		<input type="submit" name="submit" value="Filtrer" class="submit">
															</div>
												</div>
									</form>
									<div id="help_icon" class="bt_help pointer"><img src="../_media/bo_help_32.png"/></div>
									<div id="help" title='<img src="../_media/bo_help_32.png" align="absmiddle"/> aide'>
												<p class="note">Les champs libres acceptent des nombres ou du texte.</p>
												<p class="note">Si vous saisissez un <b>nombre</b> la recherche se fera sur le nombre exactement. Par exemple la recherche "12" ne renverra que les lignes contenant "12".</p>
												<p class="note">Si vous saisissez un <b>nombre</b> suivi d'un *, la recherche renverra toutes les réponses contenant ce nombre. Par exemple la recherche "12*" renverra  les lignes contenant "124 rue du mirail" ou "34126".</p>
												<p class="note">Si vous saisissez un <b>texte</b> (chaine de caractères), la recherche renverra les lignes qui contiennent au moins ce texte. Par exemple, la recherche "7 rue P" renverra les lignes contenant "197 rue Pierre Lotti" ou "27 rue Pascal".</p>
												<p class="note">Laissez "Tous" si vous ne souhaitez pas utiliser ce critère de recherche. Si aucun critère n'est défini, la recherche renvoie tous les enregistrements de la table.</p>
									</div>
									<?php	include("../_footer.php")	?>
			</body>
</html>

