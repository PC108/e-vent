<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once("../jourevenement/_requete.php");
require_once('../../librairie/php/code_adn/Formatage.php');

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
if	(isSet($_GET['pageNum']))	{
		$pageNum	=	$_GET['pageNum'];
}	else	{
		$pageNum	=	0;
}

/*	* ******************************************************** */
/*               Définition des variables                  */
/*	* ******************************************************** */
// Récupère le nom de l'événement
$idEven	=	$_GET["idEven"];
$RS1	=	mysql_query(nomEvenJreven($idEven),	$connexion)	or	die(mysql_error());
$row	=	mysql_fetch_object($RS1);
$nomEven	=	$row->EVEN_nom_fr;	// Pour indication, pas modifiable
// Récupère la liste des hébergements et la transforme en tableau
$RS2	=	mysql_query(listeHebJreven(),	$connexion)	or	die(mysql_error());
$val_hebergement	=	array();
while	($row	=	mysql_fetch_assoc($RS2))	{
		$row['actif']	=	FALSE;
		$val_hebergement[$row['TYHEB_id']]	=	$row;
}
// Récupère la liste des restaurations
$RS3	=	mysql_query(listeResJreven(),	$connexion)	or	die(mysql_error());
$val_restauration	=	array();
while	($row	=	mysql_fetch_assoc($RS3))	{
		$row['actif']	=	FALSE;
		$val_restauration[$row['TYRES_id']]	=	$row;
}

// récupération de la variable GET $id
if	(!isSet($_GET["idJour"]))	{
// Mode ADD
		$id	=	0;
		$Action	=	"add";
		$Submit	=	"Ajouter";
		$dateDBDebut	=	"0000-00-00";
		$dateDebut	=	"";
		$dateDBFin	=	"0000-00-00";
		$dateFin	=	"";
		$montant	=	"";
		$surcout	=	0;
		$places	=	"";
		$lieu	=	0;
		$etat	=	1;

		while	($hebergementLignes	=	mysql_fetch_array($RS2))	{
				
		}
}	else	{
		// Mode MAJ
		$id	=	$_GET['idJour'];
		$Action	=	"maj";
		$Submit	=	"Modifier";
		// récupère les infos du jour
		$RS4	=	mysql_query(infoEvenJreven($id),	$connexion)	or	die(mysql_error());
		$row	=	mysql_fetch_object($RS4);
		$dateDBDebut	=	$row->JREVEN_date_debut;
		$dateDebut	=	adn_changeFormatDate($row->JREVEN_date_debut,	"DB_FR");
		$dateDBFin	=	$row->JREVEN_date_fin;
		$dateFin	=	adn_changeFormatDate($row->JREVEN_date_fin,	"DB_FR");
		$montant	=	$row->JREVEN_montant;
		$surcout	=	$row->JREVEN_surcout;
		$places	=	$row->JREVEN_places;
		$lieu	=	$row->FK_LEVEN_id;
		$etat	=	$row->EJREVEN_nom_fr;
// Modifie les valeurs par défaut de l'hébergement en fonction des résultats trouvés dans la table jointe tj_tyheb_jreven
		$query_RS5	=	"SELECT * FROM tj_tyheb_jreven WHERE TJ_JREVEN_id=$id";
		$RS5	=	mysql_query($query_RS5,	$connexion)	or	die(mysql_error());
		while	($row	=	mysql_fetch_object($RS5))	{
				$id_hebergement	=	$row->TJ_TYHEB_id;
				$val_hebergement[$id_hebergement]['TYHEB_montant_defaut']	=	$row->TYHEB_JREVEN_montant;
				$val_hebergement[$id_hebergement]['TYHEB_capacite_defaut']	=	$row->TYHEB_JREVEN_capacite;
				$val_hebergement[$id_hebergement]['actif']	=	TRUE;
		}
		// Modifie les valeurs par défaut de la restauration en fonction des résultats trouvés dans la table jointe tj_tyres_jreven
		$query_RS6	=	"SELECT * FROM tj_tyres_jreven WHERE TJ_JREVEN_id=$id";
		$RS6	=	mysql_query($query_RS6,	$connexion)	or	die(mysql_error());
		while	($row	=	mysql_fetch_object($RS6))	{
				$id_restauration	=	$row->TJ_TYRES_id;
				$val_restauration[$id_restauration]['TYRES_montant_defaut']	=	$row->TYRES_JREVEN_montant;
				$val_restauration[$id_restauration]['TYRES_capacite_defaut']	=	$row->TYRES_JREVEN_capacite;
				$val_restauration[$id_restauration]['actif']	=	TRUE;
		}
}
$RS7	=	mysql_query(listeEtatJreven(),	$connexion)	or	die(mysql_error());

// Liste des lieux
$RS5	=	mysql_query(listeLieuxJreven(),	$connexion)	or	die(mysql_error());


