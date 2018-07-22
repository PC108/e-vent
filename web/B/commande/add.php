<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../librairie/php/code_adn/Formatage.php');
require_once('_requete.php');

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
// récupération de la variable GET $id
$id	=	$_GET["id"];
$Action	=	"maj";
$Submit	=	"Modifier";
// création du recordset

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$query	=	mainQueryCmd($connexion);
$query	.=	" WHERE CMD_id=$id ";
$RS	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$row	=	mysql_fetch_object($RS);

$ref	=	$row->CMD_ref;
$idEtat	=	$row->FK_ECMD_id;
$idModePay	=	$row->FK_MDPAY_id;
$acheteur	=	$row->ADH_prenom	.	" "	.	$row->ADH_nom;
$nomEtat	=	$row->ECMD_nom;
$dateCrea	=	adn_changeFormatDate($row->CMD_date,	'DB_fr');
// TODO a modifier
if	(($row->CMD_date_confirm	==	"0000-00-00"))	{
			$defaultdate	=	date("d/m/Y");
}	else	{
			$defaultdate	=	adn_changeFormatDate($row->CMD_date_confirm,	'DB_fr');
}
$total	=	$row->totalCommande;
$remise	=	$row->CMD_remise;
$encaissement	=	$row->CMD_encaissement;

$query2	=	listeEtatsQuery();
$RS2	=	mysql_query($query2,	$connexion)	or	die(mysql_error());
$listeEtats	=	array();
while	($row_RS2	=	mysql_fetch_object($RS2))	{
			$listeEtats[$row_RS2->ECMD_id]	=	$row_RS2->ECMD_nom;
}

