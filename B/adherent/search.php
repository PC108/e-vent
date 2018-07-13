<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/CreerFiltre.php');

/*	* ******************************************************** */
/*              Configuration                    */
/*	* ******************************************************** */
$tableId	=	'ADH';
$sessionFiltre	=	'FiltreADH';

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */

if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('adher',	'admin')))	{
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

$query_RS1	=	"SELECT * FROM t_typetarif_tytar WHERE TYTAR_visible=1 ORDER BY TYTAR_ordre ASC";
$RS1	=	mysql_query($query_RS1,	$connexion)	or	die(mysql_error());

$query_RS2	=	"SELECT * FROM t_pays_pays ORDER BY PAYS_tri_fr ASC";
$RS2	=	mysql_query($query_RS2,	$connexion)	or	die(mysql_error());

$query_RS3	=	"SELECT * FROM t_etatadherent_eadh ORDER BY EADH_ordre ASC";
$RS3	=	mysql_query($query_RS3,	$connexion)	or	die(mysql_error());

// $query_RS4	=	"SELECT * FROM t_competence_cmpt WHERE CMPT_visible=1 ORDER BY CMPT_nom_fr ASC"; // modifié le 13/03/2012
$query_RS4	=	"SELECT * FROM t_competence_cmpt ORDER BY CMPT_nom_fr ASC";
$RS4	=	mysql_query($query_RS4,	$connexion)	or	die(mysql_error());
$nbr_competence	=	mysql_num_rows($RS4);

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['ADHERENTS']['L1']['nom']	.	"  - Rechercher un enregistrement";

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
															<h2>Inscription <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></h2>
															<div class="form_hr">
																		<div class="label_form label">Dont l'état = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_EADH_id",	$sessionFiltre)	?>">
																					<select name="FK_EADH_id">
																								<option value="Tous" selected>Tous</option>
																								<?php	while	($row_RS3	=	mysql_fetch_assoc($RS3))	{	?>
																											<option value="<?php	echo	$row_RS3['EADH_id']	?>"<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_EADH_id',	$row_RS3['EADH_id'],	'LISTE')	?>><?php	echo	$row_RS3['EADH_nom']	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et l'année de cotisation = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_annee_cotisation",	$sessionFiltre)	?>">
																					<input name="ADH_annee_cotisation" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_annee_cotisation',	'Tous',	'CHAMP')	?>" size="10">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et accède aux évé. privés <img src="../_media/bo_locked.png" width="16" height="16" border="0"alt=""/><img src="../_media/bo_participe.png" width="16" height="16" border="0"alt=""/></div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_prive",	$sessionFiltre)	?>">
																					<input type="radio" name="ADH_prive" value="Tous" checked> Tous
																					<input type="radio" name="ADH_prive" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_prive',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="ADH_prive" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_prive',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<h2>Informations Personnelles</h2>
															<div class="form_hr">
																		<div class="label_form label">Et dont le genre = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_genre",	$sessionFiltre)	?>">
																					<input type="radio" name="ADH_genre" value="Tous" checked> Tous
																					<input type="radio" name="ADH_genre" value="H" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_genre',	'H',	'RADIO')	?>> Homme
																					<input type="radio" name="ADH_genre" value="F" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_genre',	'F',	'RADIO')	?>> Femme
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le nom a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_nom",	$sessionFiltre)	?>">
																					<input name="ADH_nom" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_nom',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le prénom a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_prenom",	$sessionFiltre)	?>">
																					<input name="ADH_prenom" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_prenom',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et l'identifiant a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_identifiant",	$sessionFiltre)	?>">
																					<input name="ADH_identifiant" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_identifiant',	'Tous',	'CHAMP')	?>" size="20">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Dont l'année de naissance = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_annee_naissance",	$sessionFiltre)	?>">
																					<input name="ADH_annee_naissance" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_annee_naissance',	'Tous',	'CHAMP')	?>" size="10">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Dont le type tarif = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_TYTAR_id",	$sessionFiltre)	?>">
																					<select name="FK_TYTAR_id">
																								<option value="Tous" selected>Tous</option>
																								<?php	while	($row_RS1	=	mysql_fetch_assoc($RS1))	{	?>
																											<option value="<?php	echo	$row_RS1['TYTAR_id']	?>"<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_TYTAR_id',	$row_RS1['TYTAR_id'],	'LISTE')	?>><?php	echo	$row_RS1['TYTAR_nom_fr']	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<h2>Communication</h2>
															<div class="form_hr">
																		<div class="label_form label">Et l'email a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_email",	$sessionFiltre)	?>">
																					<input name="ADH_email" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_email',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et l'inscription à la newsletter = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("NEWS_email",	$sessionFiltre)	?>">
																					<input type="radio" name="NEWS_email" value="Tous" checked> Tous
																					<input type="radio" name="NEWS_email" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'NEWS_email',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="NEWS_email" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'NEWS_email',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le télephone a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_telephone",	$sessionFiltre)	?>">
																					<input name="ADH_telephone" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_telephone',	'Tous',	'CHAMP')	?>" size="20">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le portable a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_portable",	$sessionFiltre)	?>">
																					<input name="ADH_portable" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_portable',	'Tous',	'CHAMP')	?>" size="20">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et la langue = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_langue",	$sessionFiltre)	?>">
																					<input type="radio" name="ADH_langue" value="Tous" checked> Tous
																					<input type="radio" name="ADH_langue" value="FR" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_langue',	'FR',	'RADIO')	?>> Français
																					<input type="radio" name="ADH_langue" value="EN" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_langue',	'EN',	'RADIO')	?>> Anglais
																		</div>
															</div>
															<h2>Adresse</h2>
															<div class="form_hr">
																		<div class="label_form label">Et l'adresse 1 a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_adresse1",	$sessionFiltre)	?>">
																					<input name="ADH_adresse1" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_adresse1',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et l'adresse 2 a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_adresse2",	$sessionFiltre)	?>">
																					<input name="ADH_adresse2" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_adresse2',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le code postal a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_zip",	$sessionFiltre)	?>">
																					<input name="ADH_zip" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_zip',	'Tous',	'CHAMP')	?>" size="20">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et la ville a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_ville",	$sessionFiltre)	?>">
																					<input name="ADH_ville" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_ville',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le pays = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_PAYS_id",	$sessionFiltre)	?>">
																					<select name="FK_PAYS_id">
																								<option value="Tous" selected>Tous</option>
																								<?php	while	($row_RS2	=	mysql_fetch_assoc($RS2))	{	?>
																											<option value="<?php	echo	$row_RS2['PAYS_id']	?>"<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_PAYS_id',	$row_RS2['PAYS_id'],	'LISTE')	?>><?php	echo	$row_RS2['PAYS_nom_fr']	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<?php	if	($configAppli['ADHERENT']['sangha']	==	"oui")	{	?>
															<h2>Sangha</h2>
															<div class="form_hr">
																		<div class="label_form label">Ordination = </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_ordination",	$sessionFiltre)	?>">
																					<input type="radio" name="ADH_ordination" value="Tous" checked> Tous
																					<input type="radio" name="ADH_ordination" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_ordination',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="ADH_ordination" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_ordination',	'0',	'RADIO')	?>> non
																					<div class="note">A cocher si vous rechercher une nonne ou un moine.</div>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et le nom de Dharma a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_nom_dharma",	$sessionFiltre)	?>">
																					<input name="ADH_nom_dharma" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_nom_dharma',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<?php	}	?>
															<?php	if	($configAppli['ADHERENT']['benevolat']	==	"oui")	{	?>
															<h2>Bénévolat</h2>
															<div class="form_hr">
																		<div class="label_form label">Et souhaite être bénévole </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_benevolat",	$sessionFiltre)	?>">
																					<input type="radio" name="ADH_benevolat" value="Tous" checked> Tous
																					<input type="radio" name="ADH_benevolat" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_benevolat',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="ADH_benevolat" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_benevolat',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et la profession a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_profession",	$sessionFiltre)	?>">
																					<input name="ADH_profession" type="text" value="<?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_profession',	'Tous',	'CHAMP')	?>" size="35">
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et la disponibilité a </div>
																		<div class="content_form <?php	echo	adn_showFiltre("ADH_disponibilite",	$sessionFiltre)	?>">
																					<select name="ADH_disponibilite">
																								<option value="Tous" selected> Tous</option>
																								<option value="ALL" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_disponibilite',	'ALL',	'LISTE')	?>> Tout le temps</option>
																								<option value="VAC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_disponibilite',	'VAC',	'LISTE')	?>> Pendant les vacances scolaires</option>
																								<option value="WE" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'ADH_disponibilite',	'WE',	'LISTE')	?>> Pendant le week end</option>
																					</select>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label">Et les compétences sont ... ou ... </div>
																		<div class="content_form <?php	echo	adn_showFiltre("TJ_CMPT_id",	$sessionFiltre)	?>">
																					<ul class="liste_add">
																								<?php
																								if	($nbr_competence	>	0)	{
																											while	($row_RS4	=	mysql_fetch_assoc($RS4))	{
																														?>
																														<li><input type="checkbox" name="TJ_CMPT_id[]" value="<?php	echo	$row_RS4['CMPT_id']	?>" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'TJ_CMPT_id',	$row_RS4['CMPT_id'],	'CHECKBOX')	?>><?php	echo	$row_RS4['CMPT_nom_fr']	?></li>
																														<?php
																											}	}	else	{
																											echo	"Aucune compétence renseignée";
																								}
																								?>
																					</ul>
																		</div>
															</div>
															<?php	}	?>
															<h2>Autres</h2>
															<div class="form_hr">
																		<div class="label_form label">Et posséde un commentaire</div>
																		<div class="content_form <?php	echo	adn_showFiltre("FK_CMTADH_id",	$sessionFiltre)	?>">
																					<input type="radio" name="FK_CMTADH_id" value="Tous" checked> Tous
																					<input type="radio" name="FK_CMTADH_id" value="1" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_CMTADH_id',	'1',	'RADIO')	?>> oui
																					<input type="radio" name="FK_CMTADH_id" value="0" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'FK_CMTADH_id',	'0',	'RADIO')	?>> non
																		</div>
															</div>
															<div class="form_hr">
																		<div class="content_form">Trier par :
																					<select name="select_tri">
																								<option value="ADH_id DESC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"ADH_id DESC",	'LISTE')	?>>Date d'inscription</option>
																								<option value="ADH_nom ASC, ADH_prenom ASC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"ADH_nom ASC, ADH_prenom ASC",	'LISTE')	?>>Nom</option>
																								<option value="ADH_ville ASC, ADH_nom ASC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"ADH_ville ASC, ADH_nom ASC",	'LISTE')	?>>Ville</option>
																								<option value="ADH_zip ASC, ADH_nom ASC" <?php	echo	adn_reafficheLastFiltre($sessionFiltre,	'select_tri',	"ADH_zip ASC, ADH_nom ASC",	'LISTE')	?>>Code postal</option>
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
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>