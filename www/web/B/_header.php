<?php	if	(!strpos($_SERVER['PHP_SELF'],	'login.php'))	{	?>
			<div id="barre_navigation">
						<?php	if	(isset($_SESSION['user_info']))	{	?>
									<div id="userinfo">utilisateur connecté  : <b><?php	echo	$_SESSION['user_info'][0];	?></b> | groupe : <img src="../_media/grp_<?php	echo	$_SESSION['user_info'][1]	?>.gif" width="41" height="14" alt="" align="absmiddle" /></div>
						<?php	}	?>
						<!--NAVIGATION-->
						<?php
						// Remplace le bouton navigation par un bouton "retour"
						if	(strpos($_SERVER['PHP_SELF'],	'commentaire/add.php'))	{	// ADD et MAJ du commentaire
									echo	'<div class="bt_navigation"><a href="../'	.	$repertoire	.	'/result.php?pageNum='	.	$pageNum	.	'">retour</a></div>';
						}	elseif	(strpos($_SERVER['PHP_SELF'],	'jourevenement/add.php'))	{	// ADD et MAJ du jourevent
									echo	'<div class="bt_navigation"><a href="../evenement/result.php?pageNum='	.	$pageNum	.	'">retour</a></div>';
						}	else	if	(strpos($_SERVER['PHP_SELF'],	'achat/add.php'))	{	// MAJ du achat
									echo	'<div class="bt_navigation"><a href="../commande/result.php?pageNum='	.	$pageNum	.	'">retour</a></div>';
						}	else	if	(strpos($_SERVER['PHP_SELF'],	'upload_img/crop.php')	||	strpos($_SERVER['PHP_SELF'],	'upload_img/upload_img.php'))	{	// CROP et UPLOAD des images
									echo	'<div class="bt_navigation"><a href="result.php">retour</a></div>';
						}	else	if	(strpos($_SERVER['PHP_SELF'],	'upload_doc/upload_doc.php'))	{	// UPLOAD des docs
									echo	'<div class="bt_navigation"><a href="result.php?pageNum='	.	$pageNum	.	'">retour</a></div>';
						}	else	if	(strpos($_SERVER['PHP_SELF'],	'ca_mois_detail.php'))	{	// Stats par mois
									echo	'<div class="bt_navigation"><a href="ca_mois_liste.php">retour</a></div>';
						}	else	if	(strpos($_SERVER['PHP_SELF'],	'ca_event_detail.php'))	{	// Stats par event
									echo	'<div class="bt_navigation"><a href="ca_event_liste.php">retour</a></div>';
						}	else	if	(strpos($_SERVER['PHP_SELF'],	'fusion.php'))	{	// doublon/fusion.php
									echo	'<div class="bt_navigation"><a href="compare.php">retour</a></div>';
						}	else	if	(strpos($_SERVER['PHP_SELF'],	'add.php')	||	strpos($_SERVER['PHP_SELF'],	'search.php'))	{	// Tous les autres ADD et SEARCH
									echo	'<div class="bt_navigation"><a href="result.php?pageNum='	.	$pageNum	.	'">retour</a></div>';
						}	else	{
									//Sinon affiche le bouton navigation qui fonctionne en javascript
									echo	'<div id="bt_navigation" class="bt_navigation">navigation</div>';
						}
						?>
			</div>
<?php	}	?>
<h1><img src="../_media/bo_pucetitre.gif" width="30" height="30" align="absmiddle" alt="" /><?php	echo	$titre	?></h1>
<div class="bloc_message"><?php	include('../_messages.php');	?></div>

