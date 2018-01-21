/**

  author: Olivier Dupras-Tessier
  copyright: (c) 2015-2016 Olivier Dupras-Tessier
  contact: olivier.dupras.tessier@gmail.com
  last-modified: 2016-04-25@21:45:00 UTC/GMT -4 hours 

	This page support different type of ol.interaction on feature to: 
	- Draw; 
	- Measure; 
	- Modify; 
	- Display information.  

	It's also define three ol.vector.layer: 
	- Draw; 
	- Measure; 
	- GeoMarker. 

	It's contain filter to define which intercation are support by which feature
	It's also support a lot of function needed to event on feature interaction
	For example, updateMarkerPanel() define the way the information panel is use to show after the selection of a type of layer.   

*/



/**
 * Currently drawed feature
 * @type { ol.Feature }
 */
var sketch; 
//var fid=0; 
var interaction = { draw:"", measure:"", select:"", modify:"" }; 
var vector = { 

	// Draw map layer vector
	draw: new ol.layer.Vector( { 
		name: "draw", 
  	source: new ol.source.Vector(),
  	style: function ( feature ) {
      if ( !feature.getProperties().projectcolor ) {
      	var hex = "#ffffff"; 
      } else { 
      	var hex = ( !feature.getProperties().featurecolor ) ? feature.getProperties().projectcolor : feature.getProperties().featurecolor; 
      } 
      var rgba = hex2rbga( hex, 0.2 ); 
      var yiq = getContrastYIQ( hex, "rgba" ); 
  		var label = ( !feature.get( "name" ) ) ? " " : feature.get( "name" ); 
  		var styles = setTestStyle( rgba, hex, yiq, label ); 

  		return styles[ feature.getProperties().status ]; 
  	}
	} ), 

	// Measure map layer vector
	measure: new ol.layer.Vector( { 
		name: "measure", 
  	source: new ol.source.Vector(),
	} ), 

	// Geo Marker map layer vector
	geoMarker: new ol.layer.Vector( { 
		name: "geomarker", 
  	source: new ol.source.Vector(),
  	style: new ol.style.Style( { 
	    image: new ol.style.Icon( ( { 
	      anchor: [0.5, 32],
	      anchorXUnits: 'fraction',
	      anchorYUnits: 'pixels',
	      opacity: 0.75,
	      src: 'dist/img/paomedia/map-marker.png'
	    } ) )
	  } ) 
	} )

}; 

for ( key in vector ) { 
	map.addLayer( vector[key] ); 
}


/** handle pointer move **/
$( map.getViewport() ).on( 'mousemove', function( evt ) { 
  if ( sketch ) { 
		var geom = ( sketch.getGeometry() ); 
		var output = validOutput( geom ); 
		( sketch.get( 'source' ) ) ? info.tooltip().attr( 'data-original-title', output.value ) : $( ".box#feature-info tr#geom td#info" ).html( output.value ); 
  } 
} );


/**
 * Start measuring by drawning features
 * @input { interaction_type } select, modify
 * @return {  }
 */
function addMeasureInteraction( type, source ) { 
  interaction.measure = new ol.interaction.Draw( { 
    source: source,
    type: /** @type { ol.geom.GeometryType } */ ( type )
   } );
  map.addInteraction( interaction.measure );

  interaction.measure.on( 'drawstart',
		function( evt ) { 

			// Remove the last feature create in measure vector
			try { 
				source.getSource().clear()
			} catch( err ) { 
				console.log( err )
			} 

			// Get feature create at the event beginning
			sketch = evt.feature; 
			sketch.set( 'source', 'measure' ); 
        
			// Set the tooltip position
			map.on( 'pointermove', function( evt ) { 
				displayFeatureInfo( evt.pixel );
			} );
							
		}, this ),  

  interaction.measure.on( 'drawend',
		function( evt ) { 
			map.on( 'pointermove', function( evt ) { 
				info.tooltip( 'hide' );
			} ); 
			sketch=null; 
			try { 
				source.getSource().clear(); 
			} catch( err ) { 
				console.log( err ); 
			}
		}, this );

}; 


/**
 * Start drawing features
 * @input { interaction_type } select, modify
 * @return {  }
 */
