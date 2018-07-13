<!-- AVERTISSEMENT MODE SAISIE -->
<?php	if	(isset($_SESSION['saisie']))	{	?>
			<div id="saisie" class="ui-state-error">
						<p> Attention, vous êtes actuellement en mode SAISIE DIRECTE avec <b><?php	echo$_SESSION['saisie']	?></b> |  
									<a href="../general/logout.php"><div class="bt_close_saisie corner10-all">Terminer la session</div></a></p>
			</div>
<?php	}	?>
<div id="header">
			<!-- IMAGE -->
			<div id="header_img">
						<div id="header_e-""><span class="e-">e-</span>venement.com <?php	echo	_("pour")	.	" "	.	$_SESSION['info_client']['nom'];	?></div>
			</div>
			<!-- IDENTIFICATION -->
			<div id="header_user" class="corner20-bottom">
						<?php	if	(!isset($_SESSION['info_adherent']))	{	?>
									<a href="../evenement/index.php?showid=yes"><div class="bt_lien corner10-all" style="margin-top: -5px"><?php	echo	_("S'identifier");	?></div></a>
						<?php	}	else	{	?>
									<a href="../general/logout.php?mode=keep"><div class="bt_lien corner10-all" style="margin-top: -5px"><?php	echo	_("se déconnecter");	?></div></a>
									<div style="line-height: 27px; float: left;"><?php	echo	_("Bonjour")	.	" "	.	$_SESSION['info_adherent']['prenom_adh']	.	" <b>"	.	$_SESSION['info_adherent']['nom_adh']	.	"</b>";	?></div>
						<?php	}	?>
			</div>
			<!--    LANGUE -->
			<div id="header_langue" class="corner20-bottom">
						<?php
						switch	($langue)	{
									case	"fr"	:
												$url	=	adn_UpdateValFromUrl("lang=fr",	"lang=en");
												echo	('<u>fr</u> | <a href="'	.	$url	.	'">en</a>');
												break;
									case	"en"	:
												$url	=	adn_UpdateValFromUrl("lang=en",	"lang=fr");
												echo	('<a href="'	.	$url	.	'">fr</a> | <u>en</u>');
												break;
						}
						?>
			</div>
			<!-- MESSAGE -->
			<div>
						<?php
						if	(isset($_SESSION['info_adherent']))	{ // Sinon le message sera affiché dans la boite de dialogue d'inscription
									include('../_messages.php');	}
						?>
			</div>
			<!-- NAVIGATION -->
			<!-- Les URL des liens sont en javascript dans _fonction/ns_front.js -->
			<?php	if	((!isset($_SESSION['info_adherent']))	||	($_SESSION['info_adherent']["etat_adh"]	<=	2))	{	?>
						<div id="navigation">
									<button id="evenement"><?php	echo	_("Les événements");	?></button>
						</div>
			<?php	}	else	{	?>
						<div id="navigation">
									<button id="evenement"><?php	echo	_("Les événements");	?></button>
									<?php	if	($configAppli['MENU']['cotisation']	==	"oui")	{	?>
												<button id="cotisation"><?php	echo	_("Les cotisations");	?></button>
									<?php	}	?>
									<button id="commande"><?php	echo	_("Mes commandes");	?></button>
									<button id="amis"><?php	echo	_("Mes relations");	?></button>
									<button id="info_pers"><?php	echo	_("Mes infos perso.");	?></button>
						</div>
			<?php	}	?>
</div>
<div style="clear: both"></div>