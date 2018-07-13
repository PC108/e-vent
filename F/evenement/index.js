// @namespace = INDEX
var INDEX = {

		// classe CSS du div qui va contenir les jours de l'événement
		classBlocData : '.includeAjax_jours',


		/**
     * @description :
     * Initialise le comportement de l'accordéon de la page index.php.
     * Cette fonction initialise les événements CLIC
     * Met à jour l'information du blocs= ouvert dans $_SESSION['lastOpen'].
     * @lastMAJ : 29/06/2011 - Philippe Chevalier
     */
		initAccordeon : function(obj) {
				// CLIC
				obj.click(function() {
						// AJAX
						// envoie l'info de l'événement ouvert en session
						$.post("../../librairie/js/code_adn/ajax_setSession.php", {
								multiSessionName: "front-office",
								cle: "lastOpen",
								valeur: $(this).attr('id'),
								action: "replace"
						});
						// Charge la liste des jours la première fois
						var objInsertJours = $(this).next().find(INDEX.classBlocData)
						if (!objInsertJours.children().hasClass('liste_jours')) {
								// INDEX.getJoursIndex(objInsertJours, $(this).attr('id'));
								EVENEMENT.getJours(objInsertJours, $(this).attr('id'));
						}
				});
		},


		/**
     * @description :
     * Ouvre le bloc de l'accordeon de la page index.php en fonction de la variable de session 'lastOpen'.
     * Recharge les jours pour le bloc ouvert.
     * Cette fonction est lancée après le chargement de la page index.php.
     * @lastMAJ : 14/06/2011 - Philippe Chevalier
     */
		openAccordeon : function(idEvent, obj) {
				if (idEvent != 0) {
						obj.accordion("activate",'#'+idEvent);
						var objEvent = $('.acc_header').filter($('#'+idEvent));
						// AJAX
						// Charge la liste des jours la première fois
						var objInsertJours = objEvent.next().find(INDEX.classBlocData);
						if (!objInsertJours.children().hasClass('liste_jours')) {
								// INDEX.getJoursIndex(objInsertJours, idEvent);
								EVENEMENT.getJours(objInsertJours, idEvent);
						}
				}
		},

		/**
     * @description :
     * Initialise les poupup d'information pour les icones case à cocher, hebregement ety restauration dans l'accordéon.
     * Cette fonction initialise les événements HOVER et MOUSEMOVE si on veut que le popup suive les mouvements de la souris en slide = [vertical,horizontal]
			*
     * @lastMAJ : 12/07/2011 - Philippe Chevalier
     */

		initInfoPlusAccordeon : function(obj, texte, decalage, slide) {
				var posDepart = [0,0];
				obj.live('hover', function() {
						$('#box_popup').html(texte);
					  posDepart =  NS_UTIL.displayInfoPopup($(this), $('#box_popup'), decalage, "corner10-top corner10-br");
				});
				obj.live('mousemove', function(e) {
						NS_UTIL.slideInfoPopup(e, $('#box_popup'), decalage, slide, posDepart);
				});
		},

		/**
     * @description : Affiche l'icone Google Maps au dessus des lieux.
     * Inutilisé mais à garder pour script remaplacement chaine de caractère en JS
     * @lastMAJ : 28/09/2011 - Philippe Chevalier
     */
		initIconGmap: function(obj, texte, position) {

				obj.live('hover', function() {
						var reg1=new RegExp("%X%", "g");
						var reg2=new RegExp("%Y%", "g");
						var newTexte = texte.replace(reg1, '<b>'+$(this).attr('restant')+'</b>');
						newTexte = newTexte.replace(reg2, '<b>'+$(this).attr('capacite')+'</b>');
						$('#box_popup').html(newTexte);
						NS_UTIL.displayInfoPopup($(this), $('#box_popup'), position, "corner10-top corner10-br");
				});

		}
};