function addDrawInteraction( type, layer, source, fid ) { 
  interaction.draw = new ol.interaction.Draw( { 
    source: source,
    type: ( type )
   } );
  map.addInteraction( interaction.draw ); 

	interaction.draw.on( 'drawstart', 
		function( evt ) {  
			fid+=1; 
			var feature = evt.feature; 
			var color = rgb2hex( $( ".project-table-info tr td#pType span").css( "background-color" ) );
			feature.setId( fid ); 
			feature.setProperties( { status: "defaut", projectcolor: color, name: "" } ); 
			sketch = feature; 
	 }, this );  

	interaction.draw.on( 'drawend', 
		function( evt ) {  
			var feature=evt.feature; 
			sketch = null; 
			updateMarkerPanel( feature ); 
	 }, this ); 

 }; 


/**
 * Start interaction with drawned features
 * @input { interaction_type } select, modify
 * @return {  }
 */
function addFeatureInteraction( type, name ) {  
	interaction.select = new ol.interaction.Select( { 
  	wrapX: false, 
  	layers: function ( layer ) {
  		return layer.get( "name" ) == name; 
  	}, 
  	/* feature: function ( feature, layer ) {
  		return !feature.getProperties().hasOwnProperty( "Production" );
  	} */
	} ); 
	map.addInteraction(  interaction.select  ); 

	// on select event
	var collection = interaction.select.getFeatures(); 
	collection.on( 'add', 
		function( evt ) { 
			var feature = evt.element;  
			if ( feature.getProperties().hasOwnProperty( "Production" ) ) { // Restricts the selection of any thematic layers 
				interaction.select.getFeatures().clear(); 
			} else {			
				sketch = feature; 
				
				$( "button#delete-feature" ).prop( "disabled", false ); 
				if ( feature.getProperties().status == "defaut" ) {
					$( ".check[name='defaut']" ).fadeIn(); 
				} else {
					$( ".check#label" ).fadeIn();
					if ( feature.getProperties().status == "validation" ) {
						$( ".check[name='validation']" ).fadeIn(); 
					}
				}

				updateMarkerPanel(  feature  ); 
			}
	}, this ); 

	collection.on( 'remove', 
		function( evt ) { 
			sketch=null; 
			resetMarkerPanel(); 
	}, this ); 

	if ( type == 'modify' ) { 
		interaction.modify = new ol.interaction.Modify( { 
  		features: collection
		} );
		map.addInteraction( interaction.modify ); 
	} 
}


/**
 * Start the geolocation function from your device
 * @params { --- }
 * @return { --- }
 */
var watchID; 
function addPosition( type, layer, source, fid ) {

	if ( type == "Point" ) {
		getCurrentPosition( source, fid );

	}	else if ( type == "LineString" ) {
		var btn = $( ".btn-box-tool#position[name='LineString']" ); 

		if ( btn.attr( "fid" ) === undefined ) {
			var icon = $( "<i/>" ).addClass( "fa fa-pause" ); 
			var stop = $( "<button/>" ).addClass( "btn btn-box-tool" ) // Create new button
				.attr( { "id": "stop-position", "rel": "tooltip", "title": "Arrêter" } ) // add attributes
				.append( $( "<i/>").addClass( "fa fa-stop" ) ) // append
				.insertAfter( btn ); 

			$( "button.btn-box-tool#stop-position" ).on( "click", stopPosition ); 
			btn.attr( { "fid": fid+=1, "status": "play", "title": "Suspendre" } ); 
			indentDiv.replaceWith( { last: btn.children( "i.fa" ), html: icon } ); 
			startTracking( source, fid ); 

		} else {

			if ( btn.attr( "status" ) == "play" ) { 
				var icon = $( "<i/>" ).addClass( "fa fa-play" ); 
				btn.attr( { "status": "pause", "title": "Reprendre" } ); 
				indentDiv.replaceWith( { last: btn.children( "i.fa" ), html: icon } ); 

			} else if ( btn.attr( "status" ) == "pause" ) { 
				var icon = $( "<i/>" ).addClass( "fa fa-pause" ); 
				btn.attr( { "status": "play", "title": "Suspendre" } ); 
				indentDiv.replaceWith( { last: btn.children( "i.fa" ), html: icon } ); 

			}

		}

		btn.tooltip( "hide" ) 
			.tooltip( "fixTitle" )
			.tooltip( "show" );

	}

} /* addPosition() */



/**
 * Stop watchPostion from your device
 * @params { --- }
 * @return { --- }
 */
