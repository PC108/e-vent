// @namespace = NS_UTIL
// @version : 14/06/2011 - Philippe Chevalier
var NS_UTIL = {

		// A privilégier au chargement du formulaire (sans animation)
		displayFormOpt:  function (objCheckBox, divToShow){
				if ($(objCheckBox).attr('checked')) {
						$(divToShow).show();
				} else {
						$(divToShow).hide();
				}
		},

		// A privilégier au clic sur la case à cocher (avec animation)
		openFormOpt:  function (objCheckBox, divToShow){
				if ($(objCheckBox).attr('checked')) {
						$(divToShow).slideDown('fast');
				} else {
						$(divToShow).slideUp('fast');
				}
		},

		// Gére l'affichage des popup au clic. Utilise showPopup et hidePopup
		displayInfoPopup: function(objClicked, box, decalage, boxClass) {
				if (objClicked.hasClass('isShowingBox')) {
						box.removeClass(boxClass);
						objClicked.removeClass('isShowingBox');
						var posTop = -500;
						var posLeft = -500;
				} else {
						$('.isShowingBox').removeClass('isShowingBox');
						objClicked.addClass('isShowingBox');
						box.removeClass(boxClass);
						box.addClass(boxClass);
						var posTop = objClicked.offset().top + decalage[0];
						var posLeft = objClicked.offset().left + decalage[1] ;
				}
				NS_UTIL.showPopup(box, false, posTop,  posLeft);
				return [posTop, posLeft];
		},
		
		slideInfoPopup: function(e, box, decalage, slide, posDepart) {
				var posTop = posDepart[0];
				if(slide[0] == "yes") {
						posTop = e.pageY + decalage[0];
				}
				var posLeft = posDepart[1];
				if(slide[1] == "yes") {
						posLeft = e.pageX + decalage[1];
				}
				NS_UTIL.showPopup(box, false, posTop,  posLeft);
		},

		showPopup: function(box, texte, posTop, posLeft) {
				if (texte) {
						box.html(texte);
				}
				box.offset({
						top:posTop,
						left:posLeft
				});
		},

		hidePopup: function(box, texte) {
				if (texte) {
						box.html(texte);
				}
				box.offset({
						top:-500,
						left:-500
				});
		},
    
		var_dump: function(obj) {
				var out = '';
				for (var i in obj) {
						out += i + ": " + obj[i] + "\n";
				}
				alert(out);
		}

}
