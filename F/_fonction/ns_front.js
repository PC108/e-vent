// @namespace = NS_FRONT
// @version : 14/06/2011 - Philippe Chevalier
var NS_FRONT = {


			// @description : Initialise la barre de navigation
			initNavigation: function() {
						$('#navigation').buttonset();
						// initialisation des boutons
						$( "#navigation #evenement" ).button({
									icons: {
												primary:'ui-icon-star'
									}
						});
						$( "#navigation #info_pers" ).button({
									icons: {
												primary:'ui-icon-person'
									}
						});
						$( "#navigation #amis" ).button({
									icons: {
												primary:'ui-icon-shuffle'
									}
						});
						$( "#navigation #commande" ).button({
									icons: {
												primary:'ui-icon-cart'
									}
						});
						$( "#navigation #cotisation" ).button({
									icons: {
												primary:'ui-icon-check'
									}
						});

						// liens des boutons
						$('#navigation #evenement').click(function() {
									window.location='../evenement/index.php';
						})
						$('#navigation #info_pers').click(function() {
									window.location='../adherent/add.php';
						})
						$('#navigation #amis').click(function() {
									window.location='../amis/result.php';
						})
						$('#navigation #commande').click(function() {
									window.location='../commande/result.php';
						})
						$('#navigation #cotisation').click(function() {
									window.location='../cotisation/add.php';
						})
			},


			// @description : Initialise les formulaires présents sur toutes les pages du FRONT
			initFormulaires: function(){
						// Rollover des boutons submit
						// Passé en .live pour les boutons qui apparaissent via AJAX
						$('.bt_submit, .bt_delete, .bt_retour, .bt_lien').live("mouseover mouseout",function(event) {
									if ( event.type == "mouseover" ) {
												$(this).addClass("ui-state-hover");
												$(this).css('cursor', 'pointer');
									} else {
												$(this).removeClass("ui-state-hover");
									}
						});

						// Efface le contenu des champs si clic et contenu = valeur par défaut
						// Ajouter la classe "default-value" pour les champs concernés
						$('.default-value').each(function() {
									var default_value = this.value;
									$(this).focus(function() {
												if(this.value == default_value) {
															this.value = '';
															$(this).css('color', '#00cc00');
												}
									});
									$(this).blur(function() {
												if(this.value == '') {
															$(this).css('color', '#99ccff');
															this.value = default_value;
												}
									});
						})
						// http://www.electrictoolbox.com/jquery-change-default-value-on-focus/


						// Initialise la fonctionnalité de validation qui définit la valeur par défaut du champ
						jQuery.validator.addMethod("valeurDefaut", function(value, element) {
									return this.optional(element) || (value != element.defaultValue);
						})
						// Formulaire d'identification
						$('#form_identification').validate({
									rules: {
												'user': {
															required: true,
															valeurDefaut: true,
															minlength: 2
												},
												'pwd': {
															required: true,
															minlength: 1
												},
												'email_lostpwd': {
															required: true,
															valeurDefaut: true,
															email: true
												}
									}
						});

						// Formulaire newsletter
						$('#form_newsletter').validate({
									rules: {
												'email': {
															required: true,
															valeurDefaut: true,
															email: true
												}
									}
						});

						// Formulaire don
						$('#form_don_cmd, #form_don_anonyme').validate({
									rules: {
												'montant_don': {
															required: true,
															valeurDefaut: true,
															number: true
												}
									}
						});

			},

			/**
			* Ajoute un don dans la commande
			* Envoyé depuis F/don/_add.php
			*/
			faireUnDon: function(idBeneficiaire) {
						if ( $("#form_don_cmd").valid()) {
									var typeDon = 1; //Sans objet particulier
									if ($('input[name=type_don]:checked').val() != null) {
												typeDon = $('input[name=type_don]:checked').val();
									}
									// AJAX
									var objData = {
												action: 'add',
												FK_ADH_id: idBeneficiaire,
												FK_TYDON_id: typeDon,
												FK_TYACH_id: 2,
												ACH_montant: $('#montant_don').val()
									};
									// Recharge la page si on est sur /commande/result.php
									if ((window.location.pathname).indexOf("/commande/result.php") > -1) {
												NS_FRONT.updateCommande(objData, true);
									} else {
												NS_FRONT.updateCommande(objData, false);
									}
						}
			},

			/**
			* Ajoute une cotisatoion à la commande
			* Envoyé depuis F/cotisation/add.php
			*/
			payerCotisation: function(idBeneficiaire) {
						var typeCotisation = $('#select_type_cotisation').val();
						typeCotisation = typeCotisation.split('-');
						// AJAX
						var objData = {
									action: 'add',
									FK_ADH_id: idBeneficiaire,
									FK_TYCOT_id:typeCotisation[0],
									FK_TYACH_id: 1,
									ACH_montant: typeCotisation[1]
						};
						NS_FRONT.updateCommande(objData, false);
			},

			/**
	* @description :
	* Ajoute ou enlève un achat dans la commande via AJAX
	* Passe l'information reload à NS_DIALOG.showFastMessage pour le cas de la modification d'un don sur la page commande/result
	* @lastMAJ : 21/07/2011 - Philippe Chevalier
	*/
			updateCommande : function(objData, reload) {

						// Ouvre en premier la boite de dialogue
						NS_DIALOG.openDialog($('#dialog_confirmachat'));
						$('#dialog_confirmachat').load('../_loading.php #loading_dialog');
						NS_DIALOG.reload = reload; // Rafraichit la page à la fermeture si True

						// AJAX
						$.post("../_fonction/ajax_updateCmd.php",
									objData,
									function(data){
												// NS_DIALOG.showFastMessage($('#box_confirmachat'), data, reload); //Remplacé par l'ouverture d'un boite de dialogue modale
												$('#dialog_confirmachat').html(data);
												$('#totalfromboxcmd').html($('#totalfromboxachat').html());
									});
			}

};