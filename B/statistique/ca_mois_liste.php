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
/*              Execution des requêtes                     */
/*	* ******************************************************** */
//Pour obtenir l'affichage des mois en français
setlocale(LC_TIME,	'fra');

//Execution de la requete mainQueryDateSql pour récupérer tous les mois de la BDD
$query_d	=	mainQueryDateSql($connexion);
$RS_d	=	mysql_query($query_d,	$connexion)	or	die(mysql_error());
$nbre_rows_d	=	mysql_num_rows($RS_d);

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['STATISTIQUES']['L1']['nom'];
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
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
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
									<?php	if	($nbre_rows_d	==	0)	{	?>
												<div class="ui-state-error ui-corner-all uiphil-msg">
															<p>Aucune commande confirmée n'a été trouvée pour être prise en compte dans les statistiques.</p>
												</div>
									<?php	}	else	{	?>
												<div id="chart_div" style="width: 1000px; height: 300px;"></div>
												<p>Cliquez sur l'événement pour obtenir le détail | Cliquez sur l'icone pour télécharger le tableau<a href="csv_mois_liste.php"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a></p>
												<table	 class="table_result" cellspacing="0">
															<tr>
																		<th>Mois</th>	
																		<th>Nombre<br />cmd(s)</th>
																		<th>Cotisation</th>											
																		<th>Don</th>
																		<th>Evenement</th>
																		<th>Hebergement</th>
																		<th>Restauration</th>
																		<th>Somme<br />Achats</th>
																		<th>Somme<br />Remise</th>
																		<th>Somme<br />Commande</th>
																		<th>Remb.</th>
																		<th>Chiffre d'affaire</th>
																		<th>Encaissement</th>	
															</tr>
															<?php
															$tableau	=	array();
															while	($row	=	mysql_fetch_object($RS_d))	{
																		$date_sql	=	$row->date_sql;
																		$date_mois	=	strftime("%B %Y",	strtotime($date_sql,	time()));
																		$date_graphe	=	strftime("%m-%y",	strtotime($date_sql,	time()));
																		//Execution de la requete mainQuerySommeCommandes pour le calcul des sommes pour chaque mois 
																		$query_sum_c	=	mainQuerySommeCommandes($connexion,	$date_sql);
																		$RS_sum_c	=	mysql_query($query_sum_c,	$connexion)	or	die(mysql_error());
																		$rows_sum_c	=	mysql_fetch_object($RS_sum_c);

																		$tableau[]	=	array($date_graphe,	(float)	$rows_sum_c->TotalEncaissement,	(float)	$rows_sum_c->TotalChiffreAffaire);

																		// Affichage des différences entre CA et Encaissement
																		if	($rows_sum_c->TotalChiffreAffaire	!=	$rows_sum_c->TotalEncaissement)	{
																					$addClass	=	"complet";
																		}	else	{
																					$addClass	=	"";
																		}
																		?>
																		<?php	// $array=array ($date_mois, $rows_sum_c->TotalChiffreAffaire, $rows_sum_c->TotalEncaissement); echo $array[0]."".$array[1]."".$array[2];	//echo $array[0];?>
																		<tr class="to_highlight">
																					<td nowrap><a  href="<?php	echo	"ca_mois_detail.php?date_sql="	.	$date_sql	?> "><?php	echo	utf8_encode($date_mois);	?></a></td>	

																					<td> <?php	echo	$rows_sum_c->Compteur	?></td>
																					<td> 
																								<?php
																								if	($rows_sum_c->TotalCotisation	!=	null)	{
																											echo	$rows_sum_c->TotalCotisation	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas de cotisation";	}	?></span>
																					</td>
																					<td> <?php
															if	($rows_sum_c->TotalDon	!=	null)	{
																		echo	$rows_sum_c->TotalDon	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas de don";	}	?></span>
																					</td>
																					<td> <?php
															if	($rows_sum_c->TotalEvenement	!=	null)	{
																		echo	$rows_sum_c->TotalEvenement	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas d'événement";	}	?></span>
																					</td>
																					<td> <?php
															if	($rows_sum_c->TotalHebergement	!=	null)	{
																		echo	$rows_sum_c->TotalHebergement	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas d'hébergement";	}	?></span>
																					</td>
																					<td> <?php
															if	($rows_sum_c->TotalRestauration	!=	null)	{
																		echo	$rows_sum_c->TotalRestauration	.	" €";	}	else	{
																											?>
																											<span class="note"> <?php	echo	" Pas de restauration";	}	?></span>
																					</td>
																					<td> <?php	echo	$rows_sum_c->TotalSommeAchats	.	" €"	?></td>
																					<td> <?php	echo	$rows_sum_c->TotalSommeRemise	.	" €"	?></td>
																					<td> <?php	echo	$rows_sum_c->TotalSommePercu	.	" €"	?></td>
																					<td> <?php	echo	$rows_sum_c->TotalRemboursement	.	" €"	?></td>
																					<td class="<?php	echo	$addClass	?>"> <?php	echo	$rows_sum_c->TotalChiffreAffaire	.	" €"	?></td>
																					<td class="<?php	echo	$addClass	?>"> <?php	echo	$rows_sum_c->TotalEncaissement	.	" €"	?></td>		
																		</tr>

															<?php	}	?>
															<?php
															// renverse le tableau pour avoir le mois le plus ancien à gauche
															$tableau[]	=	array('Mois-Année',	'Encaissement',	'CA');
															$tableau	=	array_reverse($tableau);
															$tableau	=	json_encode($tableau);
															?>
															<div>										
																		<script type="text/javascript">
																																																				
																					google.load("visualization", "1", {packages:["corechart"]});
																					google.setOnLoadCallback(drawChart);
																																				
																					function drawChart() {
																																																												
																								var data = google.visualization.arrayToDataTable(<?php	echo	$tableau	?>);
																								var options = {
																											title: 'Statistique commandes par mois',
																											hAxis: {title: 'Mois-Année', titleTextStyle: {color: 'red'}},
																											backgroundColor:'#eeeeee'
																								};

																								var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
																								chart.draw(data, options);
																					}
																		</script>
															</div>
												<?php	}	?>

									</table>
									<?php
									if	($_SESSION['afficheSession']	==	2)	{
												echo	('<p class="requete"><b>Requête : </b>'	.	$query_sum_c	.	'</p>');
									}
									?>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
