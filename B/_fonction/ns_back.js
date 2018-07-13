// @namespace = NS_FRONT
// @version : 14/06/2011 - Philippe Chevalier
var NS_BACK = {
			/**
				* @description :
				* Initialise le popup qui affiche les commentaires dans les tableaux
				* Cette fonction initialise les événements CLIC
				* AJAX. utilise ajax_getCommentaire.php
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			initPopupCommentaire: function(tableId, pagenum) {

						$('.js_commentaire').has('img').click(function() {
									var idcom = $(this).attr('id');
									var idfiche = $(this).attr('idfiche');
									// AJAX
									if (!$(this).hasClass('isShowingBox')) {
												$('#box_popup').load(
																				'../_fonction/ajax_getCommentaire.php',
																				{
																							id_cmt: $(this).attr('id')
																				},
												function(responseText, textStatus) {
															switch (textStatus) {
																		case 'success':
																					$('#box_popup').append('<p class="note_comment"><a href="../commentaire/add.php?idcom=' + idcom + '&idfiche=' + idfiche + '&table=' + tableId + '&pageNum=' + pagenum + '">Cliquez pour modifier ou supprimer le commentaire.</a></p>');
																					break;
																		case 'error':
																					alert('Erreur ajax.load() vers ajax_getCommentaire.php');
																					break;
															}
												});
									}
									NS_UTIL.displayInfoPopup($(this), $('#box_popup'), [-5, $(this).width() + 4], "box_commentaire");
						});
			},
			/**
				* @description :
				* Initialise le popup qui affiche les logs dans les tableaux
				* Cette fonction initialise les événements CLIC
				* AJAX. utilise ajax_getLog.php
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			initPopupLog: function(obj, tableId) {
						obj.click(function() {
									// AJAX
									if (!$(this).hasClass('isShowingBox')) {
												$('#box_popup').load(
																				'../_fonction/ajax_getLog.php',
																				{
																							id: $(this).attr('id'),
																							table: tableId
																				},
												function(responseText, textStatus) {
															if ((textStatus == "error")) {
																		alert('Erreur ajax.load() vers ajax_getLog.php');
															}
												});
									}
									NS_UTIL.displayInfoPopup($(this), $('#box_popup'), [-5, $(this).width() + 4], "box_log");
						});

			},
			/**
				* @description :
				* Initialise le popup qui affiche les descriptions avec un début de texte
				* Cette fonction initialise les événements CLIC
				* Attention ! Cette fonctionnalité n'utilise pas AJAX.
				* Les descriptions sont chargés en même temps que les résultats du tableau et cachées dans un div #box_description
				* Si over = TRUE, un clic sur la description cache la fenêtre.
				* Si over = FALSE, la fonction est désactivée (par exemple s'il y a un lien cliquable dans la description
				* @lastMAJ : 11/10/2011 - Philippe Chevalier
				*/
			initBoxDescriptions: function(posLeft, posTop, over) {

						// Active la fermeture de la description par clic sur la description (sinon uniqument sur le bouton)
						if (over) {
									$('#box_description').click(function(event) {
												event.stopPropagation(); // Bloque la propagation de l'événement à highlightRow()
												NS_UTIL.hidePopup($(this), "");
									});
						}

						// Ajoute le curseur pointer en rollover de la description
						var addClass = "";
						if (over) {
									addClass = "box_description pointer";
						} else {
									addClass = "box_description";
						}

						// Initialise le clic sur le bouton
						$('.js_description').click(function(event) {
									event.stopPropagation(); // Bloque la propagation de l'événement à highlightRow()
									$('#box_description div').addClass('invisible');
									var divDescription = $('#box_description').find('#' + $(this).attr('id'));
									divDescription.removeClass('invisible');
									// Affiche la description
									NS_UTIL.displayInfoPopup($(this), $('#box_description'), [posLeft, posTop], addClass);
						});
			},
			/**
				* @description :
				* Initialise le bouton permettant d'ouvrir et de fermer une sous table dans un tableau (voir Evenement ou Commande)
				* Cette fonction initialise les événements CLIC
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			initBoutonSousTable: function(obj, nomSession) {

						obj.click(function() { // Ouvre et ferme la sous table
									//
									// AJAX
									$.post("../../librairie/js/code_adn/ajax_setSession.php", {
												multiSessionName: "back-office",
												cle: nomSession,
												valeur: $(this).attr('idParent'),
												action: "toggle"
									}, function(data) {
												// console.log(data);
												// Modif 03/2014
												// reload la page pour afficher les commandes ouvertes
												location.reload();
												// Modif 03/2014
									});
						});
			},
			/**
				* @description :
				* Sauve le nombre de ligne affiché sur un tableau dans un cookie
				* Cette fonction initialise les événements CHANGE
				* AJAX. utilise ajax_setCookie.php
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			saveNbreLigneTableau: function() {
						$('#nbre_ligne').change(function() {
									$.post("../_fonction/ajax_setCookie.php", {
												cle: "nbre_ligne",
												valeur: $(this).val()
									}, function() {
												window.location.href = "http://" + window.location.host + window.location.pathname;
									});
						});
			},
			/**
				* @description :
				* Alerte avant de supprimer.
				* Cette fonction initialise les événements CLICK sur le bouton Supprime
				* @lastMAJ : 27/09/2011 - Philippe Chevalier
				*/
			checkSupprime: function(obj, target) {
						obj.click(function() {
									var mess = "Attention, vous allez supprimer cet enregistrement. \n Confirmez en cliquant sur OK.";
									var okSup = window.confirm(mess);
									var idToDelete = $(this).attr('id_todelete');
									var adresse = target + '&id=' + idToDelete;
									//var adresse=page;
									if (okSup) {
												document.location.href = adresse;
									}
						});
			},
			/**
				* @description :
				* Alerte avant de supprimer.
				* Cette fonction initialise les événements CLICK sur le bouton Supprime all
				* @lastMAJ : 06/03/2014 - Philippe Chevalier
				*/
			checkSupprimeAll: function(obj, target) {
						obj.click(function() {
									var mess = "Attention, vous allez supprimer tout les enregistrements liés à cette commande. \n Confirmez en cliquant sur OK.";
									var okSup = window.confirm(mess);
									var cmdToDelete = $(this).attr('cmd_todelete');
									var adresse = target + '&cmdid=' + cmdToDelete;
									//var adresse=page;
									if (okSup) {
												document.location.href = adresse;
									}
						});
			},
			/**
				* @description :
				* Met en surbrillance la ligne d'un tableau.
				* Cette fonction initialise les événements CLICK
				* @lastMAJ : 12/07/2011 - Philippe Chevalier
				*/
			highlightRow: function() {
						$('.to_highlight').click(function() {
									$(this).find('td').toggleClass("highlight");
						});
			},
			/**
				* @description :
				* Charge en Ajax les infos de l'adhérent dans doublon/compare.php et doublon/fusion.php
				* AJAX. utilise ajax_getInfoAdherent.php
				* identAdh correspond à ADH_identifiant, de type XX_000_XX et non pas l'id de l'adhérent
				* @lastMAJ : 18/04/2014 - Philippe Chevalier
				*/
			showInfoAdherent: function(identAdh, idBloc) {

						$.post("../_fonction/ajax_getInfoAdherent.php", {id_adh: identAdh}, function(data) {
									var infoAdh = $.parseJSON(data);
									console.log(infoAdh);
									$.each(infoAdh, function(key, value) {
												// Gére le CSS de la ligne etat
												if (key === "etat_couleur") {
															$("#info_" + idBloc + " #etat_couleur").css("background-color", "#" + value);
												} else {
															// ou affiche la valeur
															if (value === "") {
																		value = "-";
															}
															$("#info_" + idBloc + " #info_" + key).html(value);
												}
									});
									$("#info_" + idBloc).show();
						});
			}

};