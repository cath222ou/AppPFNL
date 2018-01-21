$(function () {
	
	// Le id #start_outils permet de déclancher les outils et composantes. Voir le lien dans le html
	// L'affichage des popups est enlevé.
	$('#start_outils').click(function () {
		// Create windows with JQUERY-UI Dialog 
		$(function() {
			$( "#dialog4" ).dialog({
				position: {
					my: "left+30 bottom-50",
					at: "left bottom",
					of: window
				},
				height: 220,
                width: 360
			})
		});
		// Remove interaction whit the close button of JQUERY-UI Dialog 
		$('#dialog4').bind('dialogclose', function(event) {
	           
		 });
		 

	});
});