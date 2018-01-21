$(function() {
    $( "#menu" ).menu({
      items: "> :not(.ui-widget-header)", visibility:false
    });
	
    $( "#tabs" ).tabs();
	
	$( "#tabs2" ).tabs();
	
	$(function() { 
		$("#assistance-closer" ).click(function() {
			$(".divSplashScreenContainer").hide();
			$("#popup_assistance").hide();
		});
	});
	$("#assistance").click(function(){
		$("#popup_assistance").show();
	});
	
	
	$(function() { 
		$( "#fermer" ).click(function() {
			$( "#tabs2" ).hide();
		});
	});
	
	// pour gèrer l'affichage du popups d'aide et d'information. Sinon à l'ouverture on voyait le contenu sans la mise en forme...
	$(".startsUgly").show();
	
    $("#start_filter").hide();
    $("#start_outils").hide();

	$( "#accordion" ).accordion({
	  heightStyle: "fill"
	});

	//$("#gSignInWrapperOut").hide();
	//$("#gSignInWrapper").hide();
	/*
	$("#gSignInWrapper").click(function(){
		$(".authentication").toggle();
	});
	*/
	$("#gSignInWrapperOut").click(function(){
		$.ajax({
			url:"logout.php",
			success:function(){location.reload();}
			});
	});
    
    $("#gSignInWrapperIQH").click(function(){
           window.open("http://igeomedia.com/~odupras/temp/portail_pfnl/pages/mushroom.html?email=Etienne.Lauzier-Hudon@USherbrooke.ca","_blank");
    });
		
	
    $(function() {
		$( ".draggable" ).draggable();
	});
    
    
	$("#layer21").show();
	$("#layer22").show();
	
  });
  
