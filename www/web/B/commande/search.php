<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('_requete.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'CMD';
$sessionFiltre	=	'FiltreCMD';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */

if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'event')))	{
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
$infoFiltre	=	adn_afficheNbreFiltres($sessionFiltre,	array('check_withcmt',	'select_tri',	'submit',	'fonction'));

$query2	=	listeEtatsQuery();
$RS2	=	mysql_query($query2,	$connexion)	or	die(mysql_error());

$query3	=	listeModePayeQuery();
$RS3	=	mysql_query($query3,	$connexion)	or	die(mysql_error());


/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L1']['nom']	.	"  - Rechercher un enregistrement";

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
						<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript" src="../_aide.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {

												$('input:text').click(function(){
															if ($(this).attr('value') == "Tous") {
																		$(this).val("");
															}
												});

												$("#form_event").validate({
															rules: {
																		'search_jour': {
																					required: true
																		}
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
															<h2>Par commande <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></h2>
															<div class="form_hr">
																		<div class="label_form label">Dont la référence a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("CMD_ref",	$sessionFiltre)	?>">
																					<input id="CMD_ref" name="CMD_ref" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'CMD_ref',	'Tous',	'CHAMP')	?>">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">et dont l'état = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_ECMD_id",	$sessionFiltre)	?>">
																					<select name="FK_ECMD_id">
																								<option value="Tous" selected>Tous</option>
																								<?php	while	($row_RS2	=	mysql_fetch_assoc($RS2))	{	?>
																											<option value="<?php	echo	$row_RS2['ECMD_id']	?>"<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_ECMD_id',	$row_RS2['ECMD_id'],	'LISTE')	?>><?php	echo	$row_RS2['ECMD_nom']	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">et dont le mode de paiement = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_MDPAY_id",	$sessionFiltre)	?>">
																					<select name="FK_MDPAY_id">
																								<option value="Tous" selected>Tous</option>
																								<?php	while	($row_RS3	=	mysql_fetch_assoc($RS3))	{	?>
																											<option value="<?php	echo	$row_RS3['MDPAY_id']	?>"<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_MDPAY_id',	$row_RS3['MDPAY_id'],	'LISTE')	?>><?php	echo	$row_RS3['MDPAY_nom_fr']	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">et le total = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("totalCommande",	$sessionFiltre)	?>">
																					<input id="totalCommande" name="totalCommande" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'totalCommande',	'Tous',	'CHAMP')	?>">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">et le remboursement = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("totalRemb",	$sessionFiltre)	?>">
																					<input id="ADH_nom" name="totalRemb" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'totalRemb',	'Tous',	'CHAMP')	?>">
																		</div>
															</div>
															<h2>Par acheteur</h2>
															<div class="form_hr">
																		<div class="label_form label">et l'identifiant a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_identifiant",	$sessionFiltre)	?>">
																					<input id="ADH_identifiant" name="ADH_identifiant" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_identifiant',	'Tous',	'CHAMP')	?>">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">et le nom de l'acheteur a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_nom",	$sessionFiltre)	?>">
																					<input id="ADH_nom" name="ADH_nom" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_nom',	'Tous',	'CHAMP')	?>">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">et le prénom de l'acheteur a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_prenom",	$sessionFiltre)	?>">
																					<input id="ADH_nom" name="ADH_prenom" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_prenom',	'Tous',	'CHAMP')	?>">
																		</div>
															</div>
															<h2>Autres</h2>
															<div class="form_hr">
																		<div class="label_form label">Et posséde un commentaire</div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_CMTCMD_id",	$sessionFiltre)	?>">
																					<input type="radio" name="FK_CMTCMD_id" value="Tous" checked> Tous
																					<input type="radio" name="FK_CMTCMD_id" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_CMTCMD_id',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="FK_CMTCMD_id" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_CMTCMD_id',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="content_form">Trier par :
																					<select name="select_tri">
																								<option value="CMD_id DESC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"CMD_id DESC",	'LISTE')	?>>Date de création</option>
																								<option value="ADH_nom ASC, ADH_prenom ASC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"ADH_nom ASC, ADH_prenom ASC",	'LISTE')	?>>Acheteur</option>
																								<option value="totalCommande DESC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"totalCommande DESC",	'LISTE')	?>>Total</option>
																								<option value="totalRemb DESC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"totalRemb DESC",	'LISTE')	?>>Remboursement</option>
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
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