/*	* ******************************************************** */
/*       Variables d'affichages de messages                */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['EVENEMENTS']['L5']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html>
		<head>
				<title><?php	echo	$titre;	?></title>
				<meta NAME="author" CONTENT="www.atelierdu.net" />
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
				<link rel="icon" type="image/png" href="../_media/favicon.png" />
				<!-- JS -->
				<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.core.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.datepicker.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/ui_localisation/jquery.ui.datepicker-fr.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
				<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
				<script type="text/javascript" src="../_shared.js"></script>
				<script type="text/javascript">
						$(document).ready(function() {

								$.datepicker.setDefaults($.datepicker.regional["fr"]);
								$("#JREVEN_date_debut").datepicker({
										defaultDate: 0,
										minDate: null,
										beforeShow: customRange,
										altField: "#alternateDeb",
										altFormat: "yy-mm-dd"
								});
								$("#JREVEN_date_fin").datepicker({
										minDate: null,
										beforeShow: customRange,
										altField: "#alternateFin",
										altFormat: "yy-mm-dd"
								});


								function customRange(input) {
										if (input.id == 'JREVEN_date_fin') {
												return {
														minDate: $('#JREVEN_date_debut').datepicker("getDate")
												};
										} else if (input.id == 'JREVEN_date_debut') {
												return {
														maxDate: $('#JREVEN_date_fin').datepicker("getDate")
												};
										}
								}

								$('table input:checkbox').click (function () {
										if ($(this).is(':checked')) {
												$(this).closest('tr').find('input:text').removeAttr("disabled");
										} else {
												$(this).closest('tr').find('input:text').attr("disabled", "disabled");
										}
								});

								// VALIDATION
								$("#form_jourevent").validate({
										rules: {
												'JREVEN_date_debut': {
														required: true
												},
												'JREVEN_date_fin': {
														required: true
												},
												'JREVEN_montant': {
														required: true,
														number: true,
														min: 0
												},
												'JREVEN_places': {
														required: true,
														number: true,
														min: 0
												},
												'FK_LEVEN_id': {
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
						<form id="form_jourevent" action="action.php" method="post">
								<div class="BoxSearch">
										<h2>Informations liées au jours-événement</h2>
										<div class="form_hr">
												<div class="label_form label_R">Lieu du jour-événement</div>
												<div class="content_form">
														<select  id="FK_LEVEN_id" name="FK_LEVEN_id" class="form_R">
																<option value="">Choisir un lieu</option>
																<?php	while	($row_RS5	=	mysql_fetch_assoc($RS5))	{	?>
																		<option value="<?php	echo	$row_RS5['LEVEN_id']	?>"<?php	if	($row_RS5['LEVEN_id']	==	$lieu)	{	echo	"SELECTED";	}	?>><?php	echo	$row_RS5['LEVEN_nom']	?></option>
																<?php	}	?>
														</select>
														<div class="note">Pour ajouter un nouveau lieu dans la liste, <a href="../lieuevent/result.php">cliquez ici.</a></div>
												</div>
										</div>
										<h2>Jours-événement : <?php	echo	$nomEven;	?></h2>
										<div class="form_hr">
												<div class="label_form label_R">Etat du jour</div>
												<div class="content_form">
														<select  id="FK_EJREVEN_id" name="FK_EJREVEN_id" class="form_R">
																<?php	while	($row_RS7	=	mysql_fetch_assoc($RS7))	{	?>
																		<option value="<?php	echo	$row_RS7['EJREVEN_id']	?>"<?php	if	(!(strcmp($row_RS7['EJREVEN_nom_fr'],	$etat)))	{	echo	"SELECTED";	}	?>><?php	echo	$row_RS7['EJREVEN_nom_fr']	?></option>
																<?php	}	?>
														</select>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Dates</div>
												<div class="content_form">
														Du <input id="JREVEN_date_debut" name="JREVEN_date_debut" type="text" value="<?php	echo	($dateDebut);	?>" size="10" class="form_R">
														au <input id="JREVEN_date_fin" name="JREVEN_date_fin" type="text" value="<?php	echo	($dateFin);	?>" size="10" class="form_R">
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form label_R">Montant</div>
												<div class="content_form">
														<input id="JREVEN_montant" name="JREVEN_montant" type="text" value="<?php	echo	($montant);	?>" size="10" class="form_R" AUTOCOMPLETE="OFF"> €
														<label for="JREVEN_montant" class="error" style="display:none">Ce champ est requis.</label>
														<div class="note">Mettre 0 si le jour de l'événement est gratuit</div>
												</div>
										</div>
										<?php	if	($configAppli['MENU']['cotisation']	==	"oui")	{	?>
												<div class="form_hr">
														<div class="label_form label_R">Surcoût cotisation</div>
														<div class="content_form">
																<input id="JREVEN_surcout" name="JREVEN_surcout" type="text" value="<?php	echo	($surcout);	?>" size="10" class="form_R" AUTOCOMPLETE="OFF"> €
																<label for="JREVEN_surcout" class="error" style="display:none">Ce champ est requis.</label>
																<div class="note">Surcoût si la personne n'a pas payé sa cotisation annuelle.</div>
														</div>
												</div>
										<?php	}	?>
										<div class="form_hr">
												<div class="label_form label_R">Capacité</div>
												<div class="content_form">
														<input id="JREVEN_places" name="JREVEN_places" type="text" value="<?php	echo	($places);	?>" size="10" class="form_R" AUTOCOMPLETE="OFF"> places
														<label for="JREVEN_places" class="error" style="display:none">Une capacité ne peut pas être négative.</label>
														<div class="note">Mettre 0 si le nombre d'inscription n'est pas limité. <br />Quand le nombre d'inscription dépassera la capacité, l'état du jour passera en complet.</div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form ">Hébergement</div>
												<div class="content_form">
														<?php	if	(count($val_hebergement))	{	?>
																<table width="100%">
																		<tr>
																				<td>&nbsp;</td>
																				<td>Montant d'une place</td>
																				<td>Capacité globale</td>
																		</tr>
																		<?php	foreach	($val_hebergement	as	$key	=>	$value)	{	?>
																				<tr>
																						<td><input type="checkbox"  name="TYHEB_<?php	echo	$key	?>[]" <?php	if	($value['actif'])	{	echo	"CHECKED";	}	?>><?php	echo	$value['TYHEB_nom_fr']	?></td>
																						<td width="150"><input name="TYHEB_<?php	echo	$key	?>[]" type="text" <?php	if	(!$value['actif'])	{	echo	'disabled="disabled"';	}	?> value="<?php	echo	($value['TYHEB_montant_defaut']);	?>" size="10" AUTOCOMPLETE="OFF"> €</td>
																						<td width="150"><input name="TYHEB_<?php	echo	$key	?>[]" type="text" <?php	if	(!$value['actif'])	{	echo	'disabled="disabled"';	}	?> value="<?php	echo	($value['TYHEB_capacite_defaut']);	?>" size="10" AUTOCOMPLETE="OFF"></td>
																				</tr>
																		<?php	}	?>
																</table>
														<?php	}	else	{	?>
																<p>Aucun type d'hébergement n'a été ajouté pour le moment.</p>
														<?php	}	?>
														<div class="note">Pour ajouter un nouvel hébergement ou modifier les valeurs par défaut, <a href="../typehebergement/result.php">cliquez ici.</a></div>
												</div>
										</div>
										<div class="form_hr">
												<div class="label_form">Restauration</div>
												<div class="content_form">
														<?php	if	(count($val_restauration))	{	?>
																<table width="100%">
																		<tr>
																				<td>&nbsp;</td>
																				<td>Montant d'une place</td>
																				<td>Capacité globale</td>
																		</tr>
																		<?php	foreach	($val_restauration	as	$key	=>	$value)	{	?>
																				<tr>
																						<td><input type="checkbox" name="TYRES_<?php	echo	$key	?>[]" <?php	if	($value['actif'])	{	echo	"CHECKED";	}	?>><?php	echo	$value['TYRES_nom_fr']	?></td>
																						<td width="150"><input name="TYRES_<?php	echo	$key	?>[]" type="text" <?php	if	(!$value['actif'])	{	echo	'disabled="disabled"';	}	?> value="<?php	echo	($value['TYRES_montant_defaut']);	?>" size="10" AUTOCOMPLETE="OFF"> €</td>
																						<td width="150"><input name="TYRES_<?php	echo	$key	?>[]" type="text" <?php	if	(!$value['actif'])	{	echo	'disabled="disabled"';	}	?> value="<?php	echo	($value['TYRES_capacite_defaut']);	?>" size="10" AUTOCOMPLETE="OFF"></td>
																				</tr>
																		<?php	}	?>
																</table>
														<?php	}	else	{	?>
																<p>Aucun type de restauration n'a été ajouté pour le moment.</p>
														<?php	}	?>
														<div class="note">Pour ajouter une nouvelle restauration  ou modifier les valeurs par défaut, <a href="../typerestauration/result.php">cliquez ici.</a></div>
												</div>
										</div>
										<div class="form_submit">
												<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
												<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
												<input name="alternateDeb" type="hidden" id="alternateDeb" value="<?php	echo	$dateDBDebut;	?>">
												<input name="alternateFin" type="hidden" id="alternateFin" value="<?php	echo	$dateDBFin;	?>">
												<input name="JREVEN_id" type="hidden" id="JREVEN_id" value="<?php	echo	$id;	?>">
												<input name="FK_EVEN_id" type="hidden" id="FK_EVEN_id" value="<?php	echo	$idEven;	?>">
												<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
										</div>
								</div>
						</form>
				</div>
				<?php	include("../_footer.php")	?>
		</body>
</html>
