<!--GENERAL-->
<script type="text/javascript" src="../../librairie/js/jquery/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.button.min.js"></script>

<!--DIALOGUE-->
<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.effects.core.min.js"></script>
<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.position.min.js"></script>
<script type="text/javascript" src="../../librairie/js/jquery/ui/jquery.ui.dialog.min.js"></script>

<!--FORMULAIRE-->
<script type="text/javascript" src="../../librairie/js/jquery/validate/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../librairie/js/jquery/validate/messages_<?php	echo	strtoupper($langue)	?>.js"></script>

<!--ADN NAMESPACE-->
<script type="text/javascript" src="../_fonction/ns_front.js"></script>
<script type="text/javascript" src="../_fonction/ns_dialog.js"></script>
<script type="text/javascript" src="../../librairie/js/code_adn/ns_util.js"></script>

<script type="text/javascript">
			$(document).ready(function() {
						
						// BARRE NE NAVIGATION
						NS_FRONT.initNavigation();
						
						// FORMULAIRE DES WIDGETS
						NS_FRONT.initFormulaires();
						
						// DIALOG INSCRIPTION
						$('#dialog_register').dialog({
									width: 640,
									modal: true,
									autoOpen: false
						});
								
						// DIALOG CONFIRM ACHAT
						// Affiche un message en haut de page pour valider l'action d'achat + le montant de la commande
						// NS_DIALOG.initFastMesssage($('#box_confirmachat'));
						// Init Dialogue qui se trouve dans footer.php. Remplace la commande précédente.
						NS_DIALOG.confirmChemin = '../commande/result.php';
						NS_DIALOG.initConfirmDialog($('#dialog_confirmachat'), ['<?php	echo	_("voir le détail de la commande & payer");	?>','<?php	echo	_("continuer");	?>']);
												
			});
</script>