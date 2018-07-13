<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
include('_requete_mois.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('admin',	'stat')))	{
			$_SESSION['message_user']	=	"acces_ko";
			adn_myRedirection('../login/menu.php');
}

/*	* ******************************************************** */
/*              Récupération des variables GET                   */
/*	* ******************************************************** */
if	(isset($_GET	['date_sql']))	{
			$date_sql	=	$_GET['date_sql'];
}	else	{
			adn_myRedirection('ca_mois_liste.php');
			exit;
}

/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */
setlocale(LC_TIME,	'utf-8',	'fra');
$date_mois	=	strftime("%B %Y",	strtotime($date_sql,	time()));

//Execution de la requete mainQueryCommandes
$query_c	=	mainQueryCommandes($connexion,	$date_sql);
$RS_c	=	mysql_query($query_c,	$connexion)	or	die(mysql_error());
$nbre_rows_c	=	mysql_num_rows($RS_c);

//Execution de la requete mainQuerySommeCommandes
$query_sum_c	=	mainQuerySommeCommandes($connexion,	$date_sql);
$RS_sum_c	=	mysql_query($query_sum_c,	$connexion)	or	die(mysql_error());
$rows_sum_c	=	mysql_fetch_object($RS_sum_c);

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['STATISTIQUES']['L1']['nom']	.	" - Détail";
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
						<script type="text/javascript" src="../_fonction/ns_back.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {

												// Met en surbrillance la ligne du tableau.
												NS_BACK.highlightRow();
		
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
									<p>Statistique du mois de <b> <?php	echo	utf8_encode($date_mois);	?></b> contenant <b><?php	echo	$nbre_rows_c	?></b> commandes confirmées.</p>
									<p><a href="<?php	echo	"csv_mois_detail.php?date_sql="	.	$date_sql	?> "><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a> Cliquez ici pour télécharger le tableau en fichier excel</p>
									<?php	if	($nbre_rows_c	>	0)	{	?>
												<table	 class="table_result" cellspacing="0">
															<tr>
																		<th>Référence<br />commande</th>
																		<th>Nom</th>
																		<th>Prénom</th>
																		<th>Mode de<br />paiement</th>
																		<th>Nombre<br />achat(s)</th>
																		<th>Cotisation</th>
																		<th>Don</th>
																		<th>Evénement</th>
																		<th>Hébergement</th>
																		<th>Restauration</th>
																		<th>Somme<br />Achats</th>
																		<th>Remise</th>
																		<th>Total<br />Commande</th>
																		<th>Remboursement</th>
																		<th>Chiffre d'affaire</th>
																		<th>Encaissement</th>
															</tr>	
															<?php
															while	($row_c	=	mysql_fetch_object($RS_c))	{
																		// Affichage des différences entre CA et Encaissement
																		if	($row_c->chiffreAffaire	!=	$row_c->CMD_encaissement)	{
																					$addClass	=	"complet";
																		}	else	{
																					$addClass	=	"";
																		}
																		?>
																		<tr class="to_highlight">
																					<td> <a href="<?php	echo	"../../F/commande/print.php?lien="	.	$row_c->CMD_lien	.	"&retour="	.	'stat'	.	"&date_sql="	.	$date_sql	?>" > <?php	echo	$row_c->CMD_ref;	?></a></td>
																					<td nowrap> <?php	echo	$row_c->ADH_nom	?></td>
																					<td nowrap> <?php	echo	$row_c->ADH_prenom	?></td>
																					<td> <?php	echo	$row_c->MDPAY_nom_fr	?></td>
																					<td> <?php	echo	$row_c->NbreAchats	?></td>

																					<td nowrap> 
																								<?php
																								if	($row_c->Cotisation	!=	null)	{
																											echo	$row_c->Cotisation	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas de cotisation";	}	?></span>
																					</td>
																					<td nowrap> 
																								<?php
																								if	($row_c->Don	!=	null)	{
																											echo	$row_c->Don	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas de don";	}	?></span>
																					</td>
																					<td nowrap> 
																								<?php
																								if	($row_c->Evenement	!=	null)	{
																											echo	$row_c->Evenement	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas d'événement";	}	?></span>
																					</td>
																					<td nowrap> 
																								<?php
																								if	($row_c->Hebergement	!=	null)	{
																											echo	$row_c->Hebergement	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas d'hébergement";	}	?></span>
																					</td >
																					<td nowrap> 
																								<?php
																								if	($row_c->Restauration	!=	null)	{
																											echo	$row_c->Restauration	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas de restauration";	}	?></span>
																					</td>

																					<td nowrap> <?php	echo	$row_c->TotalAchats	.	" €"	?></td>
																					<td nowrap> <?php	echo	$row_c->CMD_remise	.	" €"	?></td>
																					<td nowrap> <?php	echo	$row_c->totalCommande	.	" €"	?></td>
																					<td nowrap> <?php	echo	$row_c->TotalRemb	.	" €"	?></td>
																					<td nowrap class="<?php	echo	$addClass	?>"> <?php	echo	$row_c->chiffreAffaire	.	" €"	?></td>
																					<td nowrap class="<?php	echo	$addClass	?>"> <?php	echo	$row_c->CMD_encaissement	.	" €"	?></td>
																		<?php	}	?>
															</tr>
															<tr>
																		<td class="td_somme" colspan=2>	Total du mois</td>	
																		<td class="td_somme" colspan=3>	</td>	
																		<td nowrap class="td_somme">
																					<?php
																					if	($rows_sum_c->TotalCotisation	!=	null)	{
																								echo	$rows_sum_c->TotalCotisation	.	" €";	}	else	{
																								?>
																								<span class="note"> <?php	echo	" Pas de cotisation";	}	?></span>
																		</td>
																		<td nowrap class="td_somme">
																					<?php
																					if	($rows_sum_c->TotalDon	!=	null)	{
																								echo	$rows_sum_c->TotalDon	.	" €";	}	else	{
																								?>
																								<span class="note"> <?php	echo	" Pas de don";	}	?></span>
																		</td>
																		<td nowrap class="td_somme">
																					<?php
																					if	($rows_sum_c->TotalEvenement	!=	null)	{
																								echo	$rows_sum_c->TotalEvenement	.	" €";	}	else	{
																								?>
																								<span class="note"> <?php	echo	" Pas d'événement";	}	?></span>
																		</td>
																		<td nowrap class="td_somme">
																					<?php
																					if	($rows_sum_c->TotalHebergement	!=	null)	{
																								echo	$rows_sum_c->TotalHebergement	.	" €";	}	else	{
																								?>
																								<span class="note"> <?php	echo	" Pas d'hébergement";	}	?></span>
																		</td>
																		<td nowrap class="td_somme">
																					<?php
																					if	($rows_sum_c->TotalRestauration	!=	null)	{
																								echo	$rows_sum_c->TotalRestauration	.	" €";	}	else	{
																								?>
																								<span class="note"> <?php	echo	" Pas de restauration";	}	?></span>
																		</td>
																		<td nowrap class="td_somme"><?php	echo	$rows_sum_c->TotalSommeAchats	.	" €"	?></td>
																		<td nowrap class="td_somme"><?php	echo	$rows_sum_c->TotalSommeRemise	.	" €"	?></td>
																		<td nowrap class="td_somme"><?php	echo	$rows_sum_c->TotalSommePercu	.	" €"	?></td>
																		<td nowrap class="td_somme"><?php	echo	$rows_sum_c->TotalRemboursement	.	" €"	?></td>
																		<td nowrap class="td_somme"><?php	echo	$rows_sum_c->TotalChiffreAffaire	.	" €"	?></td>
																		<td nowrap class="td_somme"><?php	echo	$rows_sum_c->TotalEncaissement	.	" €"	?></td>
															</tr>
												</table	>
									<?php	}	?>
									<?php
									if	($_SESSION['afficheSession']	==	2)	{
												echo	('<p class="requete"><b>Requête : </b>'	.	$query_c	.	'</p>');
												echo	('<p class="requete"><b>Requête : </b>'	.	$query_sum_c	.	'</p>');
									}
									?>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>

