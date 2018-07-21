$(document).ready(function() {

			$("#bt_navigation").click(function() {
						$('#menu_popup').toggle();
						$('#bt_navigation').toggleClass('highlight');
			});

			$('#menu_popup').click(function() {
						$('#menu_popup').toggle();
						$('#bt_navigation').toggleClass('highlight');
			});

});


