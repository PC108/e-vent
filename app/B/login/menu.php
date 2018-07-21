<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
include('../_shared.php');

/*	* ******************************************************** */
/*                  Test de connexion                      */
/*	* ******************************************************** */
if	((!isset($_SESSION['user_info']))	||	!in_array($_SESSION['user_info'][1],	array('adher',	'cmd',	'admin',	'event',	'stat')))	{
			$_SESSION['message_user']	=	"accesMenu_ko";
			adn_myRedirection('login.php');
}

/*	* ******************************************************** */
/*                       Titre                             */
/*	* ******************************************************** */
$titre	=	"Back-Office "	.	$_SESSION['info_client']['nom']	.	" : Menu";

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
						<script type="text/javascript" src="../../librairie/js/jquery/external/jquery.cookie.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.core.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.widget.min.js"></script>
						<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.tabs.min.js"></script>
						<script type="text/javascript" src="../_shared.js"></script>
						<script type="text/javascript">
									$(document).ready(function() {
												$("#tabs").tabs({cookie: {expires: 30}});
									});
						</script>
						<!-- CSS -->
						<link type="text/css" href="../_css/jquery-ui.css" rel="stylesheet" />
						<link type="text/css" href="../_css/jquery-override.css" rel="stylesheet" />
						<link type="text/css" href="../_css/style_bo.css" rel="stylesheet"/>
			</head>
			<body>
						<?php	include("../_header.php");	?>
						<div id="contenu">
									<div id="tabs">
												<ul>
															<li><a href="#tabs-1"><span class="uiphil_icontab ui-icon ui-icon-person"></span> ADHERENTS</a></li>
															<li><a href="#tabs-2"><span class="uiphil_icontab ui-icon ui-icon-star"></span> EVENEMENTS</a></li>
															<li><a href="#tabs-3"><span class="uiphil_icontab ui-icon ui-icon-cart"></span> COMMANDES</a></li>
															<li><a href="#tabs-4"><span class="uiphil_icontab ui-icon ui-icon-image"></span> STATISTIQUES</a></li>
															<li><a href="#tabs-5"><span class="uiphil_icontab ui-icon ui-icon-key"></span> CONFIGURATION</a></li>
												</ul>
												<div id="tabs-1">
															<h2>ADHERENTS</h2>
															<h3><a href="<?php	echo	$menuInfos['ADHERENTS']['L1']['url']	?>"><?php	echo	$menuInfos['ADHERENTS']['L1']['nom']	?></a> <img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_adher.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Ajouter et modifier des adhérents. Rechercher des adhérents en ajoutants filtres. Exporter les résultats de la recherche dans un fichier Excel ou dans un fichier CSV pour publipostage.</p>
															<h3><a href="<?php	echo	$menuInfos['ADHERENTS']['L2']['url']	?>"><?php	echo	$menuInfos['ADHERENTS']['L2']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_adher.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Définir et modifier les types de tarifs à associer à chaque adhérent. Rendre le type de tarif visible dans le formulaire d'inscription.</p>
															<h3><a href="<?php	echo	$menuInfos['ADHERENTS']['L3']['url']	?>"><?php	echo	$menuInfos['ADHERENTS']['L3']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_adher.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Voir et modifier les emails associés aux adhérents. Exporter la liste des emails par langue pour un envoi de newsletter.</p>
															<h3><a href="<?php	echo	$menuInfos['ADHERENTS']['L6']['url']	?>"><?php	echo	$menuInfos['ADHERENTS']['L6']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_adher.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Fusionner les adhérents qui se trouvent en doublon.</p>
															<h2>BENEVOLES</h2>
															<h3><a href="<?php	echo	$menuInfos['ADHERENTS']['L4']['url']	?>"><?php	echo	$menuInfos['ADHERENTS']['L4']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_adher.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Définir et modifier la liste des compétences à associer aux adhérents bénévoles. Rendre une compétence visible au niveau du formulaire d'inscription ou seulement dans le back-office.
																		<?php	if	($configAppli['ADHERENT']['benevolat']	==	"non")	{	?><br /><span class="alert">Info : Les fonctions associées aux inscriptions de bénévoles sont désactivées.</span><?php	}	?>
															</p>
												</div>
												<div id="tabs-2">
															<h2>EVENEMENTS</h2>
															<h3><a href="<?php	echo	$menuInfos['EVENEMENTS']['L1']['url']	?>"><?php	echo	$menuInfos['EVENEMENTS']['L1']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_event.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Créer et modifier un événement.</p>
															<h3><a href="<?php	echo	$menuInfos['EVENEMENTS']['L7']['url']	?>"><?php	echo	$menuInfos['EVENEMENTS']['L7']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_event.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Uploader des images à associer ensuite à un événement.</p>
															<h3><a href="<?php	echo	$menuInfos['EVENEMENTS']['L2']['url']	?>"><?php	echo	$menuInfos['EVENEMENTS']['L2']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_event.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Définir des lieux à associer ensuite à un événement.</p>
															<h2>HEBERGEMENT ET RESTAURATION</h2>
															<h3><a href="<?php	echo	$menuInfos['EVENEMENTS']['L3']['url']	?>"><?php	echo	$menuInfos['EVENEMENTS']['L3']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_event.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Créer et modier les types d'hebergement par défaut à associer ensuite à un événement.</p>
															<h3><a href="<?php	echo	$menuInfos['EVENEMENTS']['L4']['url']	?>"><?php	echo	$menuInfos['EVENEMENTS']['L4']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_event.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Modier les types de restauration par défaut à associer ensuite à un événement.</p>
															<h2>DOCUMENTS</h2>
															<h3><a href="<?php	echo	$menuInfos['EVENEMENTS']['L6']['url']	?>"><?php	echo	$menuInfos['EVENEMENTS']['L6']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_event.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Uploader des documents PDF à associer ensuite à un événement pour qu'il soit proposé en téléchargement.</p>
												</div>
												<div id="tabs-3">
															<h2>COMMANDES</h2>
															<h3><a href="<?php	echo	$menuInfos['COMMANDES']['L1']['url']	?>"><?php	echo	$menuInfos['COMMANDES']['L1']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_cmd.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Voir les commandes et le détail des achats. Associer une information de remboursement à un achat.</p>
															<h3><a href="<?php	echo	$menuInfos['COMMANDES']['L2']['url']	?>"><?php	echo	$menuInfos['COMMANDES']['L2']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_cmd.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Consulter les informations retournées par PayPal pour chaque commande payée.</p>
															<h3><a href="<?php	echo	$menuInfos['COMMANDES']['L6']['url']	?>"><?php	echo	$menuInfos['COMMANDES']['L6']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_cmd.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Permet d'accéder à l'application en créant des commandes avec des jours dépassés. Sert pour la saisie pendant ou après les événements.</p>
															<h2>DONS</h2>
															<h3><a href="<?php	echo	$menuInfos['COMMANDES']['L3']['url']	?>"><?php	echo	$menuInfos['COMMANDES']['L3']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_cmd.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Définir les catégories auxquelles l'adhérent pourra associer son don. Par exemple : Offrande, bougies, etc. La liste apparait dans le formulaire de commande au niveau du don.
																		<?php	if	($configAppli['MENU']['don']	==	"non")	{	?><br /><span class="alert">Info : Les fonctions associées aux dons sont désactivées.</span><?php	}	?>
															</p>
															<h2>COTISATIONS</h2>
															<h3><a href="<?php	echo	$menuInfos['COMMANDES']['L4']['url']	?>"><?php	echo	$menuInfos['COMMANDES']['L4']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/><img src="../_media/grp_cmd.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Définir les types de cotisations proposées par l'association. par exemple : Individuelle, groupe, bienfaiteur 100, bienfaiteur 200, etc.
																		<?php	if	($configAppli['MENU']['cotisation']	==	"non")	{	?><br /><span class="alert">Info : Les fonctions associées aux cotisations sont désactivées.</span><?php	}	?>
															</p>
												</div>
												<div id="tabs-4">
															<h2>STATISTIQUES DES COMMANDES</h2>
															<h3><a href="<?php	echo	$menuInfos['STATISTIQUES']['L1']['url']	?>"><?php	echo	$menuInfos['STATISTIQUES']['L1']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Statistiques des commandes confirmées groupées par mois avec CA, etc.</p>
															<h3><a href="<?php	echo	$menuInfos['STATISTIQUES']['L2']['url']	?>"><?php	echo	$menuInfos['STATISTIQUES']['L2']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Statistiques des commandes confirmées par événements avec CA, etc.</p>
												</div>
												<div id="tabs-5">
															<h2>ACCES</h2>
															<h3><a href="<?php	echo	$menuInfos['CONFIGURATION']['L1']['url']	?>"><?php	echo	$menuInfos['CONFIGURATION']['L1']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Créer, modifier ou supprimer un accès au back-office.</p>
															<h2>INFO SOCIETE</h2>
															<h3><a href="<?php	echo	$menuInfos['CONFIGURATION']['L2']['url']	?>"><?php	echo	$menuInfos['CONFIGURATION']['L2']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Modifier les informations de la société qui seront utilisées dans l'application.
																		<br />Par exemple : L'adresse postale à afficher sur les commandes ou l'adresse email de contact
															</p>
															<h2>MAINTENANCE</h2>
															<?php	if	($configAppli['MENU']['export_database']	==	"oui")	{	?>
																		<h3><a href="../maintenance/export_database.php">Exporter la base de données</a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/></h3>
																		<p class="note">Exporter la base de données dans un fichier pour une sauvegarde externe.</p>
															<?php	}	?>
															<h3><a href="<?php	echo	$menuInfos['CONFIGURATION']['L3']['url']	?>"><?php	echo	$menuInfos['CONFIGURATION']['L3']['nom']	?></a><img src="../_media/grp_admin.gif" width="41" height="14" alt=""/></h3>
															<p class="note">Supprimer les inscriptions non confirmées depuis plus d'une semaine.</p>
												</div>
									</div>
						</div>
						<?php	include("../_footer.php")	?>
			</body>
</html>
