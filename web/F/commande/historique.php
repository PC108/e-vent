<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
require_once('../../B/commande/_requete.php');

$title	=	_("Historique des factures");

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	(!isset($_SESSION['info_adherent']))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../evenement/index.php');
}	else	{
			$idAdh	=	$_SESSION['info_adherent']['id_adh'];
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
$query	=	mainQueryCmd($connexion);
$query	.=	" WHERE FK_ADH_id=$idAdh AND FK_ECMD_id IN (3,6,10) ORDER BY CMD_id DESC";	// (PAYPAL valide, Confirmé, En archive)
$RSCmd	=	mysql_query($query,	$connexion)	or	die(mysql_error());
$NbreRows_RSCmd	=	mysql_num_rows($RSCmd);

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php	echo	$langue;	?>" xml:lang="<?php	echo	$langue;	?>">
			<head>
						<title>e-venement.com | <?php	echo	$title;	?></title>
						<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
						<meta http-equiv="Content-Language" content="<?php	echo	$langue;	?>" />
						<link rel="icon" type="image/png" href="../_media/GEN/favicon.png" />
						<!-- JS -->
						<?php	include('../_shared_js.php');	?>
						<script type="text/javascript">
									$(document).ready(function() {
												//Navigation
												$('#navigation #commande').addClass('ui-state-active')
												$('#navigation #commande').button( "option", "disabled", true );

												//LOCAL
												// Boutons détail
												$( ".bt_detail" ).button({
															icons: {
																		secondary:'ui-icon-circle-zoomin'
															}
												});
												$('.bt_detail').click(function() {
															var lienCmd = $(this).attr('lien_cmd');
															window.location='print.php?lien='+lienCmd+'&retour=historique&lg=<?php	echo	$langue;	?>';
												});
									});
						</script>
						<!-- CSS -->
						<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
						<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
						<link type="text/css" href="../_css/style_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/cmd_front.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/css3.css" rel="stylesheet"  />
						<link type="text/css" href="../_css/custom.css" rel="stylesheet"  />
			</head>
			<body>
						<div id="global">
									<?php	include('../_header.php');	?>
									<?php	include('../_sidebar.php');	?>
									<div id="content" class="corner20-all">
												<h1><a href="result.php"><?php	echo	_("Commande en cours");	?></a> | <?php	echo	$title;	?></h1>
												<div class="info_print corner10-all">
															<?php	if	($NbreRows_RSCmd	==	0)	{	?>
																		<p><?php	echo	_("Votre historique des factures ne contient pour le moment aucun élément.");	?></p>
															<?php	}	else	{	?>
																		<table class="table_cmd" cellpadding="4" cellspacing="0" width="100%">
																					<tr>
																								<th class="commentaire"><?php	echo	_("Référence");	?></th>
																								<th class="commentaire"><?php	echo	_("Date de création");	?></th>
																								<th class="commentaire"><?php	echo	_("Montant");	?></th>
																								<th class="commentaire"><?php	echo	_("Etat");	?></th>
																								<th class="commentaire"><?php	echo	_("Paiement");	?></th>
																								<th>&nbsp;</th>
																					</tr>
																					<?php	while	($row	=	mysql_fetch_array($RSCmd))	{	?>
																								<tr>

																											<td><?php	echo	$row['CMD_ref']	?></td>
																											<td><?php	echo	adn_changeFormatDate($row['CMD_date'],	'DB_fr');	?></td>
																											<td><?php	echo	$row['totalCommande'];	?> €</td>
																											<td><?php	echo	$row['ECMD_description_'	.	$langue]	?> </td>
																											<td><?php	echo	$row['MDPAY_nom_'	.	$langue];	?></td>
																											<td width="10px"><div class="bt_detail small_jquery_bt" lien_cmd="<?php	echo	$row['CMD_lien']	?>"><a href="#"><?php	echo	_("détail");	?></a></div></td>
																								</tr>
																					<?php	}	?>
																		</table>
															<?php	}	?>
												</div>
												<?php	if	(isset($_SESSION['info_cmd']))	{	?>
															<div class="info_print corner10-all">
																		<div><img src="../_media/GEN/ic_info.png" align="absmiddle" alt="info"/> <span class="commentaire"><?php	echo	_("Notez qu'il existe aussi une commande en cours.");	?></span></div>
															</div>
												<?php	}	?>
    	    </div>
									<?php	include('../_footer.php');	?>
						</div>
			</body>
</html>