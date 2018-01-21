/** * Elements that make up the popup. */

var container = document.getElementById('popup');
var content = document.getElementById('popup-content');
var closer = document.getElementById('popup-closer');

/** * Add a click handler to hide the popup. * @return {boolean} Don't follow the href. */ 
closer.onclick = function() {
	overlay.setPosition(undefined);
	closer.blur();
	return false;
};

/** * Create an overlay to anchor the popup to the map. */
var overlay = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
	element: container,
	autoPan: true,
	autoPanAnimation: {
		duration: 250
	}
}));
map.addOverlay(overlay)

/** * Add a click handler to the map to render the popup. */
/** * L'information des poissons et de l'herpetofaune sont contenu dans deux fichier javascript voir la déclaration dans le HTML */
/** * layer_name pour distinguer les couches qui seront interroger. */
map.on('click', function(evt) {
	var layer_name = [];
	var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
		// layer_name.push(layer.get("title"));
		return feature;
	});
	
	// Condition pour distinguer la couche et permettre de formater en fonction de son contenu
	if (feature) {
		
			var coordinate = evt.coordinate;
			var hdms = ol.coordinate.toStringHDMS(ol.proj.transform(coordinate, 'EPSG:3857', 'EPSG:4326'));
			
			content.innerHTML =  
				"<h3>Analyse IQH</h3>" +
				"<ul class='nav nav-tabs'>"+
					"<li class='active'><a data-toggle='tab' href='#tab_legende'>Légende</a></li>"+
					"<li><a data-toggle='tab' href='#data'>Données</a></li>"+
                    "<li><a data-toggle='tab' href='#geog'>Géographie</a></li>"+
				"</ul>"+
				"<div class='tab-content'>"+
					"<div id='tab_legende' class='tab-pane fade in active'></div>"+
					"<div id='data' class='tab-pane fade'></div>"+
                    "<div id='geog' class='tab-pane fade'></div>"+
				"</div>"
			
                $("#tab_legende").append("<li><b>Potentiels de présence</b></li><br>")
                $("#tab_legende").append("<li><div class='container'>"+
                                         "<li>"+
                                         "<div class='row'>"+
                                         "<div class='col-md-6'>"+
                                         "<div class='chart-legend clearfix' id='js-legend' height='60'></div> "+
                                         "</div>"+
                                         "<div class='col-md-6'>"+
                                         "<div class='chart-responsive'><canvas id='pieChart' width='100' height='60'></canvas></div>"+
                                         "</div></div>"+
                                         "</li>"+
                                          "<li><br>"+
                                         "<div class='row'>"+
                                         "<div class='chart-legend' id='occupy' height='40'>"+
                                         "<ul>"+
                                         "<li><p>20%</p><span style='background-color:rgba( 254, 229, 217, 0.5 );'></span></li>"+
                                         "<li><p>40%</p><span style='background-color:rgba( 252, 174, 145, 0.5 );'></span></li>"+
                                         "<li><p>60%</p><span style='background-color:rgba( 251, 106, 74, 0.5 );'></span></li>"+
                                         "<li><p>80%</p><span style='background-color:rgba( 222, 45, 38, 0.5 );'></span></li>"+
                                         "<li><p>100%</p><span style='background-color:rgba( 165, 15, 21, 0.5 );'></span></li>"+
                                         "</ul>"+
                                         "</div>"+
                                         "</div>"+
                                          "</li>"+
                                         "</div></li>");
      
        
			/*
				$("#legende").append("<li><b>Activity: </b>"+feature.get("Activity")+"</li><br>")
                $("#legende").append("<li><b>Trail Type: </b>"+feature.get("TrailType")+"</li><br>")
                $("#legende").append("<li><b>Environnement: </b>"+feature.get("Environ_EN")+"</li><br>")
                $("#legende").append("<li><b>Lenght (km): </b>"+feature.get("Len_Lon_KM")+"</li>")
              */  
                $("#data").append("<li><b>Production: </b>"+feature.get("Production")+"</li><br>")
                $("#data").append("<li><b>Élevé: </b>"+feature.get("Eleve")+"</li><br>")
                $("#data").append("<li><b>Moyen: </b>"+feature.get("Moyen")+"</li><br>")
                $("#data").append("<li><b>Faible: </b>"+feature.get("Faible")+"</li><br><br>")
                $("#data").append("<li><i>Les données sont en % du territoire </i></li><br><br>")
                
                $("#geog").append("<li><b>En cours de développement</li><br>")
                
                
                
			overlay.setPosition(coordinate);
			$(".ol-popup").show();
			// Cette fonction est essentiel pour activer le controle des tabs dans le popup. Sinon les tabs ne fonctionne pas
			$(".nav-tabs a").click(function(){
				$(this).tab('show');
			});
        
        var canvas = document.getElementById( "pieChart" ); 
        var ctx = canvas.getContext( "2d" );
        
        var pieData = [ 
                { value: 0, color: "rgba( 204, 76, 2, 0.7 )", highlight: "#CC4C02", label: "Élevé" }, 
                { value: 0, color: "rgba( 254, 153, 41, 0.7 )", highlight: "#FE9929", label: "Moyen" }, 
                { value: 0, color: "rgba( 254, 217, 142, 0.7 )", highlight: "#FED98E", label: "Faible" }, 
                //{ value: 100, color: "#FEF3DD", highlight: "#FEF3DD", label: "Null" }
            ]; 
        var pieOptions = { 
            segmentShowStroke: true, 
            animationEasing: "easeOutQuart", 
            animationStep: 100, 
            animateRotate: true,
            animateScale: false,
            percentageInnerCutout: 50, 
            //tooltipTemplate: "<%= label %> - <%= value %>%"
        }
        var myChart = new Chart( ctx ); 
        var pieChart = myChart.Doughnut( pieData, pieOptions ); 

        $( ".chart-legend#js-legend" ).append( pieChart.generateLegend() ); 
        pieChart.destroy(); 
        pieChart = myChart.Doughnut( [], pieOptions );
        
        var myData = [ 
          { value: feature.get( "Eleve" ), color: "#CC4C02", highlight: "#CC4C02", label: "Élevé" }, 
          { value: feature.get( "Moyen" ), color: "#FE9929", highlight: "#FE9929", label: "Moyen" }, 
          { value: feature.get( "Faible" ), color: "#FED98E", highlight: "#FED98E", label: "Faible" }, 
          //{ value: feature.get( "Absent" ), color: "#FEF3DD", highlight: "#FEF3DD", label: "Null" }
        ];

        pieChart.destroy(); 
        pieChart = myChart.Doughnut( myData, pieOptions );
		
	}
	else {
		/*document.getElementById('info').innerHTML = '';
		var viewResolution = * @type {number}  (view.getResolution());
		var url = etendues_eaux_lacs.getSource().getGetFeatureInfoUrl(
			evt.coordinate, viewResolution, 'EPSG:3857',
			{'INFO_FORMAT': 'text/html'}
		);
		console.log(url)
		if (url) {
			document.getElementById('info').innerHTML =
			'<iframe seamless src="' + url + '"></iframe>';
		}*/
	}
		
	
});

// map.on('click', function(evt) {
  // document.getElementById('info').innerHTML = '';
  // var viewResolution = /** @type {number} */ (view.getResolution());
  // var url = wmsSource.getGetFeatureInfoUrl(
      // evt.coordinate, viewResolution, 'EPSG:3857',
      // {'INFO_FORMAT': 'text/html'});
	  // console.log(url)
  // if (url) {
    // document.getElementById('info').innerHTML =
        // '<iframe seamless src="' + url + '"></iframe>';
  // }
// });

map.on('singleclick', function(evt) {
  var pixel = map.getEventPixel(evt.originalEvent);
  map.forEachLayerAtPixel(pixel, function(layer) {
    return true;
	console.log(layer)
  });
  map.getTargetElement().style.cursor = hit ? 'pointer' : '';
});