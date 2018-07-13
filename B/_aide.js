$(document).ready(function() {
			
			$('#help').dialog({
						autoOpen: false,
						width: 240,
						position: ['right', 70],
						zIndex: 50
			});
			
			$('.bt_help').click(function() {
						if ($('#help').dialog("isOpen")) {
									$('#help').dialog("close");	
						} else {
									$('#help').dialog("open");	
						}	
			});

});