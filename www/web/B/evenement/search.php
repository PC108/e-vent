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
$tableId	=	'EVEN';
$sessionFiltre	=	'FiltreEVEN';

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
$infoFiltre	=	adn_afficheNbreFiltres($sessionFiltre,	array('check_withcmt',	'select_tri',	'submit',	'chemin_retour',	'fonction'));

$RS3	=	mysql_query(listeLieuEvent(),	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*       Titre               */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L1']['nom']	.	"  - Rechercher un enregistrement";

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
						<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript" src="../_aide.js"></script>
						<script type="text/javascript">
									$(document).ready(function () {

												$('input:text').click(function () {
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
															<h2>Informations publiques <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></h2>
															<div class="form_hr">
																		<div class="label_form label">Et le nom en français a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_nom_fr",	$sessionFiltre)	?>">
																					<input name="EVEN_nom_fr" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_nom_fr',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le nom en anglais a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_nom_en",	$sessionFiltre)	?>">
																					<input name="EVEN_nom_en" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_nom_en',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et est privé <img src="../_media/bo_locked.png" width="16" height="16" border="0"alt=""/></div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_prive",	$sessionFiltre)	?>">
																					<input type="radio" name="EVEN_prive" value="Tous" checked> Tous
																					<input type="radio" name="EVEN_prive" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_prive',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="EVEN_prive" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_prive',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et est plein tarif </div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_pleintarif",	$sessionFiltre)	?>">
																					<input type="radio" name="EVEN_pleintarif" value="Tous" checked> Tous
																					<input type="radio" name="EVEN_pleintarif" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_pleintarif',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="EVEN_pleintarif" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_pleintarif',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et la description française a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_descriptif_fr",	$sessionFiltre)	?>">
																					<input name="EVEN_descriptif_fr" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_descriptif_fr',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et la description anglaise a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_descriptif_en",	$sessionFiltre)	?>">
																					<input name="EVEN_descriptif_en" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_descriptif_en',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et l'image = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_image",	$sessionFiltre)	?>">
																					<input type="radio" name="EVEN_image" value="Tous" checked> Tous
																					<input type="radio" name="EVEN_image" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_image',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="EVEN_image" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_image',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le lien = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("EVEN_lien",	$sessionFiltre)	?>">
																					<input type="radio" name="EVEN_lien" value="Tous" checked> Tous
																					<input type="radio" name="EVEN_lien" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_lien',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="EVEN_lien" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'EVEN_lien',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le lieu d'un jour = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_LEVEN_id",	$sessionFiltre)	?>">
																					<select name="FK_LEVEN_id">
																								<option value="Tous" selected>Tous</option>
																								<?php	while	($row_RS3	=	mysql_fetch_assoc($RS3))	{	?>
																											<option value="<?php	echo	$row_RS3['LEVEN_id']	?>"<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_LEVEN_id',	$row_RS3['LEVEN_id'],	'LISTE')	?>><?php	echo	$row_RS3['LEVEN_nom'];	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<h2>Autres</h2>
															<div class="form_hr">
																		<div class="label_form label">Et posséde un commentaire</div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_CMTEVEN_id",	$sessionFiltre)	?>">
																					<input type="radio" name="FK_CMTEVEN_id" value="Tous" checked> Tous
																					<input type="radio" name="FK_CMTEVEN_id" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_CMTEVEN_id',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="FK_CMTEVEN_id" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_CMTEVEN_id',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="content_form">Trier par :
																					<select name="select_tri">
																								<option value="minDate DESC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"minDate DESC",	'LISTE')	?>>Date de début décroissant</option>
																								<option value="minDate ASC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"minDate ASC",	'LISTE')	?>>Date de début croissant</option>
																								<option value="EVEN_id DESC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"EVEN_id DESC",	'LISTE')	?>>Date de création</option>
																					</select>
																		</div>
															</div>
															<div class="form_submit">
																		<input name="fonction" type="hidden" value="adn_creerFiltre" />
																		<input name="chemin_retour" type="hidden" value="search.php" />
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
