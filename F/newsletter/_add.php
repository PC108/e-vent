<div id ="bloc_newsletter" class="corner20-all">
		<h2><?php	echo	_("Lettre d'information");	?></h2>
		<div class="centrer">
				<form id="form_newsletter" action="../newsletter/action.php" method="post">
						<div>
								<input id="email" name="email" size="24" value="<?php	echo	_("email");	?>" class="light default-value"/>
								<input id="chemin" type="hidden" name="chemin" value="<?php	echo	("http://"	.	$_SERVER['HTTP_HOST']	.	$_SERVER['PHP_SELF']);	?>"/>
						</div>
						<div>
								<input type="submit" name="sign-in" id="sign-in" value="<?php	echo	_("s'inscrire");	?>" class="bt_submit corner10-all espace10"/>
								<input type="submit" name="sign-out" id="sign-out" value="<?php	echo	_("se désinscrire");	?>" class="bt_submit corner10-all espace10"/>
						</div>
				</form>
		</div>
</div>
