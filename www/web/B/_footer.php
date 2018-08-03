<!-- MENU DE NAVIGATION BACK OFFICE -->
<div id="menu_popup">
		<?php
		// if (!strpos($_SERVER['PHP_SELF'], 'menu')) { // Sauf pour la page menu.php
		foreach	($menuInfos	as	$key	=>	$value)	{
				//Rubrique
				echo	'<div class="menu_popup_bloc">';
				echo	(	'<div>'	.	$key	.	' <img src="../_media/navig_'	.	strtolower($key)	.	'.png" alt=""></div>');
				foreach	($value	as	$sskey	=>	$ssvalue)	{
						if	($ssvalue['class']	!=	"niveau0")	{
								// Vérifie aussi pour les liens de menus configurables
								if	(($ssvalue['check'] == "ok") ||	($configAppli[$ssvalue['check'][0]][$ssvalue['check'][1]] == "oui")) {
										echo	(	'<div class="'	.	$ssvalue['class']	.	'"><a href="'	.	$ssvalue['url']	.	'">'	.	$ssvalue['nom']	.	'</a></div>');
								}

						}
				}
				echo	'</div>';
		}
		// }
		?>
</div>
<?php	echo	adn_afficheSession();	?>