var sourceMeasure = new ol.source.Vector();
var vectorMeasure = new ol.layer.Vector({
  source: sourceMeasure,
  style: new ol.style.Style({
	fill: new ol.style.Fill({
	  color: 'rgba(255, 255, 255, 0.2)'
	}),
	stroke: new ol.style.Stroke({
	  color: '#ffcc33',
	  width: 2
	}),
	image: new ol.style.Circle({
	  radius: 7,
	  fill: new ol.style.Fill({
		color: '#ffcc33'
	  })
	})
  })
});	
map.addLayer(vectorMeasure);

var overlay = new ol.Overlay(/** @type {olx.OverlayOptions} */ ({
  autoPan: true,
}));

$(function () {
	
	// Le id #start_mesure permet de déclancher l'outil de mesure. Voir le lien dans le html
	// L'affichage des popups est enlevé.
	$('#start_mesure').click(function () {
		map.removeOverlay(overlay)
		// Create windows with JQUERY-UI Dialog 
		$(function() {
			$( "#dialog" ).dialog({
				position: {
					my: "left+10 bottom-35",
					at: "left bottom",
					of: window
				},
				maxHeight: 140
			})
		});
		// Remove interaction whit the close button of JQUERY-UI Dialog 
		$('#dialog').bind('dialogclose', function(event) {
			 map.removeInteraction(draw);
			 map.addOverlay(overlay)
			 overlay.setPosition(undefined);
			 sourceMeasure.clear()
		 });
		
		
		// Le code pour l'outil de mesure
		/** * Currently drawed feature * @type {ol.Feature} */
		var sketch;
		/** * Element for currently drawed feature * @type {Element} */
		var sketchElement;
		/** * handle pointer move * @param {Event} evt */
		var mouseMoveHandler = function (evt) {
			if (sketch) {
				var output;
				var geom = (sketch.getGeometry());
				if (geom instanceof ol.geom.Polygon) {
					output = formatArea( /** @type {ol.geom.Polygon} */ (geom));
					} 
				else if (geom instanceof ol.geom.LineString) {
					output = formatLength( /** @type {ol.geom.LineString} */ (geom));
					}
				sketchElement.innerHTML = output;
			}
		};

		$(map.getViewport()).on('mousemove', mouseMoveHandler);

		var typeSelect = document.getElementById('type');

		var draw; // global so we can remove it later
		function addInteraction() {
			var type = (typeSelect.value == 'area' ? 'Polygon' : 'LineString');
			draw = new ol.interaction.Draw({
				source: sourceMeasure,
				type: /** @type {ol.geom.GeometryType} */(type)
			});
			map.addInteraction(draw);

			draw.on('drawstart',
				function (evt) {
					// set sketch
					sketch = evt.feature;
					sketchElement = document.createElement('li');
					var outputList = document.getElementById('measureOutput');

					if (outputList.childNodes) {
						outputList.insertBefore(sketchElement, outputList.firstChild);
					} else {
						outputList.appendChild(sketchElement);
					}
				}, this);

			draw.on('drawend',
				function (evt) {
					// unset sketch
					sketch = null;
					sketchElement = null;
				}, this);
		}


		/** * Let user change the geometry type. * @param {Event} e Change event. */
		typeSelect.onchange = function (e) {
			map.removeInteraction(draw);
			addInteraction();
		};

		var wgs84Sphere = new ol.Sphere(6378137);
		/** * format length output * @param {ol.geom.LineString} line * @return {string} */
		var formatLength = function(line) {
		  var length;
			var coordinates = line.getCoordinates();
			length = 0;
			var sourceProj = map.getView().getProjection();
			for (var i = 0, ii = coordinates.length - 1; i < ii; ++i) {
			  var c1 = ol.proj.transform(coordinates[i], sourceProj, 'EPSG:4326');
			  var c2 = ol.proj.transform(coordinates[i + 1], sourceProj, 'EPSG:4326');
			  length += wgs84Sphere.haversineDistance(c1, c2);
			}
		  var output;
		  if (length > 1000) {
			output = (Math.round(length / 1000 * 100) / 100) +
				' ' + 'km';
		  } else {
			output = (Math.round(length * 100) / 100) +
				' ' + 'm';
		  }
		  return output;
		};


		/** * format length output * @param {ol.geom.Polygon} polygon * @return {string} */
		var formatArea = function(polygon) {
		  var area;
			var sourceProj = map.getView().getProjection();
			var geom = /** @type {ol.geom.Polygon} */(polygon.clone().transform(
				sourceProj, 'EPSG:4326'));
			var coordinates = geom.getLinearRing(0).getCoordinates();
			area = Math.abs(wgs84Sphere.geodesicArea(coordinates));
		  var output;
		  if (area > 10000) {
			output = (Math.round(area / 1000000 * 100) / 100) +
				' ' + 'km<sup>2</sup>';
		  } else {
			output = (Math.round(area * 100) / 100) +
				' ' + 'm<sup>2</sup>';
		  }
		  return output;
		};

		addInteraction();
	});
});