function stopPosition() {
	var btn = $( ".btn-box-tool#position[name='LineString']" ); 
	var icon = $( "<i/>" ).addClass( "fa fa-location-arrow" ); 

	btn.attr( { "status": "stop", "title": "Suivi" } )
		.removeAttr( "fid" )
		.tooltip( "hide" )
		.tooltip( "fixTitle" ); 
	$( this ).tooltip( "hide" ); 
	indentDiv.replaceWith( { last: btn.children( "i.fa" ), html: icon } ); 
	indentDiv.remove( { node: $( this ) } ); 

} /* stopPosition() */


/**
 * Select tool from the nav-sidebar
 * @params { elem } li.btn-wrapper
 */
function drawFeature( elem ) { 
	var class_name = ( $( elem ).is( "button" ) ) ? $( elem ).attr( "id" ) : $( elem ).parent().attr( "id" ); 
	var action_type = $( elem ).attr( "name" ); 
	var sidebar = $( ".control-sidebar" ); 
	var panel = sidebar.children( ".tab-content" ); 
	var layer = layer2draw(); 
	var source = getLayerSource( layer.layer ); 

	if ( class_name != "measure" ) var fid = getXtrmFid( source ); 
	stopInteraction( class_name, 'none' ); 
	removeInteraction(); 

	if ( class_name!="measure" ) { 
		sidebar.addClass( "control-sidebar-open" )
			.find( "*" )
			.children( "li.info" )
			.tab( "show" ); 

		panel.children().each( function() { 
			var child_id = $( this ).attr( "id" ); 
			var selector = "control-sidebar-info-tab"; 

			if ( child_id == selector ) { 
				$( this ).addClass( "active" ); 
			} else { 
				$( this ).removeClass( "active" ); 
			}
		} ); 

	}

	if ( class_name == "draw" ) { 
		addDrawInteraction( action_type, layer, source, fid.max ); 
	} else if ( class_name == "modify" ) { 
		addFeatureInteraction( action_type, layer2draw().layer );
	} else if ( class_name == "measure" ) { 
		addMeasureInteraction( action_type, source );
	} else if ( class_name == "position" ) {
		addPosition( action_type, layer, source, fid.max ); 
	}
}


function getLayerSource( name ) {
	var results; 
	map.getLayers().forEach( function( e ) {
		if ( e.get("name") == name ) {
			results = e.getSource(); ; 
		}
	} ); 

	return results; 
} /* getLayerSource() */



function getXtrmFid( obj ) {
	var results = { max: 0, min: 99999}; 
	var features = obj.getFeatures(); 

	if ( features.length > 0 ) {
		for ( var i = 0; i < features.length; i++ ) {
			fid = parseInt( features[i].getId() ); 
			if ( fid > results.max ) results.max = fid; 
			if ( fid < results.min ) results.min = fid; 
		}
	} 

	return results; 
}


/**
 * Update the info-panel geometry content
 * @params { feature } 
 * @return { --- }
 */
function updateMarkerPanel( feature ) { 

	var fid = feature.getId(); 
	$( ".box#feature-info tr#fid td#info" ).html( fid ); 

	var geom = validOutput( feature.getGeometry() ); 	
	$( ".box#feature-info tr#geom td#info" ).html( geom.value ); 

	var name = ( feature.get( "name" ) !== undefined && feature.get( "name" ) != "" ) ? feature.get( "name" ).replaceAll( "\\", "" ) : "E&#769tiquette";
	$( ".box#feature-info tr#label td#info" ).html( ( name !== undefined ) ? name.replaceAll("\\", "") : "E&#769tiquette" ); 

	var status = ( feature.get( "status" ) !== undefined ) ? feature.get( "status" ) : "De&#769faut";
	status = ( feature.get( "status" ) != "defaut" ) ? feature.get( "name" ) : feature.get( "status" );
	$( ".box#feature-info tr#group td#info" ).html( status.replaceAll("\\", "") ); 

	var hex = ( !feature.getProperties().featurecolor ) ? feature.getProperties().projectcolor : feature.getProperties().featurecolor;
  var rgba =  hex2rbga( hex, 0.2 ); 
  var yiq = getContrastYIQ( hex, "rgba" ); 
	var span = $( ".box#feature-info tr#style td#info span" ); 
  span.css( "background-color", hex ); 
  span.css( "color", yiq["fill"] ); 

} /* updateMarkerPanel() */


/**
 * Find output content by geometry
 * @param { ol.geometry } point, line, polygon
 * @return { value: "", title: "" }
 */
