<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
include('_requete_event.php');
//include('../../librairie/php/code_adn/CropTexte.php');

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

//Execution de la requete QueryEvent pour récupérer tous les événements de la BDD
$query_e	=	QueryEvent($connexion);
$RS_e	=	mysql_query($query_e,	$connexion)	or	die(mysql_error());
$nbre_rows_e	=	mysql_num_rows($RS_e);

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['STATISTIQUES']['L2']['nom'];
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
									<?php	if	($nbre_rows_e	==	0)	{	?>
												<div class="ui-state-error ui-corner-all uiphil-msg">
															<p>Aucun événement n'a été trouvée pour être prise en compte dans les statistiques.</p>
												</div>
									<?php	}	else	{	?>
												<div id="chart_div" style="width: 1000px; height: 300px;"></div>
												<p>Cliquez sur l'événement pour obtenir le détail | Cliquez sur l'icone pour télécharger le tableau<a href="csv_event_liste.php"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a></p>
												<table	 class="table_result" cellspacing="0">
															<tr>
																		<th>Nom événement</th>	
																		<th>Nombre <br />jours événements</th>
																		<th>Somme <br />jours événements</th>
																		<th>Somme<br />hébergements</th>
																		<th>Somme<br />restaurations</th>
																		<th>Chiffre d'affaire</th>
															</tr>
															<?php
															$tableau[]	=	array('Evénement',	'CA');
															while	($row	=	mysql_fetch_object($RS_e))	{
																		$even_id	=	$row->EVEN_id;
																		//Execution de la requête QuerySommeJourEvent pour le calcul des sommes pour chaque JourEvent 
																		$query_sum_e	=	QuerySommeJourEvent($connexion,	$even_id);
																		$RS_sum_e	=	mysql_query($query_sum_e,	$connexion)	or	die(mysql_error());
																		$rows_sum_e	=	mysql_fetch_object($RS_sum_e);
																		// $nom_event=adn_cropTexte ($row->EVEN_nom_fr, 10);
																		$nom_event	=	$row->EVEN_nom_fr;
																		$tableau[]	=	array($nom_event,	(float)	$rows_sum_e->CAEvent);
																		?>
																		<tr class="to_highlight">			
																					<td nowrap>
																								<a  href="<?php	echo	"ca_event_detail.php?even_id="	.	$even_id	?>"><?php	echo	$row->EVEN_nom_fr;	?></a>
																					</td>	
																					<td> <?php	echo	$row->NbreJour	?></td>
																					<td> 
																								<?php
																								if	($rows_sum_e->Somme_Event	!=	null)	{
																											echo	$rows_sum_e->Somme_Event	.	" €";
																								}	else	{
																											?>
																											<span class="note"> <?php	echo	$rows_sum_e->Nbre_All_Event	.	" achat";	}	?></span>
																					</td>
																					<td> 
																								<?php
																								if	($rows_sum_e->Somme_Heber	!=	null)	{
																											echo	$rows_sum_e->Somme_Heber	.	" €";
																								}	else	{
																											?>
																											<span class="note"> <?php	echo	$rows_sum_e->Nbre_All_Heber	.	" hébergement";	}	?></span>
																					</td>
																					<td> 
																								<?php
																								if	($rows_sum_e->Somme_Resto	!=	null)	{
																											echo	$rows_sum_e->Somme_Resto	.	" €";
																								}	else	{
																											?>
																											<span class="note"> <?php	echo	$rows_sum_e->Nbre_All_Resto	.	" restauration";	}	?></span>
																					</td>
																					<td> <B><?php	echo	$rows_sum_e->CAEvent	.	" €"	?></B></td>																										
																		</tr>
															<?php	}	?>
															<?php	$tableau	=	json_encode($tableau);	?>
															<div>										
																		<script type="text/javascript">
																										
																					google.load("visualization", "1", {packages:["corechart"]});
																					google.setOnLoadCallback(drawChart);
																					function drawChart() {
																																				
																								var data = google.visualization.arrayToDataTable(<?php	echo	$tableau	?>);
																								var options = {
																											title: 'Statistique commandes par événement',
																											hAxis: {title: 'Nom événement', titleTextStyle: {color: 'red'}},
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
												echo	('<p class="requete"><b>Requête : </b>'	.	$query_e	.	'</p>');
												echo	('<p class="requete"><b>Requête : </b>'	.	$query_sum_e	.	'</p>');
									}
									?>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>

