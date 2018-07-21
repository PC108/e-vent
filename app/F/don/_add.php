<?php
/*	* ******************************************************** */
/*             Charge la liste des dons                    */
/*	* ******************************************************** */
if	($configAppli['MENU']['don']	==	"oui")	{
		$affiche	=	true;
		$queryDon	=	"SELECT * FROM t_typedon_tydon WHERE TYDON_visible= 1 ORDER BY TYDON_ordre";
		$RSDon	=	mysql_query($queryDon,	$connexion)	or	die(mysql_error());
		$NbreRows_RSDon	=	mysql_num_rows($RSDon);
}	else	{
		$affiche	=	false;
}

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<?php	if	(($affiche))	{	?>
		<div id ="bloc_don" class="corner20-all">
				<h2><?php	echo	_("Faire un don");	?></h2>
				<div>
						<?php	if	((!isset($_SESSION['info_adherent']))	||	($_SESSION['info_adherent']['etat_adh']	<=	2))	{	?>
								<!-- le don est anonyme -->
								<form id="form_don_anonyme" action="../paypal/paypal_anonyme.php" method="post">
										<div style="margin-bottom:10px;">
												<input id="montant_don" name="montant_don" size="8" value="<?php	echo	_("mon don");	?>" class="light default-value"/> €
												<input style="border:0; padding:0;" src="https://www.paypal.com/fr_FR/FR/i/logo/PayPal_mark_50x34.gif" type=image Value=submit align="right"/>
										</div>
										<div></div>
								</form>

						<?php	}	else	{	?>
								<!-- l'utilisateur est identifié -->
								<form id="form_don_cmd">
										<div style="margin:5px 0;"><input id="montant_don" name="montant_don" size="10" value="<?php	echo	_("Mon don");	?>" class="light default-value"/> €</div>
										<?php	if	($NbreRows_RSDon	>	0)	{
												while	($row	=	mysql_fetch_array($RSDon))	{	?>
														<div><input type="radio" id="type_don" name="type_don" value ="<?php	echo	$row['TYDON_id'];	?>" <?php	if	($row['TYDON_id']	==	1)	{	echo	"CHECKED";	}	?>><?php	echo	$row['TYDON_nom_'	.	$langue];	?></div>
												<?php	}	}	?>
								</form>
								<a href="javascript:NS_FRONT.faireUnDon(<?php	echo	$_SESSION['info_beneficiaire']['id_benef'];	?>)"><div class="bt_lien corner10-all espace10"/><?php	echo	_("ajouter à ma commande");	?></div></a>
						<?php	}	?>
		</div>
		</div>
<?php	}	?>