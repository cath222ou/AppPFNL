//////////////////// Sélection d'un layer	////////////////////////////////////////

	var select = new ol.interaction.Select({
		style: [
			new ol.style.Style({
				stroke: new ol.style.Stroke({
					color: 'white',
					width: 6
					})
				}),
			new ol.style.Style({
				fill: new ol.style.Fill({
					color: 'rgba(255, 255, 255, 0.0)'
					}),
				stroke: new ol.style.Stroke({
					color: '#D10101',
					width: 3
					}),
				text: new ol.style.Text({
					font: '12px Calibri,sans-serif',
					fill: new ol.style.Fill({
						color: '#000'
						}),
					stroke: new ol.style.Stroke({
						color: '#fff',
						width: 3
						})
					})
				})
			]
		});  // ref to currently selected interaction
    
    var selectClick = new ol.interaction.Select({
      condition: ol.events.condition.click
    });

    var selectPointerMove = new ol.interaction.Select({
      condition: ol.events.condition.pointerMove
    });

    map.addInteraction(selectPointerMove);
    map.addInteraction(selectClick);

	map.addInteraction(select);
	var selectedFeatures = select.getFeatures();
	
	
	// Pour changer le curseur lorsqu'il se positionne par-dessus une couche sélectionnable
	var target = map.getTarget();
    var hit;
        var jTarget = typeof target === "string" ? $("#" + target) : $(target);
        // change mouse cursor when over marker
        $(map.getViewport()).on('mousemove', function (e) {
            var pixel = map.getEventPixel(e.originalEvent);
            hit = map.forEachFeatureAtPixel(pixel, function (feature, layer) {
                return true;
            });
            if (hit) {
                jTarget.css("cursor", "pointer");
            } else {
                jTarget.css("cursor", "");
            }
        });