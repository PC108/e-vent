<?php
/*	* ******************************************************** */
/*                  Message pour la cotisation                      */
/*	* ******************************************************** */
if	(isset($_SESSION['info_adherent'])	&&	$_SESSION['info_adherent']['etat_adh']	>	2	&&	$configAppli['MENU']['cotisation']	==	"oui")	{
		$affiche	=	true;
		$arrayInfoCotisation	=	checkCotisation();
}	else	{
		$affiche	=	false;
}

/*	* ******************************************************** */
/*              Début code de la page html                 */
/*	* ******************************************************** */
?>
<?php	if	(($affiche))	{	?>
		<div id ="bloc_cotisation" class="corner20-all">
				<h2><img src="<?php	echo	$arrayInfoCotisation[1]	?>"/> <?php	echo	_("Ma cotisation");	?></h2>
				<p><a href="../cotisation/add.php"><?php	echo	$arrayInfoCotisation[2];	?></a></p>
		</div>
<?php	}	?>