<?php
/*	* ******************************************************** */
/*              Inclusions des fichiers                    */
/*	* ******************************************************** */
require_once('../../librairie/php/code_adn/Formatage.php');

/*	* ******************************************************** */
/*              Affichage du bloc                    */
/*	* ******************************************************** */
// vérifie dans quelles conditions afficher le bloc Commande
if	(!isset($_SESSION['info_adherent']))	{
			$affiche	=	false;
}	else	{
			$affiche	=	true;
			/* Info sur la commande + total */
			if	(!isset($_SESSION['info_cmd']))	{
						$html	=	"<p>"	.	_("Pour démarrer une nouvelle commande, ajoutez un achat.")	.	"</p>";
						$total	=	"0";
			}	else	{
						$html	=	"<h4>Ref : "	.	$_SESSION['info_cmd']['ref_cmd']	.	"</h4>";
						$html	.=	"<h4>Créée le : "	.	adn_changeFormatDate($_SESSION['info_cmd']['date_cmd'],	'DB_'	.	$langue)	.	"</h4>";
						$total	=	$_SESSION['info_cmd']['total_cmd'];
			}
			/* Total */
			/* Bouton de retour */
			$check	=	strpos($_SERVER['PHP_SELF'],	"commande/result.php");
			if	($check	===	false)	{
						$labelBouton	=	_("voir le détail & payer");
						$chemin	=	"../commande/result.php";
			}	else	{
						$labelBouton	=	_("modifier");
						$chemin	=	"../evenement/evenement.php";
			}
}



/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<?php	if	(($affiche))	{	?>
			<div id ="bloc_commande" class="corner20-top">
						<h2><?php	echo	_("Ma commande");	?></h2>
						<?php	echo$html;	?></h4>
			<a href="<?php	echo	$chemin	?>"><div class="bt_lien corner10-all espace10"><?php	echo	$labelBouton	?></div></a>
			</div>
			<div id ="totalcommande" class="ui-state-active corner20-bottom">
						<div id="totalfromboxcmd" style="font-size: 16px;">Total = <?php	echo	$total;	?> €</div>
			</div>
<?php	}	?>