function validOutput( geom ) { 
	var output={ value: "", title: "" }; 

	if ( geom instanceof ol.geom.Polygon ) { 
		output.value = formatArea( geom );  
		output.title = "Superficie"; 
	} else if ( geom instanceof ol.geom.LineString ) { 
		output.value = formatLength( geom ); 
		output.title = "Distance"; 
	} else if ( geom instanceof ol.geom.Point ) { 
		output.value = formatPosition( geom );  
		output.title = "Position"; 
	} 

	return output; 
}


/**
 * format length output
 * @param { ol.geom.LineString } line
 * @return { string }
 */
function formatPosition( point ) { 
	var coord = point.getCoordinates(); 
	var projCoord = ol.proj.transform( coord, "EPSG:3857", "EPSG:4326" ); 			

	for ( i in projCoord ) { 
		projCoord[i] = Math.round( projCoord[i]*100 )/100; 
	}  
	var output = projCoord.join( ", " ); 

  return output;
};


/**
 * format length output
 * @param { ol.geom.LineString } line
 * @return { string }
 */
function formatLength( line ) { 
  var length = Math.round( line.getLength() * 100 ) / 100;
  var output;
  if ( length > 100 ) {  
    output = ( Math.round( length / 1000 * 100 ) / 100 ) +
        ' ' + 'km';
  } else { 
    output = ( Math.round( length * 100 ) / 100 ) +
        ' ' + 'm';
  }
  return output;
};


/**
 * format length output
 * @param { ol.geom.Polygon } polygon
 * @return { string }
 */
function formatArea( polygon ) { 
  var area = polygon.getArea();
  var output;
  if ( area > 10000 ) { 
    output = ( Math.round( area / 1000000 * 100 ) / 100 ) +
      ' ' + 'km<sup>2</sup>';
  } else { 
    output = ( Math.round( area * 100 ) / 100 ) +
    	' ' + 'm<sup>2</sup>';
  }
  return output;
}; 


/**
 * Location coordinates
 * @param { text string }
 */
function fn_search( location ) { 
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode( { 'address': location }, function( results, status ) { 
		if ( status == google.maps.GeocoderStatus.OK ) { 
				
			var coord = results[0].geometry.location; 
			var pos = ol.proj.transform( [coord.lng(), coord.lat()], 'EPSG:4326', 'EPSG:3857' ); 
			//map.getView().setCenter( pos ); 
			zoom2layer( pos, "coordinates" ); 

			//add icon to vector source
	   	var iconFeature = new ol.Feature( { 
				geometry: new ol.geom.Point( pos )  
			} );

	   	var source = getLayerSource( "geomarker" ); 
			source.clear();
			source.addFeature( iconFeature ); 

		} else { 
			console.log( ["Geocode was not successful for the following reason:", status].join( " " ) ); 
		}		
	} ); 
} 

/**
 * Delete the select feature
 * @param { text string }
 */
function deleteFeature( elem ) { 
	var fid = $( ".box#feature-info tr#fid td#info" ).html(); 
	var source = getLayerSource( layer2draw().layer ); 
	var feature = source.getFeatureById( fid ); 

	source.removeFeature( feature ); 
	sketch=null; 

	resetMarkerPanel(); 
	interaction.select.getFeatures().clear(); 
}

/**
 * Reset all the panel's information
 * @params { --- }
 * @return { --- }
 */
function resetMarkerPanel() { 
	$( ".box#feature-info tr#fid td#info" ).html( "Identifiant" ); 
	$( ".box#feature-info tr#geom td#info" ).html( "Ge&#769ome&#769trie" ); 
	$( ".box#feature-info tr#label td#info" ).html( "E&#769tiquette" ); 
	$( ".box#feature-info tr#group td#info" ).html( "De&#769faut" ); 

	var span = $( ".box#feature-info tr#style td#info span" );
	span.css( "background-color", "gray" ); 
  span.css( "color", "white" ); 

  $( ".check" ).fadeOut(); 
 	$( "button#delete-feature" ).prop( "disabled", true ); 
  sktech=null; 

} /* resetMarkerPanel() */


/**
 * Recieve file info from modal and create output file for download
 * @params { dict() }
 * @return { --- }
 */