$query3	=	listeModePayeQuery();
$RS3	=	mysql_query($query3,	$connexion)	or	die(mysql_error());

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['COMMANDES']['L1']['nom']	.	"  - "	.	$Submit	.	" un enregistrement";

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
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.position.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.widget.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.dialog.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.datepicker.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui_localisation/jquery.ui.datepicker-fr.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_FR.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript" src="../_aide.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {

												$.datepicker.setDefaults($.datepicker.regional["fr"]);
												$("#affiche_date_confirm").datepicker({
															defaultDate: <?php	echo	$defaultdate;	?>,
															altField: "#CMD_date_confirm",
															altFormat: "yy-mm-dd"
												});

												$("#form_commande").validate({
															rules: {
																		'CMD_encaissement': {
																					required: true,
																					number: true,
																					min: 0
																		},
																		'CMD_remise': {
																					required: true,
																					number: true,
																					min: 0
																		}
															}
												});
									});
						</script>
						<!-- CSS -->
						<link href="../_css/jquery-ui.css" type="text/css" rel="stylesheet" />
						<link href="../_css/jquery-override.css" type="text/css" rel="stylesheet" />
						<link href="../_css/style_bo.css" rel="stylesheet" type="text/css"/>
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<form id="form_commande" action="action.php" method="post">
												<div class="BoxSearch">
															<h2>Commande</h2>
															<div class="form_hr">
																		<div class="label_form">Réference</div>
																		<div class="content_form"><?php	echo	$ref;	?>	</div>
															</div>
															<div class="form_hr">
																		<div class="label_form">Date de création</div>
																		<div class="content_form"><?php	echo	$dateCrea;	?>	</div>
															</div>

															<div class="form_hr">
																		<div class="label_form">Acheteur</div>
																		<div class="content_form"><b><?php	echo	($acheteur);	?></b></div>
															</div>
															<div class="form_hr">
																		<div class="label_form">Total de la commande</div>
																		<div class="content_form"><b><?php	echo	($total);	?> €</b></div>
															</div>
															<div class="form_hr">
																		<div class="label_form">Remboursement</div>
																		<div class="content_form">
																					<?php	echo	$row->totalRemb;	?> €
																					<div class="note">Le remboursement se définit au niveau d'un achat.</div>
																		</div>
															</div>
															<h2>Etat</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Etat <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></div>
																		<div class="content_form">
																					Etat actuel : <b><?php	echo	$listeEtats[$idEtat]	?></b> >>>
																					<select  id="FK_ECMD_id" name="FK_ECMD_id" class="form_R">
																								<option value="<?php	echo	$idEtat	?>">Ne pas modifier l'état</option>
																								<option value="2">Passer l'état en "<?php	echo	$listeEtats[2]	?>"</option>
																								<option value="6">Passer l'état en "<?php	echo	$listeEtats[6]	?>"</option>
																					</select>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Date de confirmation <img class="bt_help pointer" src="../_media/bo_help_16.png" width="16" height="16" align="absmiddle" alt="aide"/></div>
																		<div class="content_form">
																					<input name="affiche_date_confirm" id="affiche_date_confirm" type="text"  value="<?php	echo	$defaultdate;	?>" class="form_R">
																		</div>
															</div>
															<h2>Encaissement</h2>
															<div class="form_hr">
																		<div class="label_form label_R">Somme payée</div>
																		<div class="content_form">
																					<input id="CMD_encaissement" name="CMD_encaissement" type="text" value="<?php	echo	($encaissement);	?>" size="10" class="form_R"> €
																					<label for="CMD_encaissement" class="error" style="display:none">Ce champ est requis.</label>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Mode de paiement</div>
																		<div class="content_form">
																					<select  id="FK_MDPAY_id" name="FK_MDPAY_id" class="form_R"">
																								<?php	while	($row_RS3	=	mysql_fetch_assoc($RS3))	{	?>
																											<option value="<?php	echo	$row_RS3['MDPAY_id']	?>"<?php	if	(!(strcmp($row_RS3['MDPAY_id'],	$idModePay)))	{	echo	"SELECTED";	}	?>><?php	echo	$row_RS3['MDPAY_nom_fr']	?></option>
																								<?php	}	?>
																					</select>
																		</div>
															</div>
															<div class="form_hr">
																		<div class="label_form label_R">Remise</div>
																		<div class="content_form">
																					<input id="CMD_remise" name="CMD_remise" type="text" value="<?php	echo	($remise);	?>" size="10" class="form_R"> €
																					<label for="CMD_remise" class="error" style="display:none">Ce champ est requis.</label>
																		</div>
															</div>
															<div class="form_submit">
																		<input name="action" type="hidden" id="action" value="<?php	echo	$Action;	?>">
																		<input name="pageNum" type="hidden" id="pageNum" value="<?php	echo	$pageNum;	?>">
																		<input name="CMD_id" type="hidden" id="CMD_id" value="<?php	echo	$id;	?>">
																		<!--<input name="CMD_date" type="hidden" id="CMD_date" value="<?php	echo	$dateCrea;	?>">-->
																		<input name="CMD_date_confirm" type="hidden" id="CMD_date_confirm" value="<?php	echo	date('Y-m-d');	?>">
																		<input type="submit" name="Submit" value="<?php	echo	$Submit	?>" class="submit">
															</div>
												</div>
									</form>
									<div id="help_icon" class="bt_help pointer"><img src="../_media/bo_help_32.png"/></div>
									<div id="help" title='<img src="../_media/bo_help_32.png" align="absmiddle"/> aide'>
												<p><u>Etats assignables</u></p>
												<p>En cours: <span class="note">La commande a été créée par l'utilisateur qui est en train d'en définir le contenu.</span></p>
												<p>Confirmée: <span class="note">Passez une commande en « confirmée » lorsque vous avez reçu le paiement de la commande.
																		<br/>La commande ne peut alors plus être modifiée par l’utilisateur et passe dans la rubrique "Historique des commandes".
																		<br />Une commande dont l’état est PAYPAL validé doit être passée manuellement en confirmé.
																		<br />ATTENTION : Tous les achats d'une commande confirmée sont immédiatement déduits dans les décomptes de capacités de places.</span>
												</p>
												<p><u>Date de confirmation</u></p>
												<p  class="note">Si vous passez votre commande en état "Confirmée", elle sera prise en compte dans les statistiques par mois avec cette date de confirmation.</p>
									</div>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
