<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');
include('_requete_event.php');
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
if	(isset($_GET	['even_id']))	{
			$even_id	=	$_GET['even_id'];
}	else	{
			echo	"Aucun even en GET";
			exit;
}
/*	* ******************************************************** */
/*              Execution des requêtes                     */
/*	* ******************************************************** */

//Execution de la requête QueryNomEvent pour récupérer le nom de l'événement
$query_nom_e	=	QueryNomEvent($connexion,	$even_id);
$RS_nom_e	=	mysql_query($query_nom_e,	$connexion)	or	die(mysql_error());
$rows_nom_e	=	mysql_fetch_object($RS_nom_e);
$nom_event	=	$rows_nom_e->EVEN_nom_fr;

//Execution de la requête mainQueryJourEvent
$query_j	=	mainQueryJourEvent($connexion,	$even_id);
$RS_j	=	mysql_query($query_j,	$connexion)	or	die(mysql_error());
$nbre_rows_j	=	mysql_num_rows($RS_j);

//Execution de la requête QuerySommeJourEvent
$query_sum_e=	QuerySommeJourEvent($connexion,	$even_id);
$RS_sum_e	=	mysql_query($query_sum_e,	$connexion)	or	die(mysql_error());
$rows_sum_e	=	mysql_fetch_object($RS_sum_e);


/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : "	.	$menuInfos['STATISTIQUES']['L2']['nom']	.	" - Détail";
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
									<p>Statistique de l'evenement <b> <?php	echo	$nom_event;	?></b> contenant <b><?php	echo	$nbre_rows_j	?></b> jours événements.</p>
									<p><a href="<?php	echo	"csv_event_detail.php?even_id="	.	$even_id	?>"><img src="../_media/ic_excel.gif" width="38" height="28" border="0" align="absmiddle" alt="exporter"/></a> Cliquez ici pour télécharger le tableau en fichier excel</p>
									<?php	if	($nbre_rows_j	>	0)	{	?>
												<table	 class="table_result" cellspacing="0">
															<tr>
																		<th>Jour événement</th>
																		<th>Total jours événement</th>
																		<th>Total hébergements</th>
																		<th>Total restaurations</th>
																		<th>Chiffre d'affaire</th>
															</tr>	
															<?php
															while	($row_j	=	mysql_fetch_object($RS_j))	{
																		?>
																		<tr class="to_highlight">
																					<td nowrap> <?php	echo	$row_j->date_jevent	.	" "	.	$row_j->LEVEN_nom	?></td>
																					<td>
																								<?php
																								if	($row_j->TotalAchatsEvent	!=	null)	{
																											echo	$row_j->TotalAchatsEvent	.	" €";
																											?>
																											<span class="note"><?php	echo	"pour "	.	$row_j->nbreAchatsEvent	.	" achat(s)";	}	else	{	?></span>	
																											<span class="note"> <?php	echo	$row_j->nbreAchatsEvent	.	" achat";	}	?></span>								
																					</td>
																					<td> 
																								<?php
																								if	($row_j->TotalAchatsHeber	!=	null)	{
																											echo	$row_j->TotalAchatsHeber	.	" €";
																											?>
																											<span class="note"><?php	echo	"pour "	.	$row_j->nbreAchatsHeber	.	" hébergement(s)";	}	else	{	?></span>	
																											<span class="note"> <?php	echo	$row_j->nbreAchatsHeber	.	" hébergement";	}	?></span>	
																					</td>
																					<td>
																								<?php
																								if	($row_j->TotalAchatsResto	!=	null)	{
																											echo	$row_j->TotalAchatsResto	.	" €";
																											?>
																											<span class="note"><?php	echo	"pour "	.	$row_j->nbreAchatsResto	.	" restauration(s)";	}	else	{	?></span>	
																											<span class="note"> <?php	echo	$row_j->nbreAchatsResto	.	" restauration";	}	?></span>
																					</td>
																					<td><B><?php	echo	$row_j->CAJourEvent	.	" €"	?></B></td>
																		<?php	}	?>
															</tr>
															<tr>
																		<td class="td_somme" colspan=1>	Total de l'événement</td>	
																		<td class="td_somme"> <?php	if($rows_sum_e->Somme_Event!=null){
																					echo	$rows_sum_e->Somme_Event	.	" €";?>
																				<span class="note"><?php	echo	"pour "	.	$rows_sum_e->Nbre_All_Event	.	" achat(s)";}else{?></span>
																				<span class="note"><?php	echo	"pour "	.	$rows_sum_e->Nbre_All_Event	.	" achat(s)";}?></span>		
																		</td>
																		<td class="td_somme"><?php if($rows_sum_e->Somme_Heber!=null){	
																					echo	$rows_sum_e->Somme_Heber	.	" €";?>
																					<span class="note"><?php	echo	"pour "	.	$rows_sum_e->Nbre_All_Heber	.	" hébergement(s)";}else{	?></span>
																					<span class="note"><?php	echo	"pour "	.	$rows_sum_e->Nbre_All_Heber	.	" hébergement(s)";	}?></span>
																		</td>
																		<td class="td_somme"><?php	if($rows_sum_e->Somme_Resto!=null){	
																					echo	$rows_sum_e->Somme_Resto	.	" €";?>
																					<span class="note"><?php	echo	"pour "	.	$rows_sum_e->Nbre_All_Resto	.	" restauration(s)";}else{	?></span>
																					<span class="note"><?php	echo	"pour "	.	$rows_sum_e->Nbre_All_Resto	.	" restauration(s)";}	?></span>
																		</td>
																		<td class="td_somme"><?php	echo	$rows_sum_e->CAEvent	.	" €";	?></td>
															</tr>
												</table	>
									<?php	}	?>
									<?php
									if	($_SESSION['afficheSession']	==	2)	{
												echo	('<p class="requete"><b>Requête : </b>'	.	$query_j	.	'</p>');
												echo	('<p class="requete"><b>Requête : </b>'	.	$query_sum_e	.	'</p>');
									}
									?>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