function exportFeature( data ) { 
	map.getLayers().forEach( function( layer ) { 
		if ( layer.get( "name" ) == data.layer ) {

			function convert2format ( ext ) {
				return ( ext == "GeoJSON" ) ? new ol.format.GeoJSON() : 
					( ext == "GPX" ) ? new ol.format.GPX() : 
					( ext == "KML" ) ? new ol.format.KML() : 
					( ext == "WKT" ) ? new ol.format.WKT() :
					undefined; 
			}

			function exportContent ( data, obj ) {
				var encode = ( data.extension == "GeoJSON" ) ? JSON.stringify( obj ).replaceAll( "\'", "\&#39;" ) : 
					( data.extension == "GPX" ) ? obj.outerHTML : 
					( data.extension == "KML" ) ? obj.outerHTML :  
					null; 

				var status = "ready"; 
				var link = url.file( url.data(), "tmp/export" );  
				var myData = {}; 

				if ( data.method == "export" ) {
					myData = { data: encode, format: data.extension, link: link, file: data.filename.replaceAll( "\'", "" ), type: data.type, status: status, user: data.user, date: data.date, method: data.method, comments: JSON.stringify( { value: data.comments } ).replaceAll( "\'", "\&#39;" ) }; 
				} else if ( data.method == "update" ) {
					myData = { data: encode, file: data.filename.replaceAll( "\'", "" ), id: data.id, type: data.type, user: data.user, method: data.method, comments: JSON.stringify( { value: data.comments } ).replaceAll( "\'", "\&#39;" ) }; 
				}
				if ( type !== null ) submitValues( myData, "export.php" ); 
			}


			var source = layer.getSource(); 
			var features = source.getFeatures(); 
			var format = convert2format( data.extension ); 
			var obj = ( data.extension == "GeoJSON" ) ? format.writeFeaturesObject( features, { featureProjection: "EPSG:" + data.srs} ) :( data.extension != "GPX" ) ? format.writeFeatures( features, { featureProjection: "EPSG:" + data.srs} ) :
				format.writeFeatures( features ); 

			exportContent( data, obj ); 
		}

	} ); 

} /** exportFeature() */


/* 
 * Perform a random HEX color
 * @params { --- }
 * @return { STRING }
 */
function randomHEX() {
	var results = '#'+Math.floor(Math.random()*16777215).toString(16);

	return results; 
} /* randomRGBA() */ 


/* 
 * Convert a HEX color to a RGB color
 * @params { STRING, FLOAT }
 * @return { STRING }
 */
function hex2rbga( hex, opacity ) {
  if (hex.lastIndexOf( '#' ) > -1 ) {
      hex = hex.replace( /#/, '0x' );
  } else {
      hex = '0x' + hex;
  }
  var r = ( hex >> 16 ).toString();
  var g = ( ( hex & 0x00FF00 ) >> 8 ).toString();
  var b = ( hex & 0x0000FF ).toString(); 
  var a = opacity.toString(); 

  return "rgba(" + [ r, g, b, a ].join( ", " ) + ")";
} /* hex2rgb() */


/* 
 * Convert a RGB (RGBA) color to a HEX color
 * @params { STRING }
 * @return { STRING }
 */
function rgb2hex( rgb ){
 rgb = rgb.match( /^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i );
 return ( rgb && rgb.length === 4 ) ? "#" +
  ( "0" + parseInt( rgb[1], 10 ).toString( 16 ) ).slice( -2 ) +
  ( "0" + parseInt( rgb[2], 10 ).toString( 16 ) ).slice( -2 ) +
  ( "0" + parseInt( rgb[3], 10 ).toString( 16 ) ).slice( -2 ) : '';
} /* rgb2hex() */


/* 
 * Get the contrast level of a HEX color
 * @params { STRING }
 * @return { STRING }
 */
function getContrastYIQ( hexcolor, state ){
	var results = {}; 
	var r = parseInt( hexcolor.substr( 0,2 ), 16 );
	var g = parseInt( hexcolor.substr( 2,2 ), 16 );
	var b = parseInt( hexcolor.substr( 4,2 ), 16 );
	var yiq = ( ( r*299 ) + ( g*587 ) + ( b*114 ) ) / 1000;

	results["fill"] = ( yiq >= 128) ? ( ( state == "text" ) ? "black" : "#000000" ) : ( ( state == "text" ) ? "white" : "#ffffff" );
	results["stroke"] = ( yiq >= 128) ? ( ( state == "text" ) ? "white" : "#ffffff" ) : ( ( state == "text" ) ? "black" : "#000000" );

	return results; 
} /* getContrastYIQ() */