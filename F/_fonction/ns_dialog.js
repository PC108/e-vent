// @namespace = NS_DIALOG
// @version : 14/06/2011 - Philippe Chevalier
var NS_DIALOG = {
			/**
				* @description :
				* Initialise une boite de dialogue classique type : Confirmer/Annuler
				* Passer le chemin du bouton Confirmer via la variable  NS_DIALOG.confirmChemin avant de lancer NS_DIALOG.openDialog
				* Si reload = true, la page est réactualisée à la fermeture de la boite de dialogue
				* @lastMAJ : 14/07/2011 - Philippe Chevalier
				*/
			confirmChemin: "",
			reload: false, // A désactiver - MODIF 03/2014
			initConfirmDialog: function(objDialogBox, arrayTxtBoutons) {
						objDialogBox.dialog({
									modal: true,
									autoOpen: false,
									width: 'inherit',
									position: "center",
									buttons: [
												{
															text: arrayTxtBoutons[0],
															click: function() {
																		if (NS_DIALOG.confirmChemin != "") {
																					window.location = NS_DIALOG.confirmChemin;
																					NS_DIALOG.confirmChemin = "";
																		} else {
																					$(this).dialog("close");
																		}
															}
												},
												{
															text: arrayTxtBoutons[1],
															click: function() {
																		$(this).dialog("close");
															}
												}],
									create: function(event, ui) {
												// $(this).load('../_loading.php #loading_dialog');
									},
									close: function(event, ui) {
												if (NS_DIALOG.reload) {
															window.location.reload();
															// réinitialise la variable
															NS_DIALOG.reload = false;
												}
												// $(this).load('../_loading.php #loading_dialog');
									}
						});
			},
			/**
				* @description :
				* Reprends la configuration par défaut de initConfirmDialog et permet de modifier/ajouter des options
				* @lastMAJ : 14/07/2011 - Philippe Chevalier
				*/
			initConfirmDialog2: function(objDialogBox, arrayTxtBoutons) {
						NS_DIALOG.initConfirmDialog(objDialogBox, arrayTxtBoutons);
						objDialogBox.dialog("option", 'modal', false);
						objDialogBox.dialog("option", 'position', ['center', 'top']);
			},
			/**
				* @description :
				* Initialise une boite de d'alerte type : Ok
				* @lastMAJ : 14/07/2011 - Philippe Chevalier
				*/
			initAlertDialog: function(objDialogBox, txtBouton) {
						objDialogBox.dialog({
									modal: true,
									autoOpen: false,
									width: 'inherit',
									position: "center",
									buttons: [
												{
															text: txtBouton,
															click: function() {
																		$(this).dialog("close");
															}
												}]
						});
			},
			/**
				* @description :
				* Ouvre les boites de dialogues JQUERU UI
				* @lastMAJ : 14/07/2011 - Philippe Chevalier
				*/
			openDialog: function(objDialogBox) {
						objDialogBox.dialog("open");
			},
			/*****************************************************
				FAST MESSAGE
				*****************************************************/
			/**
				* @description :
				* Initialise un message animé qui s'affiche en descendant du haut du centre de l'écran.
				* Important ! Voir aussi le css correspondant pour que ca fonctionne.
				* Peut servir pour la confirmation d'une action déclenchée en javascript.
				* Le message reste affiché 2,5s puis remonte se cacher
				* Si on passe en rollover sur le message, il reste bloqué en attendant que la souris s'enlève
				* @lastMAJ : 14/07/2011 - Philippe Chevalier
				*/
			positionTopDepart: -170,
			positionTopArrivee: 0,
			initFastMesssage: function(obj) {
						obj.hover(
														function() {
																	$(this).stop(true, true);
														},
														function() {
																	$(this).animate({
																				top: NS_DIALOG.positionTopDepart
																	})
														}
						);
			},
			/**
				* @description :
				* Déclenche la descente du message depuis le haut de la fenêtre.
				* Le message remonte automatiquement après 2,5s
				* Si reload = true, la page est réactualisée à la fin de l'animation.
				* ex : Pour le cas d'un don fait sur lapage commande/result.php
				* @lastMAJ : 14/07/2011 - Philippe Chevalier
				*/
			showFastMessage: function(obj, msg, reload) {
						obj.html(msg);
						obj.stop(true, true); // arréte l'animation si un autre déclenchement arrive avant la fin de l'animation
						obj.animate({
									top: NS_DIALOG.positionTopArrivee
						})
														.delay(2500)
														.animate({
																	top: NS_DIALOG.positionTopDepart
														}, {
																	complete: function() {
																				if (reload) {
																							window.location.reload();
																				}
																	}
														});
			}

}

