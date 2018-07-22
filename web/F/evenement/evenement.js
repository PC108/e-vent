// @namespace = EVENEMENT
var EVENEMENT = {
			// classe CSS du div qui va contenir les jours de l'événement
			classBlocData: '.includeAjax_jours',
			// nom de la variable de session qui va stocker l'information des blocs événements ouverts.
			// fonctionne pour la page index.php (une valeur) et pour evenement.php (plusieurs valeurs).
			nomSession: 'lastOpen',
			/**
				* @description :
				* Initialise le comportement du header des blocs événements de la page evenement.php.
				* Cette fonction initialise les événements HOVER et CLIC
				* Met à jour l'information des blocs ouverts dans $_SESSION['lastOpen'].
				* @lastMAJ : 14/06/2011 - Philippe Chevalier
				*/
			initHeadersBlocsEvenement: function(obj) {
						// ROLLOVER
						obj.hover(
														function() {
																	$(this).addClass("ui-state-hover");
														},
														function() {
																	$(this).removeClass("ui-state-hover");
														});
						// CLIC
						obj.click(function() {
									if ($(this).hasClass("ui-state-active")) {
												EVENEMENT.closeBlocEvenement($(this).parent('.blocEvent'));
									} else {
												EVENEMENT.openBlocEvenement($(this).parent('.blocEvent'));
									}
						});
			},
			/**
				* @description :
				* Initialise le comportement de la case à cocher d'un jour événement.
				* Charge les options Hébergement et restauration si nécessaire.
				* Insert ou supprime l'achat dans la base de données via AJAX
				* Cette fonction initialise les événements CHANGE (Ne pas utiliser CLICK qui est incompatible avec IE8 sur les input)
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			initCheckboxJourEvent: function(obj, langue) {
						// Click checkbox Jour
						$(obj).live('change', function() {
									// bloc Jour
									$(this).parent().toggleClass('fd_bleu');
									$(this).parent().find('a').toggleClass('fd_bleu');
									$(this).siblings('.prix').toggleClass('fd_bleu');
									$(this).siblings('.prix').toggleClass('fd_blanc');
									// Attention ! laisser getHebergement et getRestauration après la modification de la classe 'fd_bleu'
									var objInfoJour = $(this).closest('.info_jour');
									var objHeber = objInfoJour.find('.bloc_heber');
									var objResto = objInfoJour.find('.bloc_resto');
									var idJrEvent = $(this).attr('id-jrevent');
									var idBeneficiaire = $(this).attr('id-benef');
									var objData = {};
									if ($(this).is(":checked")) {
												// Sauvegarde le choix Jour evenement dans la commande
												// AJAX
												objData = {
															action: 'add',
															FK_ADH_id: idBeneficiaire,
															FK_JREVEN_id: idJrEvent,
															ACH_montant: $(this).attr('value'),
															ACH_surcout: $(this).attr('surcout'),
															FK_TYACH_id: 3
												};
												// Met à jour la commande et recharge la page si la commande vient d'être initialisée pour afficher le boc de commande'
												NS_FRONT.updateCommande(objData, false);

												// Affiche le bloc Hebergement
												if (objHeber.length) { // Il y a un bloc hebergement
															EVENEMENT.openBlocHebergement(idJrEvent, objHeber, langue);
												}
												// Affiche le bloc Restauration
												if (objResto.length) { // Il y a un bloc restauration
															EVENEMENT.openBlocRestauration(idJrEvent, objResto, langue);
												}
									} else {
												// Supprime le choix Jour evenement dans la commande
												// AJAX
												objData = {
															action: 'delete',
															FK_ADH_id: idBeneficiaire,
															FK_JREVEN_id: idJrEvent,
															FK_TYACH_id: 3
												};
												NS_FRONT.updateCommande(objData, false);
												if (objHeber.length) { // Il y a un bloc hebergement
															objHeber.removeClass('fd_bleuclair fd_bleu');
															objHeber.html('<img class="pointer ic_heber" src="../_media/GEN/hebergement1.png" />');
												}
												if (objResto.length) { // Il y a un bloc restauration
															objResto.removeClass('fd_bleuclair fd_bleu');
															objResto.html('<img class="pointer ic_resto" src="../_media/GEN/restauration1.png" />');
												}
									}

						});
			},
			/**
				* @description :
				* Initialise le comportement des boutons radios d'un hébergement.
				* Insert ou supprime l'achat dans la base de données via AJAX
				* Cette fonction initialise les événements CHANGE (Ne pas utiliser CLICK qui est incompatible avec IE8 sur les input)
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			initRadioHebergement: function(obj) {
						// Info : n'utilise pas la fonction NS_FRONT.updateCommande à cause de la structure imbriquée de la fonction'
						obj.live('change', function() {
									/* Ouvre la boite de dialogue au départ de l'action. Modif 03/2014 */
									NS_DIALOG.openDialog($('#dialog_confirmachat')); // Modif 03/2014
									$('#dialog_confirmachat').load('../_loading.php #loading_dialog');

									var refObj = $(this);
									// Supprime tous les choix pour le groupe de boutons radios
									$.post("../_fonction/ajax_updateCmd.php", {
												action: 'delete',
												FK_ADH_id: refObj.attr('id-benef'),
												FK_JREVEN_id: refObj.attr('id-jreven'),
												FK_TYHEB_id: "IS NOT NULL",
												FK_TYACH_id: 4 // hebergement
									}, function(data1) {
												// Ajoute le nouveau choix si != aucun
												if (refObj.attr('value') != "aucun") {
															$.post("../_fonction/ajax_updateCmd.php", {
																		action: 'add',
																		FK_ADH_id: refObj.attr('id-benef'),
																		FK_JREVEN_id: refObj.attr('id-jreven'),
																		FK_TYHEB_id: refObj.attr('id-heber'),
																		ACH_montant: refObj.attr('montant'),
																		FK_TYACH_id: 4 // hebergement
															}, function(data2) {
																		//NS_DIALOG.showFastMessage($('#box_confirmachat'), data2, false); //Remplacé par l'ouverture d'un boite de dialogue modale
																		$('#dialog_confirmachat').html(data2);
																		$('#totalfromboxcmd').html($('#totalfromboxachat').html());
																		// NS_DIALOG.openDialog($('#dialog_confirmachat')); // Modif 03/2014
															});
												} else {
															//NS_DIALOG.showFastMessage($('#box_confirmachat'), data1, false); //Remplacé par l'ouverture d'un boite de dialogue modale
															$('#dialog_confirmachat').html(data1);
															$('#totalfromboxcmd').html($('#totalfromboxachat').html());
															// NS_DIALOG.openDialog($('#dialog_confirmachat')); // Modif 03/2014
												}
									});
						});

			},
			/**
				* @description :
				* Initialise le comportement des boutons case à cocher d'une restauration.
				* Insert ou supprime l'achat dans la base de données via AJAX
				* Cette fonction initialise les événements CHANGE (Ne pas utiliser CLICK qui est incompatible avec IE8 sur les input)
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			initCheckboxRestauration: function(obj) {

						obj.live('change', function() {
									var objData = {};
									if ($(this).is(":checked")) {
												// Sauvegarde le choix Restauration dans la commande
												// AJAX
												objData = {
															action: 'add',
															FK_ADH_id: $(this).attr('id-benef'),
															FK_JREVEN_id: $(this).attr('id-jreven'),
															FK_TYRES_id: $(this).attr('id-resto'),
															ACH_montant: $(this).attr('value'),
															FK_TYACH_id: 5 // restauration
												};
												NS_FRONT.updateCommande(objData, false);
									} else {
												// AJAX
												objData = {
															action: 'delete',
															FK_ADH_id: $(this).attr('id-benef'),
															FK_JREVEN_id: $(this).attr('id-jreven'),
															FK_TYRES_id: $(this).attr('id-resto'),
															FK_TYACH_id: 5 // restauration
												};
												NS_FRONT.updateCommande(objData, false);
									}
						});
			},
			/**
				* @description :
				* Initialise le comportement des blocs Hebergement et restauration
				* Cette fonction initialise les événements CLICK
				* @lastMAJ : 21/07/2011 - Philippe Chevalier
				*/
			initHeberResto: function(objs, langue) {
						objs.live('click', function() {
									// AJAX
									// Permet d'ouvrir l'option si le jour event a été coché.
									// Le cas de présente lors du chargement de la commande en cours.
									var checkBox = $(this).siblings(".bloc_jour").children('.checkboxJourEvent');
									if (checkBox.attr('checked') == "checked") {
												var idJrEvent = $(this).attr('id-jrevent')
												if ($(this).hasClass('bloc_heber')) {
															EVENEMENT.openBlocHebergement(idJrEvent, $(this), langue);
												} else if ($(this).hasClass('bloc_resto')) {
															EVENEMENT.openBlocRestauration(idJrEvent, $(this), langue);
												}
												// CSS
												$(this).removeClass('fd_bleu fd_bleuclair');
												var listObj = $(this).find("input:checked");
												if (listObj.length == 0) {
															$(this).addClass('fd_bleuclair');
												}
												else if (listObj[0].defaultValue == "aucun") {
															$(this).addClass('fd_bleuclair');
												} else {
															$(this).addClass('fd_bleu');
												}
									}
						});
			},
			/**
				* @description :
				* Ouvre les blocs événements de la page evenement.php en fonction de la variable de session 'lastOpen'.
				* Recharge les jours pour les blocs ouverts
				* Cette fonction est lancée après le chargement de la page evenement.php.
				* @lastMAJ : 14/06/2011 - Philippe Chevalier
				*/
			openBlocEvenement: function(obj) {
						var objBlocEventHeader = obj.children('.blocEvent_header');
						var objInsertJours = obj.find(EVENEMENT.classBlocData);
						// CSS
						objBlocEventHeader.removeClass("ui-state-default corner20-all");
						objBlocEventHeader.addClass("ui-state-active corner20-top");
						// Ouvre et affiche le bloc Evenement
						obj.children('.blocEvent_content').slideDown('fast');
						// AJAX
						var idEvent = obj.attr('id-event');
						// Charge la liste des jours la première fois
						if (!objInsertJours.children().hasClass('liste_jours')) {
									EVENEMENT.getJours(objInsertJours, idEvent);
						}
						// met à jour $_SESSION['lastOpen']
						$.post("../../librairie/js/code_adn/ajax_setSession.php", {
									multiSessionName: "front-office",
									cle: EVENEMENT.nomSession,
									valeur: idEvent,
									action: "add"
						});
			},
			closeBlocEvenement: function(obj) {
						var objBlocEventHeader = obj.children('.blocEvent_header');
						var objBlocEventContent = obj.children('.blocEvent_content');
						// CSS
						objBlocEventHeader.removeClass("ui-state-active corner20-top");
						objBlocEventHeader.addClass("ui-state-default corner20-all");
						// AJAX
						var idEvent = obj.attr('id-event');
						// met à jour $_SESSION['lastOpen']
						$.post("../../librairie/js/code_adn/ajax_setSession.php", {
									multiSessionName: "front-office",
									cle: EVENEMENT.nomSession,
									valeur: idEvent,
									action: "del"
						});
						// Fermr le bloc Evenement
						obj.children('.blocEvent_content').slideUp('fast');
			},
			/**
				* @description :
				* Ouvre le bloc Hebergement d'un jour evenement
				* @lastMAJ : 20/07/2011 - Philippe Chevalier
				*/
			openBlocHebergement: function(idJrEvent, obj, langue) {
						// CSS
						obj.addClass('fd_bleuclair');
						// AJAX
						if (obj.children().is('img')) { // Le bloc n'est pas chargé
									$.ajax({
												type: 'POST',
												async: true,
												url: '../_fonction/ajax_getHebergement.php',
												data: 'TJ_JREVEN_id=' + idJrEvent + '&lg=' + langue,
												dataType: 'html',
												success: function(reponse) {
															obj.html(reponse);
												},
												error: function(XMLHttpRequest, textStatus, errorThrown) {
															var msg = 'Erreur EVENEMENT.openBlocHebergement vers ajax_getHebergement.php \n';
															msg += 'textStatus = ' + textStatus + '\n';
															msg += 'error = ' + errorThrown + '\n';
															alert(msg);
												}
									});
						}
			},
			/**
				* @description :
				* Ouvre le bloc Restauration d'un jour evenement
				* @lastMAJ : 20/07/2011 - Philippe Chevalier
				*/
			openBlocRestauration: function(idJrEvent, obj, langue) {
						// CSS
						obj.addClass('fd_bleuclair');
						// AJAX
						if (obj.children().is('img')) { // Le bloc n'est pas chargé
									$.ajax({
												type: 'POST',
												async: true,
												url: '../_fonction/ajax_getRestauration.php',
												data: 'TJ_JREVEN_id=' + idJrEvent + '&lg=' + langue,
												dataType: 'html',
												success: function(reponse) {
															obj.html(reponse);
												},
												error: function(XMLHttpRequest, textStatus, errorThrown) {
															var msg = 'Erreur EVENEMENT.openBlocRestauration vers ajax_getRestauration.php \n';
															msg += 'textStatus = ' + textStatus + '\n';
															msg += 'error = ' + errorThrown + '\n';
															alert(msg);
												}
									});
						}
			},
			/**
				* @description : Charge les jours d'un événement dans un bloc HTML pour l'insérer dans objInsertJours.
				* Ne charge que la première fois
				* @lastMAJ : 29/06/2011 - Philippe Chevalier
				*/
			getJours: function(objInsertJours, idevent) {

						objInsertJours.load(
														'../_fonction/ajax_getJourEvent.php',
														{
																	FK_EVEN_id: idevent
														});

						// Ai Désativé le message d'erreur de retour car se déclenche si on sort de la page événement avant que tout les jour événements ne soient chargés

						//	objInsertJours.load(
						//	    '../_fonction/ajax_getJourEvent.php',
						//	    {
						//		FK_EVEN_id: idevent
						//	    },
						//	    function(responseText, textStatus) {
						//		if ((textStatus == "error")) {
						//		    alert('Erreur ajax.load() vers ajax_getJourEvent.php');
						//		}
						//	    });

			},
			/**
				* @description : Affiche un icone d'information au dessus des options.
				* Click ouvre une boite de dialogue
				* @lastMAJ : 23/09/2011 - Philippe Chevalier
				*/
			initBtInfoOpt: function(obj, box, decalage, dialog) {

						obj.live('click', function() {
									// Efface la précédente option affichée
									$('.optcontent').addClass('invisible');
									// Affiche les infos correspondantes
									var idinfo = $(this).attr('idinfo');
									$("." + idinfo).removeClass('invisible');
									// Ouvre la boite de dialogue
									NS_DIALOG.openDialog(dialog);
						});

			}

};